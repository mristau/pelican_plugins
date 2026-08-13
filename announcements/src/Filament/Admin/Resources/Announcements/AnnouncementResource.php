<?php

namespace Boy132\Announcements\Filament\Admin\Resources\Announcements;

use App\Enums\TablerIcon;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use App\Livewire\AlertBanner;
use Boy132\Announcements\Filament\Admin\Resources\Announcements\Pages\ManageAnnouncements;
use Boy132\Announcements\Models\Announcement;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-speakerphone';

    public static function getNavigationLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(trans('announcements::strings.title')),
                TextColumn::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body')),
                TextColumn::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->color(fn ($state) => $state)
                    ->badge(),
                IconColumn::make('icon')
                    ->label(trans('announcements::strings.icon'))
                    ->placeholder(trans('announcements::strings.default_icon'))
                    ->icon(fn ($state) => $state)
                    ->color(fn (Announcement $announcement) => $announcement->type),
                TextColumn::make('url_label')
                    ->label(trans('announcements::strings.url'))
                    ->placeholder(trans('announcements::strings.no_url'))
                    ->url(fn (Announcement $announcement) => $announcement->url_link, true),
                TextColumn::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->placeholder(trans('announcements::strings.all_panels'))
                    ->badge(),
                DateTimeColumn::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from')),
                DateTimeColumn::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::getEditAuthorizationResponse($record)->allowed()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false),
            ])
            ->emptyStateIcon('tabler-speakerphone')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('announcements::strings.no_announcements'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(trans('announcements::strings.preview'))
                    ->columns(1)
                    ->columnSpanFull()
                    ->contained(false)
                    ->schema(function (Get $get) {
                        $actions = [];

                        $urlLabel = $get('url_label');
                        $urlLink = $get('url_link');
                        if ($urlLabel && $urlLink) {
                            $actions = [
                                Announcement::getUrlAction($urlLabel, $urlLink)
                                    ->defaultView(Action::LINK_VIEW)
                                    ->defaultSize(Size::Small)
                                    ->toArray(),
                            ];
                        }

                        return [
                            AlertBanner::fromArray([
                                'id' => 'announcement_preview',
                                'title' => $get('title') ?? trans_choice('announcements::strings.announcement', 1),
                                'body' => $get('body'),
                                'status' => $get('type') ?? 'info',
                                'icon' => $get('icon'),
                                'closeable' => false,
                                'actions' => $actions,
                            ]),
                        ];
                    }),
                TextInput::make('title')
                    ->label(trans('announcements::strings.title'))
                    ->required()
                    ->columnSpanFull()
                    ->debounce(),
                TextInput::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body'))
                    ->nullable()
                    ->columnSpanFull()
                    ->debounce(),
                Select::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->selectablePlaceholder(false)
                    ->default('info')
                    ->prefixIcon(TablerIcon::CircleFilled)
                    ->prefixIconColor(fn ($state) => $state)
                    ->options([
                        'info' => 'Info',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'danger' => 'Danger',
                    ])
                    ->debounce(),
                Select::make('icon')
                    ->label(trans('announcements::strings.icon'))
                    ->placeholder(trans('announcements::strings.default_icon'))
                    ->nullable()
                    ->searchable()
                    ->options(TablerIcon::class)
                    ->prefixIcon(fn ($state) => $state)
                    ->debounce(),
                TextInput::make('url_label')
                    ->label(trans('announcements::strings.url_label'))
                    ->placeholder(trans('announcements::strings.no_url'))
                    ->requiredWith('url_link')
                    ->debounce(),
                TextInput::make('url_link')
                    ->label(trans('announcements::strings.url_link'))
                    ->placeholder(trans('announcements::strings.no_url'))
                    ->requiredWith('url_label')
                    ->url()
                    ->debounce(),
                Select::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->placeholder(trans('announcements::strings.all_panels'))
                    ->columnSpanFull()
                    ->multiple()
                    ->options([
                        'admin' => 'Admin Area',
                        'server' => 'Client Area',
                        'app' => 'Server List',
                    ]),
                DateTimePicker::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from'))
                    ->nullable()
                    ->native(false)
                    ->timezone(user()->timezone),
                DateTimePicker::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to'))
                    ->nullable()
                    ->native(false)
                    ->timezone(user()->timezone),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label(trans('announcements::strings.title'))
                    ->columnSpanFull(),
                TextEntry::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body'))
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->color(fn ($state) => $state)
                    ->badge(),
                IconEntry::make('icon')
                    ->label(trans('announcements::strings.icon'))
                    ->placeholder(trans('announcements::strings.default_icon'))
                    ->color(fn (Announcement $announcement) => $announcement->type),
                TextEntry::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->placeholder(trans('announcements::strings.all_panels'))
                    ->badge(),
                TextEntry::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from')),
                TextEntry::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncements::route('/'),
        ];
    }
}
