<?php

namespace CloudinaryLabs\CloudinaryLaravel\Commands;

use Exception;
use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

/**
 * Class GenerateArchiveCommand
 * @package CloudinaryLabs\CloudinaryLaravel\Commands
 */
class GenerateArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "cloudinary:archive
        {--tags=* : The tags of the assets you want included in the arhive}
        {--public_ids=* : The public IDs of the assets you want included in the archive}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an archive on Cloudinary';

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

        if (!$this->option('tags') && !$this->option('public_ids')) {
            $this->warn(
                'Please ensure you pass in at least a tag with --tags, or at least a public_id  with --public_ids'
            );

            return;
        }

        $this->info('Generating Archive...');

        try {
            $response = $engine->createArchive(
                [
                    'tags' => $this->option('tags') ?? null,
                    'public_ids' => $this->option('public_ids') ?? null
                ]
            )['secure_url'];

            $this->info("Archive: {$response}");
        } catch (Exception $exception) {
            $this->warn("Backup of files to Cloudinary failed because: {$exception->getMessage()}.");
        }
    }
}
