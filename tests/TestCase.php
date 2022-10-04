<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //    protected $loadEnvironmentVariables = true;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     *
     * @param  Application  $app
     *
     * @return array<int, string>
     */
    protected function getPackageProviders($app)
    {
        return [CloudinaryServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . "/..");
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        $app["config"]->set("database.default", "testing");
        $app["config"]->set("cloudinary.cloud_url", env("CLOUDINARY_URL"));
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");
    }
}
