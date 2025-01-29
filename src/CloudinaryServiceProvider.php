<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Cloudinary;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class CloudinaryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['filesystem']->extend('cloudinary', function ($app, $config) {
            $adapter = new CloudinaryStorageAdapter($config);

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });
    }

    public function register(): void
    {
        $this->app->singleton(Cloudinary::class, function ($app) {
            return new Cloudinary($app['config']->get('cloudinary'));
        });
    }
}
