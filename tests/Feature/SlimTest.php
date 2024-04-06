<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SlimTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $path = __DIR__.'/../skeleton/laravel_test';

        $this->app->setBasePath($path);

        File::deleteDirectory($path);
        File::copyDirectory(__DIR__.'/../skeleton/laravel11', $path);
    }

    public function test_slim_console_failed()
    {
        File::delete(base_path('routes/web.php'));

        $this->artisan('slim:console')
            ->assertFailed()
            ->expectsOutput('Must run on new project');

        $this->assertFileExists(public_path());
        $this->assertFileExists(resource_path());
    }

    public function test_slim_console()
    {
        $this->artisan('slim:console')
            ->assertSuccessful()
            ->expectsOutput('Delete routes/web.php')
            ->expectsOutput('Set up successfully.');

        $this->assertFileDoesNotExist(public_path());
        $this->assertFileDoesNotExist(resource_path());
        $this->assertFileDoesNotExist(base_path('routes/web.php'));
    }
}
