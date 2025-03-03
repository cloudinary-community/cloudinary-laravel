<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;

class VueCommand extends Command
{
    protected $signature = 'cloudinary:vue';

    protected $description = 'Install the Vue components';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary Vue components...');
    }
}
