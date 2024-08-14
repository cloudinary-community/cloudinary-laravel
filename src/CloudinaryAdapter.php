<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\Config;
use Illuminate\Support\Str;
use Exception;
use Throwable;

/**
 * Class CloudinaryAdapter
 * @package CloudinaryLabs\CloudinaryLaravel
 */
class CloudinaryAdapter implements FilesystemAdapter
{
    /**
     * @var Cloudinary
     */
    protected Cloudinary $cloudinary;

    /**
     * The media resource extensions supported by cloudinary.
     */
    public $mediaExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'pdf', 'bmp', 'tiff', 'svg', 'ico', 'eps', 'psd', 'webp', 'jxr', 'wdp',
        'mpeg', 'mp4', 'mkv', 'mov', 'flv', 'avi', '3gp', '3g2', 'wmv', 'webm', 'ogv', 'mxf', 'avif',
    ];

    /**
     * Constructor
     * Sets configuration for Cloudinary Api.
     * @param string $config Cloudinary configuration
     */
    public function __construct(string $config)
    {
        $this->cloudinary = new Cloudinary($config);
    }

    /**
     * Write a new file.
     * Create temporary stream with content.
     * Pass to writeStream.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return void false on failure file meta data on success
     * @throws ApiError
     */
    public function write(string $path, string $contents, Config $config): void
    {
        $tempFile = tmpfile();

        fwrite($tempFile, $contents);

        $this->writeStream($path, $tempFile, $config);
    }

    /**
     * Write a new file using a stream
     *
     * @param string $path
     * @param resource $contents
     * @param Config $config Config object
     *
     * @return void false on failure file meta data on success
     * @throws ApiError
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $publicId = $this->preparePublicId($config->get('public_id', $path));

        $resourceType = $config->get('resource_type', 'auto');

        $uploadOptions = [
            'public_id'     => $publicId,
            'resource_type' => $resourceType
        ];

        $resourceMetadata = stream_get_meta_data($contents);

        $this->upload($resourceMetadata['uri'], $uploadOptions);
    }

    /**
     * Prepare the given public ID for cloudinary.
     */
    public function preparePublicId($path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return str($path)
            ->when($this->isMedia($extension))
            ->beforeLast('.'.$extension);
    }

    /**
     * Determine if the given extension is a media extension.
     */
    public function isMedia($extension): bool
    {
        return in_array($extension, $this->mediaExtensions);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename(string $path, string $newpath): bool
    {
        $pathInfo    = pathinfo($path);
        $newPathInfo = pathinfo($newpath);

        $remotePath = ($pathInfo['dirname'] != '.') ? $pathInfo['dirname'] . '/' . $pathInfo['filename'] : $pathInfo['filename'];

        $remoteNewPath = ($pathInfo['dirname'] != '.') ? $newPathInfo['dirname'] . '/' . $newPathInfo['filename'] : $newPathInfo['filename'];

        $result = $this->uploadApi()->rename(
            $this->preparePublicId($remotePath),
            $this->preparePublicId($remoteNewPath)
        );

        return $result['public_id'] == $newPathInfo['filename'];
    }

    /**
     * Expose the Cloudinary v2 Upload Functionality.
     */
    protected function uploadApi(): UploadApi
    {
        return $this->cloudinary->uploadApi();
    }

    /**
     * Upload a file
     *
     * @param string $file
     * @param array $options
     *
     * @return void
     * @throws ApiError
     */
    protected function upload(string $file, array $options = []): void
    {
        $this->uploadApi()->upload($file, $options);
    }

    /**
     * Copy a file.
     * Copy content from existing url.
     *
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @return void
     * @throws ApiError
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $source = $this->preparePublicId($source);
        $destination = $this->preparePublicId($destination);

        $this->uploadApi()->upload($source, ['public_id' => $destination]);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return void
     */
    public function delete(string $path): void
    {
        try {
            $result      = $this->uploadApi()->destroy($this->preparePublicId($path));
            $finalResult = is_array($result) && $result['result'] == 'ok';

            if ($finalResult != 'ok') {
                throw new UnableToDeleteFile('file not found');
            }
        } catch (Throwable $exception) {
            throw UnableToDeleteFile::atLocation($path, '', $exception);
        }
    }

    /**
     * Delete a directory.
     * Delete Files using directory as a prefix.
     *
     * @param string $path
     *
     * @return void
     *
     * @throws ApiError
     */
    public function deleteDirectory(string $path): void
    {
        $this->adminApi()->deleteAssetsByPrefix($path);
    }

    /**
     * Expose the Cloudinary v2 Upload Functionality
     *
     */
    protected function adminApi(): AdminApi
    {
        return $this->cloudinary->adminApi();
    }

    /**
     * Create a directory.
     *
     * @param string $path directory name
     * @param Config $config
     *
     * @return void
     *
     * @throws ApiError
     */
    public function createDirectory(string $path, Config $config): void
    {
        $this->adminApi()->createFolder($path);
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        try {
            $this->adminApi()->asset($this->preparePublicId($path));
        } catch (Exception) {
            return false;
        }

        return true;
    }

    /**
     * Check whether a directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return string
     */
    public function read(string $path): string
    {
        $resource = (array)$this->adminApi()->asset($this->preparePublicId($path));

        return file_get_contents($resource['secure_url']);
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return false
     */
    public function readStream(string $path): bool
    {
        $resource = (array)$this->adminApi()->asset($this->preparePublicId($path));

        return fopen($resource['secure_url'], 'rb');
    }

    /**
     * Set visibility for the file
     *
     * @param string $path
     * @param mixed $visibility
     *
     * @throws UnableToSetVisibility
     */
    public function setVisibility(string $path, $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary API does not support visibility.');
    }

    /**
     * Check visibility of the file
     *
     * @param string $path
     * @throws UnableToSetVisibility
     */
    public function visibility(string $path): FileAttributes
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary API does not support visibility.');
    }

    /**
     * List contents of a directory.
     *
     * @param string $path
     * @param bool $deep
     * @return iterable<StorageAttributes>
     */
    public function listContents(string $path = '', bool $deep = false): iterable
    {
        $resources = [];

        // get resources array
        $response = null;
        do {
            $response = (array)$this->adminApi()->assets(
                [
                    'type' => 'upload',
                    'prefix' => $path,
                    'max_results' => 500,
                    'next_cursor' => $response['next_cursor'] ?? null,
                ]
            );
            $resources = array_merge($resources, $response['resources']);
        } while (array_key_exists('next_cursor', $response));

        // parse resourses
        foreach ($resources as $i => $resource) {
            $resources[$i] = $this->prepareFileAttributes($this->prepareResourceMetadata($resource));
        }
        return $resources;
    }

    /**
     * Transform array of resource metadata into a {@link FileAttributes} instance.
     *
     * @param array $metadata
     *
     * @return FileAttributes
     */
    protected function prepareFileAttributes(array $metadata): FileAttributes
    {
        return new FileAttributes(
            $metadata['path'],
            $metadata['size'],
            null,
            $metadata['timestamp'],
            $metadata['mimetype'],
            $metadata
        );
    }

    /**
     * Prepare apropriate metadata for resource metadata given from cloudinary.
     * @param array $resource
     * @return array
     */
    protected function prepareResourceMetadata(array $resource): array
    {
        $resource['type'] = 'file';
        $resource['path'] = $resource['public_id'];
        $resource         = array_merge($resource, $this->prepareSize($resource));
        $resource         = array_merge($resource, $this->prepareTimestamp($resource));

        return array_merge($resource, $this->prepareMimetype($resource));
    }

    /**
     * prepare size response
     *
     * @param array $resource
     *
     * @return array
     */
    protected function prepareSize(array $resource): array
    {
        $size = $resource['bytes'];
        return compact('size');
    }

    /**
     * prepare timestamp response
     *
     * @param array $resource
     *
     * @return array
     */
    protected function prepareTimestamp(array $resource): array
    {
        $timestamp = strtotime($resource['created_at']);
        return compact('timestamp');
    }

    /**
     * prepare mimetype response
     *
     * @param array $resource
     *
     * @return array
     */
    protected function prepareMimetype(array $resource): array
    {
        $mimetype = $resource['resource_type'];
        return compact('mimetype');
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array
     */
    public function getMetadata(string $path): array
    {
        return $this->prepareResourceMetadata($this->getResource($path));
    }

    /**
     * Get Resource data
     * @param string $path
     * @return array
     */
    public function getResource(string $path): array
    {
        return (array)$this->adminApi()->asset($this->preparePublicId($path));
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array
     */
    public function getSize(string $path): array
    {
        return $this->prepareSize($this->getResource($path));
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array
     */
    public function getMimetype(string $path): array
    {
        return $this->prepareMimetype($this->getResource($path));
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return FileAttributes
     */
    public function mimeType(string $path): FileAttributes
    {
        $mimeType = $this->getMimetype($path);

        return new FileAttributes($path, null, null, null, $mimeType['mimetype']);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return FileAttributes
     */
    public function lastModified(string $path): FileAttributes
    {
        $timeStamp = $this->getTimestamp($path);

        return new FileAttributes($path, null, null, $timeStamp['timestamp']);
    }

    /**
     * Get the filesize of a file
     *
     * @param string $path
     *
     * @return FileAttributes
     */
    public function fileSize(string $path): FileAttributes
    {
        $fileSize = $this->getSize($path);

        return new FileAttributes($path, $fileSize['size']);
    }

    /**
     * Move a file to another location
     *
     * @param string $source
     * @param string $destination
     * @param Config $config
     *
     * @return void
     * @throws ApiError
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array
     */
    public function getTimestamp(string $path): array
    {
        return $this->prepareTimestamp($this->getResource($path));
    }

    /**
     * Get the url of a file
     *
     * @param string $path
     *
     * @return string|false
     */
    public function getUrl(string $path): bool|string
    {
        if ($path == '/') {
            return $path;
        }
        try {
            $resource = $this->getResource(Str::beforeLast($path, '.'));
            return $resource['secure_url'] ?? '';
        } catch (Exception) {
            return '';
        }
    }
}
