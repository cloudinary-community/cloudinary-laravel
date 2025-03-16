<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class VueCommand extends Command
{
    protected $signature = 'cloudinary:vue';

    protected $description = 'Install the Vue SDK';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary Vue SDK...');

        $process = new Process(['npm', 'install', '@cloudinary/vue', '@cloudinary/url-gen']);

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        $this->line(' ');
        $this->info('Cloudinary Vue SDK installed successfully.');
        $this->line('Read getting started: https://cloudinary.com/documentation/vue_quick_start');
    }
}
