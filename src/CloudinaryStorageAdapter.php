<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Cloudinary;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToSetVisibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;

class CloudinaryStorageAdapter implements ChecksumProvider, FilesystemAdapter
{
    private Cloudinary $cloudinary;

    private PathPrefixer $prefixer;

    private MimeTypeDetector $mimeTypeDetector;

    public function __construct(array $config, string $prefix = '', ?MimeTypeDetector $mimeTypeDetector = null)
    {
        $this->prefixer = new PathPrefixer($prefix);
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector;

        if (isset($config['url'])) {
            $this->cloudinary = new Cloudinary($config['url']);
        } else {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $config['cloud'],
                    'api_key' => $config['key'],
                    'api_secret' => $config['secret'],
                ],
                'url' => [
                    'secure' => $config['secure'] ?? false,
                ],
            ]);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        [$source] = $this->prepareResource($source);
        [$destination] = $this->prepareResource($destination);

        $this->cloudinary->uploadApi()->explicit($source, ['public_id' => $destination, 'type' => 'upload']);
    }

    public function createDirectory(string $path, Config $config): void {}

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
        $this->cloudinary->adminApi()->deleteAssetsByPrefix($path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->cloudinary->adminApi()->asset($path);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    public function fileSize(string $path): FileAttributes
    {
        $resource = $this->cloudinary->adminApi()->asset($path);

        return new FileAttributes($path, $resource['bytes']);
    }

    public function lastModified(string $path): FileAttributes
    {
        $resource = $this->cloudinary->adminApi()->asset($path);

        return new FileAttributes($path, null, null, $resource['created_at']);
    }

    public function listContents(string $path, bool $deep): array|\Traversable
    {
        $resources = [];

        $response = null;

        do {
            $response = (array) $this->cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => $path,
                'max_results' => 500,
                'next_cursor' => $response['next_cursor'] ?? null,
            ]);
            $resources = array_merge($resources, $response['resources']);
        } while (array_key_exists('next_cursor', $response));

        return array_map(fn ($resource) => new FileAttributes($resource['public_id'], $resource['bytes'], null, $resource['created_at']), $resources);
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

        return file_get_contents($resource['secure_url']);
    }

    public function readStream(string $path)
    {
        [$id, $type] = $this->prepareResource($path);

        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return fopen($resource['secure_url'], 'rb');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary does not support visibility.');
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function write(string $path, string $contents, Config $config): void {}

    public function writeStream(string $path, $contents, Config $config): void {}

    public function checksum(string $path, Config $config): string
    {
        $algo = $config->get('checksum_algo', 'sha256');

        [$id, $type] = $this->prepareResource($path);

        $resource = $this->cloudinary->adminApi()->asset($id, ['resource_type' => $type]);

        return hash($algo, file_get_contents($resource['secure_url']));
    }

    private function prepareResource(string $path): array
    {
        $id = pathinfo($path, PATHINFO_FILENAME);
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromPath($path);

        if (strpos($mimeType, 'image/') === 0) {
            return [$id, 'image'];
        }

        if (strpos($mimeType, 'video/') === 0) {
            return [$id, 'video'];
        }

        return [$id, 'raw'];
    }
}
