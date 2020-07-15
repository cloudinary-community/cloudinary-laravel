<?php

namespace CloudinaryLabs\CloudinaryLaravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

/**
 * Class BackupFilesCommand
 * @package CloudinaryLabs\CloudinaryLaravel\Commands
 */
class BackupFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "cloudinary:backup
        {--location= : The name of the folder in your app's storage/app directory}
        {--folder= : The name of the folder where all the backed up files will be stored on Cloudinary}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up all your existing assets to Cloudinary';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(CloudinaryEngine $engine)
    {
        $files = $this->getFiles();
        $folder = null;

        if (!$files) {
            $this->warn(
                'There are no files in the storage/app/public directory. Use --location flag to specify the name of the directory (if there are files in there) within the storage/app directory.'
            );

            return;
        }

        if (!config('cloudinary.cloud_url')) {
            $this->warn('Please ensure your Cloudinary credentials are set before continuing.');

            return;
        }

        if ($this->option('folder') && is_string($this->option('folder'))) {
            $folder = $this->option('folder');
        }

        if ($this->option('location') && is_string($this->option('location'))) {
            $files = $this->getFiles($this->option('location'));
        }

        $this->info('Starting backup to Cloudinary...');

        try {
            foreach ($files as $file) {
                $engine->uploadFile($file->getRealPath(), $folder ? ['folder' => $folder] : []);
                $this->info('Uploading in progress...');
            }

            $this->info('Backup to Cloudinary completed!');
        } catch (Exception $exception) {
            $this->warn("Backup of files to Cloudinary failed because: {$exception->getMessage()}.");
        }
    }

    public function getFiles($location = 'public')
    {
        return File::allFiles(storage_path("app/{$location}"));
    }
}
