<?php

namespace Unicodeveloper\Cloudinary\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Unicodeveloper\Cloudinary\CloudinaryEngine;

class DeleteFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "cloudinary:delete {publicId}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an asset on Cloudinary';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(CloudinaryEngine $engine)
    {
        if(! config('cloudinary.cloud_url')) {
            $this->warn('Please ensure your Cloudinary credentials are set before continuing.');

            return;
        }

        $publicId = $this->argument('publicId');

        $this->info("About to delete {$publicId} file on Cloudinary...");

        try {

            $engine->destroy($publicId);

            $this->info('File deleted!');
        } catch (Exception $exception) {
            $this->warn("Deletion of files on Cloudinary failed because: {$exception->getMessage()}.");
        }
    }
}