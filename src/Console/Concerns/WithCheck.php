<?php

namespace Revolution\Slim\Console\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait WithCheck
{
    protected function check(): bool
    {
        if (File::missing(app()->getBootstrapProvidersPath())) {
            return false;
        }

        if (! Str::contains($bootstrap = File::get(app()->bootstrapPath('app.php')), 'return Application::configure')) {
            return false;
        }

        // install:api
        if (Str::contains($bootstrap, '/routes/api.php')) {
            return false;
        }

        // breeze api sanctum
        if (Str::contains($bootstrap, 'EnsureFrontendRequestsAreStateful::class')) {
            return false;
        }

        // breeze inertia
        if (Str::contains($bootstrap, 'HandleInertiaRequests::class')) {
            return false;
        }

        if (File::exists(app_path('Providers/AuthServiceProvider.php'))) {
            return false;
        }

        if (File::missing(base_path('routes/web.php'))) {
            return false;
        }

        if (File::exists(base_path('routes/api.php'))) {
            return false;
        }

        // install:broadcasting
        if (File::exists(base_path('routes/channels.php'))) {
            return false;
        }

        // jetstream
        if (File::exists(config_path('jetstream.php'))) {
            return false;
        }

        if (File::missing(app()->databasePath())) {
            return false;
        }

        if (File::missing(public_path())) {
            return false;
        }

        if (File::missing(resource_path())) {
            return false;
        }

        return true;
    }
}
