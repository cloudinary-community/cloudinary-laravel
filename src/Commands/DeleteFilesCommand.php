<?php

namespace CloudinaryLabs\CloudinaryLaravel\Commands;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Exception;
use Illuminate\Console\Command;

/**
 * Class DeleteFilesCommand
 */
class DeleteFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:delete {publicId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an asset on Cloudinary';

    /**
     * Execute the console command.
     */
    public function handle(CloudinaryEngine $engine): void
    {
        if (! config('cloudinary.cloud_url')) {
            $this->warn('Please ensure your Cloudinary credentials are set before continuing.');

            return;
        }

        $publicId = $this->argument('publicId');

        $this->info("About to delete $publicId file on Cloudinary...");

        try {
            $engine->destroy($publicId);

            $this->info('File deleted!');
        } catch (Exception $exception) {
            $this->warn("Deletion of files on Cloudinary failed because: {$exception->getMessage()}.");
        }
    }
}
