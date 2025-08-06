<?php

namespace Boy132\Announcements;

use App\Livewire\AlertBanner;
use Boy132\Announcements\Models\Announcement;
use Filament\Contracts\Plugin;
use Filament\Panel;

class AnnouncementsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'announcements';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\Announcements\\Filament\\$id\\Resources");
    }

    public function boot(Panel $panel): void
    {
        foreach (Announcement::all() as $announcement) {
            if (!$announcement->shouldDisplay($panel)) {
                continue;
            }

            AlertBanner::make('announcement_' . $announcement->id)
                ->title($announcement->title)
                ->body($announcement->body)
                ->status($announcement->type)
                ->send();
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
