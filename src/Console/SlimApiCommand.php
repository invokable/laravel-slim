<?php

declare(strict_types=1);

namespace Revolution\Slim\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Revolution\Slim\Console\Concerns\WithCheck;
use Revolution\Slim\Console\Concerns\WithDelete;

class SlimApiCommand extends Command
{
    use WithCheck;
    use WithDelete;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slim:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up for api only project';

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

        $this->call('install:api', ['--without-migration-prompt' => true]);
        $this->call('migrate');

        collect([
            // directory
            resource_path(),
            base_path('node_modules'),

            // file
            base_path('routes/web.php'),
            base_path('package.json'),
            base_path('package-lock.json'),
            base_path('vite.config.js'),
        ])->each(fn (string $path) => $this->delete($path));

        $this->replaceUser();
        $this->replaceBootstrap();
        $this->replaceExampleTest();
        $this->auth();

        $this->info('Set up successfully.');

        return 0;
    }

    protected function replaceUser(): void
    {
        $this->line('<fg=gray>Replace</> app/Models/User.php');

        File::replaceInFile(
            search: [
                'use Illuminate\Notifications\Notifiable;'.PHP_EOL,
                'use HasFactory, Notifiable;'.PHP_EOL,
            ],
            replace: [
                'use Illuminate\Notifications\Notifiable;'.PHP_EOL.'use Laravel\Sanctum\HasApiTokens;'.PHP_EOL,
                'use HasApiTokens, HasFactory, Notifiable;'.PHP_EOL,
            ],
            path: app()->path('Models/User.php'),
        );
    }

    protected function replaceBootstrap(): void
    {
        $this->line('<fg=gray>Replace</> bootstrap/app.php');

        File::replaceInFile(
            search: [
                "        web: __DIR__.'/../routes/web.php',".PHP_EOL,
            ],
            replace: '',
            path: app()->bootstrapPath('app.php'),
        );
    }

    protected function replaceExampleTest(): void
    {
        $this->line('<fg=gray>Replace</> tests/Feature/ExampleTest.php');

        if (File::exists(base_path('tests/Pest.php'))) {
            $this->warn('SKIP : Using Pest');

            return;
        }

        File::replace(
            path: base_path('tests/Feature/ExampleTest.php'),
            content: File::get(__DIR__.'/stubs/api/ExampleTest.php'),
        );
    }

    protected function auth(): void
    {
        File::copy(__DIR__.'/stubs/api/auth.php', base_path('routes/auth.php'));

        File::append(
            path: base_path('routes/api.php'),
            data: PHP_EOL."require __DIR__.'/auth.php';".PHP_EOL,
        );
    }
}
