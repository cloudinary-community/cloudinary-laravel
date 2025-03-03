<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'cloudinary:install';

    protected $description = 'Install the Cloudinary Laravel SDK';

    public function handle()
    {
        if ($this->callSilently('vendor:publish', ['--tag' => 'cloudinary-config']) === 0) {
            $this->info('Configuration published successfully.');
        }

        match ($this->getDependencies()) {
            'react' => $this->call('cloudinary:react'),
            'vue' => $this->call('cloudinary:vue'),
            'livewire' => $this->call('cloudinary:livewire'),
            default => $this->info('No JavaScript framework detected.'),
        };
    }

    private function getDependencies(): ?string
    {
        $package = json_decode(File::get(base_path('package.json')), true);

        $dependencies = array_merge(
            $package['dependencies'] ?? [],
            $package['devDependencies'] ?? []
        );

        if (isset($dependencies['@vitejs/plugin-react'])) {
            return 'react';
        }

        if (isset($dependencies['@vitejs/plugin-vue'])) {
            return 'vue';
        }

        if (class_exists('Livewire\Livewire')) {
            return 'livewire';
        }

        return null;
    }
}
