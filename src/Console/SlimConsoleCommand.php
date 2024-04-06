<?php

declare(strict_types=1);

namespace Revolution\Slim\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SlimConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slim:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unnecessary files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (! $this->check()) {
            $this->error('Must run on new project');

            return 1;
        }

        collect([
            // directory
            app_path('Http'),
            app_path('Models'),
            app()->databasePath(),
            public_path(),
            resource_path(),
            base_path('node_modules'),

            // file
            config_path('auth.php'),
            config_path('database.php'),
            config_path('mail.php'),
            config_path('queue.php'),
            config_path('session.php'),
            base_path('routes/web.php'),
            base_path('package.json'),
            base_path('vite.config.js'),
        ])->each(fn (string $path) => $this->delete($path));

        $this->replaceBootstrap();
        $this->replaceExampleTest();

        $this->info('Set up successfully.');

        return 0;
    }

    protected function check(): bool
    {
        if (File::missing(app()->bootstrapPath('providers.php'))) {
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

    protected function delete(string $path): void
    {
        if (File::missing($path)) {
            return;
        }

        $this->line('<fg=gray>Deleted</> '.Str::remove(base_path().'/', $path));

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        } else {
            File::delete($path);
        }
    }

    protected function replaceBootstrap(): void
    {
        $this->line('<fg=gray>Replace</> bootstrap/app.php');

        File::replaceInFile(
            search: [
                'use Illuminate\Foundation\Configuration\Middleware;'.PHP_EOL,
                "        web: __DIR__.'/../routes/web.php',".PHP_EOL,
                "        health: '/up',".PHP_EOL,
                '    ->withMiddleware(function (Middleware $middleware) {
        //
    })'.PHP_EOL,
            ],
            replace: '',
            path: app()->bootstrapPath('app.php')
        );
    }

    protected function replaceExampleTest(): void
    {
        $this->line('<fg=gray>Replace</> tests/Feature/ExampleTest.php');

        File::replaceInFile(
            search: [
                "get('/')",
                'assertStatus(200)',
            ],
            replace: [
                "artisan('inspire')",
                'assertOk()',
            ],
            path: base_path('tests/Feature/ExampleTest.php')
        );
    }
}
