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
            'svelte' => $this->call('cloudinary:svelte'),
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

        if (isset($dependencies['@sveltejs/vite-plugin-svelte'])) {
            return 'svelte';
        }

        return null;
    }
}
