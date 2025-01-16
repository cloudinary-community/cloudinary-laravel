<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use CloudinaryLabs\CloudinaryLaravel\Commands\BackupFilesCommand;
use CloudinaryLabs\CloudinaryLaravel\Commands\DeleteFilesCommand;
use CloudinaryLabs\CloudinaryLaravel\Commands\FetchFilesCommand;
use CloudinaryLabs\CloudinaryLaravel\Commands\GenerateArchiveCommand;
use CloudinaryLabs\CloudinaryLaravel\Commands\RenameFilesCommand;
use CloudinaryLabs\CloudinaryLaravel\Commands\UploadFileCommand;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

/**
 * Class CloudinaryServiceProvider
 */
class CloudinaryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->bootMacros();
        $this->bootResources();
        $this->bootDirectives();
        $this->bootComponents();
        $this->bootCommands();
        $this->bootPublishing();
        $this->bootCloudinaryDriver();
        $this->bootRoutes();
    }

    /**
     * Boot the package macros that extends Laravel Uploaded File API.
     */
    protected function bootMacros(): void
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
     */
    protected function bootResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cloudinary');
    }

    /**
     * Boot the package directives.
     */
    protected function bootDirectives(): void
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
     */
    protected function bootComponents(): void
    {
        Blade::component('cloudinary::components.button', $this->getComponentName('cld-upload-button'));
        Blade::component('cloudinary::components.image', $this->getComponentName('cld-image'));
        Blade::component('cloudinary::components.video', $this->getComponentName('cld-video'));
    }

    protected function getComponentName($componentName)
    {
        $version = explode('.', $this->app->version());
        if ((int) $version[0] <= 6) {
            $componentName = str_replace('-', '_', $componentName);
        }

        return $componentName;
    }

    protected function bootCommands(): void
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
                    DeleteFilesCommand::class,
                ]
            );
        }
    }

    /**
     * Boot the package's publishable resources.
     */
    protected function bootPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $config = dirname(__DIR__).'/config/cloudinary.php';

            $this->publishes(
                [
                    $config => $this->app->configPath('cloudinary.php'),
                ],
                'cloudinary-laravel-config'
            );

            $this->publishes(
                [
                    __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
                ],
                'cloudinary-laravel-migration'
            );
        }
    }

    protected function bootCloudinaryDriver(): void
    {
        Storage::extend('cloudinary', function ($app, $config) {

            $cloudinaryAdapter = new CloudinaryAdapter(config('cloudinary.cloud_url'));

            return new FilesystemAdapter(
                new Filesystem($cloudinaryAdapter, $config),
                $cloudinaryAdapter,
                $config
            );
        });
    }

    /**
     * Boot the package routes.
     */
    protected function bootRoutes(): void
    {
        if (config('cloudinary.upload_route')) {
            Route::post(config('cloudinary.upload_route'), config('cloudinary.upload_action'));
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        // Register the service the package provides.
        $this->app->singleton(
            CloudinaryEngine::class,
            function () {
                return new CloudinaryEngine;
            }
        );
    }
}
