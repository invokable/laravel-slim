<?php

declare(strict_types=1);

namespace Revolution\Slim\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Revolution\Slim\Console\Concerns\WithCheck;
use Revolution\Slim\Console\Concerns\WithDelete;

class SlimConsoleCommand extends Command
{
    use WithCheck;
    use WithDelete;

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
    protected $description = 'Set up for console only project';

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
            base_path('routes/web.php'),
            base_path('package.json'),
            base_path('package-lock.json'),
            base_path('vite.config.js'),
            base_path('postcss.config.js'),
            base_path('tailwind.config.js'),

            // config
            config_path('auth.php'),
            config_path('cache.php'),
            config_path('database.php'),
            config_path('filesystems.php'),
            config_path('logging.php'),
            config_path('mail.php'),
            config_path('queue.php'),
            config_path('services.php'),
            config_path('session.php'),
        ])->each(fn (string $path) => $this->delete($path));

        $this->replaceBootstrap();
        $this->replaceExampleTest();

        $this->info('Set up successfully.');

        return 0;
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
            path: app()->bootstrapPath('app.php'),
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
            path: base_path('tests/Feature/ExampleTest.php'),
        );
    }
}
