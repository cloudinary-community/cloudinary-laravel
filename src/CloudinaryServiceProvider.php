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
            if (isset($config['url'])) {
                $cloudinary = new Cloudinary($config['url']);
            } else {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $config['cloud'],
                        'api_key' => $config['key'],
                        'api_secret' => $config['secret'],
                    ],
                    'url' => [
                        'secure' => $config['secure'] ?? false,
                    ],
                ]);
            }

            $adapter = new CloudinaryStorageAdapter($cloudinary);

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
