<?php

namespace Tests;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench;

abstract class TestCase extends Testbench\TestCase
{
    protected function getEnvironmentSetup($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('cloudinary.cloud_url', env('CLOUDINARY_URL', 'cloudinary://foo:bar@baz'));
        $app['config']->set('filesystems.disks.cloudinary', ['driver' => 'cloudinary']);
    }

    protected function getPackageProviders($app)
    {
        return CloudinaryServiceProvider::class;
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/migrations');
    }
}
