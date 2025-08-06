<?php

namespace Boy132\Announcements\Providers;

use App\Models\Role;
use Illuminate\Support\ServiceProvider;

class AnnouncementsPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Role::registerCustomDefaultPermissions('announcement');
    }

    public function boot(): void {}
}
