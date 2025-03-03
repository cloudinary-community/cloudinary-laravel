<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;

class ReactCommand extends Command
{
    protected $signature = 'cloudinary:react';

    protected $description = 'Install the React components';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary React components...');
    }
}
