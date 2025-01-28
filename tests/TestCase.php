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
    }

    protected function getPackageProviders($app): array
    {
        return [CloudinaryServiceProvider::class];
    }
}
