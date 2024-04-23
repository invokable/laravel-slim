<?php

namespace Revolution\Slim\Console\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait WithDelete
{
    protected function delete(string $path): void
    {
        if (File::missing($path)) {
            return;
        }

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        } else {
            File::delete($path);
        }

        $this->line('<fg=gray>Deleted</> '.Str::remove(base_path().'/', $path));
    }
}
