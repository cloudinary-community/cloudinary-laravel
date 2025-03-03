<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;

class LivewireCommand extends Command
{
    protected $signature = 'cloudinary:livewire';

    protected $description = 'Install the Livewire components';

    protected $hidden = true;

    public function handle()
    {
        $this->info('Installing Cloudinary Livewire components...');
    }
}
