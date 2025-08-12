<?php

namespace Boy132\MclogsUploader;

use Filament\Contracts\Plugin;
use Filament\Panel;

class MclogsUploaderPlugin implements Plugin
{
    public function getId(): string
    {
        return 'mclogs-uploader';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
