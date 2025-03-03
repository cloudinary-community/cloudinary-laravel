<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SvelteCommand extends Command
{
    protected $signature = 'cloudinary:svelte';

    protected $description = 'Install the Svelte SDK';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary Svelte SDK...');

        $process = new Process(['npm', 'install', 'svelte-cloudinary']);

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        $this->line(' ');
        $this->info('Cloudinary Svelte SDK installed successfully.');
        $this->line('Read getting started: https://svelte.cloudinary.dev/get-started');
    }
}
