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

        File::deleteDirectory(app_path('Http'));
        File::deleteDirectory(app_path('Models'));
        File::deleteDirectory(base_path('database'));
        File::deleteDirectory(public_path());
        File::deleteDirectory(resource_path());
        File::deleteDirectory(base_path('node_modules'));

        File::delete(config_path('auth.php'));
        File::delete(config_path('database.php'));
        File::delete(config_path('mail.php'));
        File::delete(config_path('queue.php'));
        File::delete(config_path('session.php'));

        File::delete(base_path('routes/web.php'));
        File::delete(base_path('package.json'));
        File::delete(base_path('vite.config.js'));

        $this->replaceBootstrap();
        $this->replaceExampleTest();

        $this->info('Set up successfully.');

        return 0;
    }

    protected function check(): bool
    {
        if (! File::exists(base_path('bootstrap/providers.php'))) {
            return false;
        }

        if (! Str::contains(File::get(base_path('bootstrap/app.php')), 'return Application::configure')) {
            return false;
        }

        if (File::exists(app_path('Providers/AuthServiceProvider.php'))) {
            return false;
        }

        if (! File::exists(base_path('routes/web.php'))) {
            return false;
        }

        if (! File::exists(base_path('database'))) {
            return false;
        }

        if (! File::exists(public_path())) {
            return false;
        }

        if (! File::exists(resource_path())) {
            return false;
        }

        return true;
    }

    protected function replaceBootstrap(): void
    {
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
            path: base_path('bootstrap/app.php')
        );
    }

    protected function replaceExampleTest(): void
    {
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
