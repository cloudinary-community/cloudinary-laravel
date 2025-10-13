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
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\ReactCommand::class,
                Console\VueCommand::class,
                Console\SvelteCommand::class,
            ]);
        }

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

            $adapter = new CloudinaryStorageAdapter($cloudinary, null, $config['prefix'] ?? null);

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        $this->loadViewsFrom(__DIR__.'/../views', 'cloudinary');

        $this->publishes([
            __DIR__.'/../config/cloudinary.php' => config_path('cloudinary.php'),
        ], 'cloudinary-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cloudinary.php', 'cloudinary');

        $this->app->singleton(Cloudinary::class, function ($app) {
            $config = $app['config']->get('filesystems.disks.cloudinary');

            if (isset($config['url'])) {
                return new Cloudinary($config['url']);
            }

            return new Cloudinary([
                'cloud' => [
                    'cloud_name' => $config['cloud'],
                    'api_key' => $config['key'],
                    'api_secret' => $config['secret'],
                ],
                'url' => [
                    'secure' => $config['secure'] ?? false,
                ],
            ]);
        });
    }
}
