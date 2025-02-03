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

        $app['config']->set('filesystems.disks.cloudinary', [
            'driver' => 'cloudinary',
            'key' => env('CLOUDINARY_KEY'),
            'secret' => env('CLOUDINARY_SECRET'),
            'cloud' => env('CLOUDINARY_CLOUD_NAME'),
            'url' => env('CLOUDINARY_URL'),
            'secure' => (bool) env('CLOUDINARY_SECURE', false),
        ]);

        $app['config']->set('filesystems.default', 'cloudinary');
    }

    protected function getPackageProviders($app): array
    {
        return [CloudinaryServiceProvider::class];
    }
}
