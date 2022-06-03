<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Exception;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Exception\NotFound;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\Config;
use Illuminate\Support\Str;

/**
 * Class CloudinaryAdapter
 * @package CloudinaryLabs\CloudinaryLaravel
 */
class CloudinaryAdapter implements FilesystemAdapter
{

    /** Cloudinary\Cloudinary */
    protected $cloudinary;


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
     * @param Config $options Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $options): void
    {
        $tempFile = tmpfile();

        fwrite($tempFile, $contents);

        $this->writeStream($path, $tempFile, $options);
    }

    /**
     * Write a new file using a stream
     *
     * @param string    $path
     * @param resource  $resource
     * @param Config    $options Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $options): void
    {
        $publicId = $options->get('public_id', $path);

        $resourceType = $options->get('resource_type', 'auto');

        $fileExtension = pathinfo($publicId, PATHINFO_EXTENSION);

        $newPublicId = $fileExtension ? substr($publicId, 0, - (strlen($fileExtension) + 1)) : $publicId;

        $uploadOptions = [
            'public_id'     => $newPublicId,
            'resource_type' => $resourceType
        ];

        $resourceMetadata = stream_get_meta_data($resource);

        resolve(CloudinaryEngine::class)->upload($resourceMetadata['uri'], $uploadOptions);
    }


    /**
     * Rename a file.
     * Paths without extensions.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $pathInfo    = pathinfo($path);
        $newPathInfo = pathinfo($newpath);

        $remotePath = ($pathInfo['dirname'] != '.') ? pathInfo['dirname'] . '/' . $pathInfo['filename'] : $pathInfo['filename'];

        $remoteNewPath = ($pathInfo['dirname'] != '.') ? $newPathInfo['dirname'] . '/' . $newPathInfo['filename'] : $newPathInfo['filename'];

        $result = $this->uploadApi()->rename($remotePath, $remoteNewPath);

        return $result['public_id'] == $newPathInfo['filename'];
    }

    /**
     * Expose the Cloudinary v2 Upload Functionality
     *
     */
    protected function uploadApi()
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
     */
    protected function upload($file, $options = [])
    {
        $this->uploadApi()->upload($file, $options);
    }

    /**
     * Copy a file.
     * Copy content from existing url.
     *
     * @param string $path
     * @param string $newpath
     * @param Config $options
     * @return void
     */
    public function copy($path, $newpath, Config $options): void
    {
        $this->uploadApi()->upload($path, ['public_id' => $newpath]);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path): void
    {
        try {

            $result      = $this->uploadApi()->destroy($path);
            $finalResult = is_array($result) ? $result['result'] == 'ok' : false;

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
     * @param string $dirname
     *
     * @return void
     *
     */
    public function deleteDirectory($dirname): void
    {
        $this->adminApi()->deleteAssetsByPrefix($dirname);
    }

    /**
     * Expose the Cloudinary v2 Upload Functionality
     *
     */
    protected function adminApi()
    {
        return $this->cloudinary->adminApi();
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $options
     *
     * @return bool
     *
     */
    public function createDirectory($dirname, Config $options): void
    {
        $this->adminApi()->createFolder($dirname);
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
            $this->adminApi()->asset($path);
        } catch (NotFound $e) {
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
     * @return array|false
     */
    public function read($path): string
    {
        $resource = (array)$this->adminApi()->asset($path);

        return file_get_contents($resource['secure_url']);
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        $resource = (array)$this->adminApi()->asset($path);

        return fopen($resource['secure_url'], 'rb');
    }

    /**
    * Set visibility for the file
    *
    * @param string $path
    * @param mixed $visibility
    *
    * @throws \League\Flysystem\UnableToSetVisibility
    */
    public function setVisibility($path, $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary API does not support visibility.');
    }

    /**
     * Check visibility of the file
     *
     * @param string $path
     * @throws \League\Flysystem\UnableToSetVisibility
     */
    public function visibility(string $path): FileAttributes
    {
        throw UnableToSetVisibility::atLocation($path, 'Cloudinary API does not support visibility.');
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $hasRecursive
     * @return iterable<\League\Flysystem\StorageAttributes>
     */
    public function listContents($directory = '', $hasRecursive = false): iterable
    {
        $resources = [];

        // get resources array
        $response = null;
        do {
            $response = (array)$this->adminApi()->assets(
                [
                    'type' => 'upload',
                    'prefix' => $directory,
                    'max_results' => 500,
                    'next_cursor' => isset($response['next_cursor']) ? $response['next_cursor'] : null,
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
     * Transform array of resource metadata into a {@link \League\Flysystem\FileAttributes} instance.
     *
     * @param array $metadata
     *
     * @return \League\Flysystem\FileAttributes
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
    protected function prepareResourceMetadata($resource)
    {
        $resource['type'] = 'file';
        $resource['path'] = $resource['public_id'];
        $resource         = array_merge($resource, $this->prepareSize($resource));
        $resource         = array_merge($resource, $this->prepareTimestamp($resource));
        $resource         = array_merge($resource, $this->prepareMimetype($resource));
        return $resource;
    }

    /**
     * prepare size response
     *
     * @param array $resource
     *
     * @return array
     */
    protected function prepareSize($resource)
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
    protected function prepareTimestamp($resource)
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
    protected function prepareMimetype($resource)
    {
        $mimetype = $resource['resource_type'];
        return compact('mimetype');
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        return $this->prepareResourceMetadata($this->getResource($path));
    }

    /**
     * Get Resource data
     * @param string $path
     * @return array
     */
    public function getResource($path)
    {
        return (array)$this->adminApi()->asset($path);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->prepareSize($this->getResource($path));
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
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
     * @param string $path
     * @param string $destination
     * @param Config $config
     *
     * @return void
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
     * @return array|false
     */
    public function getTimestamp($path)
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
    public function getUrl($path)
    {
        if ($path == '/') {
            return $path;
        }
        try {
            $resource = $this->getResource(Str::beforeLast($path, '.'));
            return $resource['secure_url'] ?? '';
        } catch (NotFound $e) {
            return '';
        }
    }
}
