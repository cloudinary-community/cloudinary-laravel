<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Cloudinary;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToSetVisibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;

class CloudinaryStorageAdapter implements ChecksumProvider, FilesystemAdapter
{
    public function __construct(
        private Cloudinary $cloudinary,
        private ?MimeTypeDetector $mimeTypeDetector = null,
        private ?string $prefix = null)
    {
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector;
        $this->prefix = $prefix ? str_replace('\\', '/', trim($prefix, '/')) : '';
    }

    public function getUrl(string $path): string
    {
        [$id, $type] = $this->prepareResource($path);

        return $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type])->offsetGet('secure_url');
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        [$source] = $this->prepareResource($source);
        [$destination] = $this->prepareResource($destination);

        $this->cloudinary->uploadApi()->explicit($source, ['public_id' => $destination, 'type' => 'upload']);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->cloudinary->adminApi()->createFolder($this->applyPrefixToPath($path));
    }

    public function delete(string $path): void
    {
        [$id, $type] = $this->prepareResource($path);

        try {
            $result = $this->cloudinary->uploadApi()->destroy($id, ['resource_type' => $type]);
            if ($result['result'] !== 'ok') {
                throw UnableToDeleteFile::atLocation($path, $result['error']);
            }
        } catch (\Throwable $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }

    }

    public function deleteDirectory(string $path): void
    {
        $this->cloudinary->adminApi()->deleteAssetsByPrefix($this->applyPrefixToPath($path));
    }

    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }

    public function fileExists(string $path): bool
    {
        [$id, $type] = $this->prepareResource($path);

        try {
            $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function fileSize(string $path): FileAttributes
    {
        [$id, $type] = $this->prepareResource($path);
        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return new FileAttributes($path, $resource->offsetGet('bytes'));
    }

    public function lastModified(string $path): FileAttributes
    {
        [$id, $type] = $this->prepareResource($path);
        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        $dateTime = new \DateTime($resource->offsetGet('created_at'));

        return new FileAttributes($path, null, null, $dateTime->getTimestamp());
    }

    public function listContents(string $path, bool $deep): array|\Traversable
    {
        $resources = [];
        $response = null;

        do {
            $response = $this->cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => $this->applyPrefixToPath($path),
                'max_results' => 500,
                'next_cursor' => isset($response) ? $response->offsetGet('next_cursor') : null,
            ]);
            $resources = array_merge($resources, $response->offsetGet('resources'));
        } while ($response->offsetExists('next_cursor'));

        return array_map(
            fn ($resource) => new FileAttributes(
                $resource['public_id'],
                $resource['bytes'],
                null,
                (new \DateTime($resource['created_at']))->getTimestamp()
            ),
            $resources
        );
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes(
            $path,
            null,
            null,
            null,
            $this->mimeTypeDetector->detectMimeTypeFromPath($path)
        );
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function read(string $path): string
    {
        [$id, $type] = $this->prepareResource($path);
        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return file_get_contents($resource->offsetGet('secure_url'));
    }

    public function readStream(string $path)
    {
        [$id, $type] = $this->prepareResource($path);
        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return fopen($resource->offsetGet('secure_url'), 'rb');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary does not support visibility.');
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function write(string $path, string $contents, Config $config): void
    {
        [$id, $type] = $this->prepareResource($path);

        $this->cloudinary->uploadApi()->upload($contents, ['public_id' => $id, 'resource_type' => $type]);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        [$id, $type] = $this->prepareResource($path);

        $this->cloudinary->uploadApi()->upload($contents, ['public_id' => $id, 'resource_type' => $type]);
    }

    public function checksum(string $path, Config $config): string
    {
        $algo = $config->get('checksum_algo', 'sha256');

        [$id, $type] = $this->prepareResource($path);

        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return hash($algo, file_get_contents($resource['secure_url']));
    }

    public function prepareResource(string $path): array
    {
        $info = pathinfo($path);

        // Ensure dirname uses forward slashes, regardless of OS
        $dirname = str_replace('\\', '/', $info['dirname']);
        // Always use forward slash for path construction
        $id = $dirname.'/'.$info['filename'];

        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromPath($path);

        if (strpos($mimeType, 'image/') === 0) {
            return [$id, 'image'];
        }

        if (strpos($mimeType, 'video/') === 0) {
            return [$id, 'video'];
        }

        // If a prefix is configured, apply it to the id. When applying a prefix
        // strip any leading './' or '/' from the generated id so we don't end up
        // with paths like "prefix/./file".
        if ($this->prefix !== '') {
            $normalizedId = ltrim($id, './\\/');
            $id = $this->prefix.
                ($normalizedId !== '' ? '/'.$normalizedId : '');
        }

        return [$id, 'raw'];
    }

    private function applyPrefixToPath(string $path): string
    {
        if ($this->prefix === '') {
            return $path;
        }

        $trimmed = ltrim(str_replace('\\', '/', $path), '\/');

        return $this->prefix.($trimmed !== '' ? '/'.$trimmed : '');
    }
}
