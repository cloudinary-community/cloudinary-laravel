<?php

namespace CloudinaryLabs\CloudinaryLaravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'cloudinary:install';

    protected $description = 'Install the Cloudinary Laravel SDK';

    protected function hasLivewire(): bool
    {
        return class_exists(\Livewire\Livewire::class);
    }

    protected function hasInertia(): bool
    {
        return class_exists(\Inertia\Inertia::class);
    }

    protected function detectInertiaFramework(): ?string
    {
        if (! File::exists(base_path('package.json'))) {
            return null;
        }

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

    protected function installLivewireComponents(): void
    {
        // @TODO: implement livewire components publishing
    }

    protected function installInertiaComponents(string $framework): void
    {
        switch ($framework) {
            case 'react':
                $this->requireNpmPackages(['@cloudinary/react', '@cloudinary/url-gen']);
                break;
            case 'vue':
                $this->requireNpmPackages(['@cloudinary/vue', '@cloudinary/url-gen']);
                break;
        }
    }

    protected function requireNpmPackages(array $packages): void
    {
        $command = 'npm install '.implode(' ', $packages);

        if (File::exists(base_path('pnpm-lock.yaml'))) {
            $command = 'pnpm add '.implode(' ', $packages);
        } elseif (File::exists(base_path('yarn.lock'))) {
            $command = 'yarn add '.implode(' ', $packages);
        }

        // @TODO: run the command
    }

    protected function installBladeComponents(): void
    {
        // @TODO: implement blade components publishing
    }

    public function handle()
    {
        $this->info('Installing Cloudinary Laravel SDK...');

        $installedComponents = false;

        if ($this->hasLivewire() && $this->confirm('We detected Livewire in your application. Would you like to install the Livewire components?')) {
            $this->installLivewireComponents();
            $installedComponents = true;
        }

        if (! $installedComponents && $this->hasInertia()) {
            $framework = $this->detectInertiaFramework();
            if ($framework && $this->confirm("We detected Inertia.js with {$framework}. Would you like to install the {$framework} components?")) {
                $this->installInertiaComponents($framework);
                $installedComponents = true;
                $this->info("Cloudinary {$framework} components installed successfully.");
            }
        }

        if (! $installedComponents) {
            $this->installBladeComponents();
            $this->info('Blade components installed successfully.');
        }

        $this->info('Cloudinary Laravel SDK installation completed.');
    }
}
