<?php

namespace CloudinaryLabs\CloudinaryLaravel\Commands;

use Exception;
use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;


/**
 * Class UploadFileCommand
 * @package CloudinaryLabs\CloudinaryLaravel\Commands
 */
class UploadFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "cloudinary:upload {remote-url}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload an existing remote file on the Internet straight to Cloudinary';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(CloudinaryEngine $engine)
    {
        if (!config('cloudinary.cloud_url')) {
            $this->warn('Please ensure your Cloudinary credentials are set before continuing.');

            return;
        }

        if (is_numeric($this->argument('remote-url'))) {
            $this->warn('This is a number, not a valid remote file url. Please try again with a valid URL.');

            return;
        }

        if (!filter_var($this->argument('remote-url'), FILTER_VALIDATE_URL)) {
            $this->warn('Please add a valid remote file url as an argument.');

            return;
        }

        $remoteUrl = $this->argument('remote-url');

        $this->info('Extracting remote file...');

        try {
            $engine->uploadFile($remoteUrl);
            $this->info('Uploading in progress...');

            $this->info('Upload to Cloudinary completed!');
        } catch (Exception $exception) {
            $this->warn("Backup of files to Cloudinary failed because: {$exception->getMessage()}.");
        }
    }
}
