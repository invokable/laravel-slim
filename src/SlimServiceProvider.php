<?php

declare(strict_types=1);

namespace Revolution\Slim;

use Illuminate\Support\ServiceProvider;
use Revolution\Slim\Console\SlimConsoleCommand;

class SlimServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SlimConsoleCommand::class,
            ]);
        }
    }
}
