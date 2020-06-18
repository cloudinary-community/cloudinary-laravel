<?php

namespace Unicodeveloper\Cloudinary;

use League\Flysystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use Unicodeveloper\Cloudinary\CloudinaryAdapter;
use Unicodeveloper\Cloudinary\Commands\BackupFilesCommand;
use Unicodeveloper\Cloudinary\Commands\UploadFileCommand;
use Unicodeveloper\Cloudinary\Commands\FetchFilesCommand;
use Unicodeveloper\Cloudinary\Commands\RenameFilesCommand;
use Unicodeveloper\Cloudinary\Commands\DeleteFilesCommand;
use Unicodeveloper\Cloudinary\Commands\GenerateArchiveCommand;
use Unicodeveloper\Cloudinary\Commands\GenerateZipCommand;


class CloudinaryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootMacros();
        $this->bootResources();
        $this->bootMigrations();
        $this->bootDirectives();
        $this->bootComponents();
        $this->bootCommands();
        $this->bootPublishing();
        $this->bootCloudinaryDriver();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Register the service the package provides.
        $this->app->singleton(CloudinaryEngine::class, function ($app) {
            return new CloudinaryEngine;
        });
    }


    protected function bootCommands()
    {
        /**
        * Register Laravel Cloudinary Artisan commands
        */
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupFilesCommand::class,
                UploadFileCommand::class,
                FetchFilesCommand::class,
                RenameFilesCommand::class,
                GenerateArchiveCommand::class,
                GenerateZipCommand::class,
                DeleteFilesCommand::class
            ]);
        }
    }

    /**
     * Boot the package's publishable resources.
     *
     * @return void
     */
    protected function bootPublishing()
    {
        if ($this->app->runningInConsole()) {
            $config = realpath(__DIR__.'/../config/cloudinary.php');

            $this->publishes([
                $config => config_path('cloudinary.php')
            ]);
        }
    }

    /**
     * Boot the package resources.
     *
     * @return void
     */
    protected function bootResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cloudinary');
    }

    /**
     * Boot the package migrations.
     *
     * @return void
     */
    protected function bootMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }



    /**
     * Boot the package directives.
     *
     * @return void
     */
    protected function bootDirectives()
    {
        Blade::directive('cloudinaryJS', function() {
            return "<?php echo view('cloudinary::js'); ?>";
        });
    }

    /**
     * Boot the package components.
     *
     * @return void
     */
    protected function bootComponents()
    {
        Blade::component('cloudinary::components.button', 'upload-button');
    }

    /**
     * Boot the package macros that extends Laravel Uploaded File API.
     *
     * @return void
     */
    protected function bootMacros()
    {
        $engine = new CloudinaryEngine;

        UploadedFile::macro('storeOnCloudinary', function ($folder = null) use ($engine) {
            return $engine->uploadFile($this->getRealPath(), ['folder' => $folder]);
        });

        UploadedFile::macro('storeOnCloudinaryAs', function ($folder = null, $publicId = null) use ($engine) {
            return $engine->uploadFile($this->getRealPath(), ['folder' => $folder, 'public_id' => $publicId]);
        });
    }

    protected function bootCloudinaryDriver()
    {
        $this->app['config']['filesystems.disks.cloudinary'] = ['driver' => 'cloudinary'];

        Storage::extend('cloudinary', function ($app, $config) {
            return new Filesystem(new CloudinaryAdapter(config('cloudinary.cloud_url')));
        });
    }
}
