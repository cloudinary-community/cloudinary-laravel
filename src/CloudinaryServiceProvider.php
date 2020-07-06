<?php

namespace Unicodeveloper\Cloudinary;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Unicodeveloper\Cloudinary\Commands\BackupFilesCommand;
use Unicodeveloper\Cloudinary\Commands\DeleteFilesCommand;
use Unicodeveloper\Cloudinary\Commands\FetchFilesCommand;
use Unicodeveloper\Cloudinary\Commands\GenerateArchiveCommand;
use Unicodeveloper\Cloudinary\Commands\RenameFilesCommand;
use Unicodeveloper\Cloudinary\Commands\UploadFileCommand;


/**
 * Class CloudinaryServiceProvider
 * @package Unicodeveloper\Cloudinary
 */
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
     * Boot the package macros that extends Laravel Uploaded File API.
     *
     * @return void
     */
    protected function bootMacros()
    {
        UploadedFile::macro(
            'storeOnCloudinary',
            function ($folder = null) {
                return resolve(CloudinaryEngine::class)->uploadFile($this->getRealPath(), ['folder' => $folder]);
            }
        );

        UploadedFile::macro(
            'storeOnCloudinaryAs',
            function ($folder = null, $publicId = null) {
                return resolve(CloudinaryEngine::class)->uploadFile(
                    $this->getRealPath(),
                    ['folder' => $folder, 'public_id' => $publicId]
                );
            }
        );
    }

    /**
     * Boot the package resources.
     *
     * @return void
     */
    protected function bootResources()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'cloudinary');
    }

    /**
     * Boot the package migrations.
     *
     * @return void
     */
    protected function bootMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Boot the package directives.
     *
     * @return void
     */
    protected function bootDirectives()
    {
        Blade::directive(
            'cloudinaryJS',
            function () {
                return "<?php echo view('cloudinary::js'); ?>";
            }
        );
    }

    /**
     * Boot the package components.
     *
     * @return void
     */
    protected function bootComponents()
    {
        Blade::component('cloudinary::components.button', 'cld-upload-button');
        Blade::component('cloudinary::components.image', 'cld-image');
        Blade::component('cloudinary::components.video', 'cld-video');
    }

    protected function bootCommands()
    {
        /**
         * Register Laravel Cloudinary Artisan commands
         */
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    BackupFilesCommand::class,
                    UploadFileCommand::class,
                    FetchFilesCommand::class,
                    RenameFilesCommand::class,
                    GenerateArchiveCommand::class,
                    DeleteFilesCommand::class
                ]
            );
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
            $config = dirname(__DIR__) . '/config/cloudinary.php';

            $this->publishes(
                [
                    $config => config_path('cloudinary.php')
                ]
            );
        }
    }

    protected function bootCloudinaryDriver()
    {
        $this->app['config']['filesystems.disks.cloudinary'] = ['driver' => 'cloudinary'];

        Storage::extend(
            'cloudinary',
            function ($app, $config) {
                return new Filesystem(new CloudinaryAdapter(config('cloudinary.cloud_url')));
            }
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Register the service the package provides.
        $this->app->singleton(
            CloudinaryEngine::class,
            function ($app) {
                return new CloudinaryEngine();
            }
        );
    }
}
