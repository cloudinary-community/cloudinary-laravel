<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ReactCommand extends Command
{
    protected $signature = 'cloudinary:react';

    protected $description = 'Install the React components';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary React components...');

        $process = new Process(['npm', 'install', '@cloudinary/react', '@cloudinary/url-gen']);

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        $this->line(' ');
        $this->info('Cloudinary React components installed successfully.');
        $this->line('Read getting started: https://cloudinary.com/documentation/react_quick_start');
    }
}
