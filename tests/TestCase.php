<?php

namespace Tests;

use Orchestra\Testbench;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider;

abstract class TestCase extends Testbench\TestCase
{
    protected function getEnvironmentSetup($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);

        $app['config']->set('cloudinary.cloud_url', env('CLOUDINARY_URL'));
    }

    protected function getPackageProviders($app)
    {
        return CloudinaryServiceProvider::class;
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/fixtures/migrations');
    }
}
