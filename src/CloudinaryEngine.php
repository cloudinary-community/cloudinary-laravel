<?php

/*
 * This file is part of the Laravel Cloudinary package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Cloudinary;
use Cloudinary\Tag\ImageTag;
use Cloudinary\Tag\VideoTag;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\BaseApiClient;
use Cloudinary\Api\Exception\ApiError;
use GuzzleHttp\Promise\PromiseInterface;
use CloudinaryLabs\Exceptions\IsNullException;

/**
 * Class CloudinaryEngine
 * @package CloudinaryLabs\CloudinaryLaravel
 */
class CloudinaryEngine
{
    /**
     * Laravel Package Version.
     *
     * @var string PACKAGE_VERSION
     */
    const PACKAGE_VERSION = '1.0.5';

    public const ASSET_ID = 'asset_id';
    public const PUBLIC_ID = 'public_id';
    public const VERSION = 'version';
    public const VERSION_ID = 'version_id';
    public const SIGNATURE = 'signature';
    public const WIDTH = 'width';
    public const HEIGHT = 'height';
    public const FORMAT = 'format';
    public const RESOURCE_TYPE = 'resource_type';
    public const CREATED_AT = 'created_at';
    public const TAGS = 'tags';
    public const PAGES = 'pages';
    public const BYTES = 'bytes';
    public const TYPE = 'type';
    public const ETAG = 'etag';
    public const PLACEHOLDER = 'placeholder';
    public const URL = 'url';
    public const SECURE_URL = 'secure_url';
    public const PHASH = 'phash';
    public const ORIGINAL_FILENAME = 'original_filename';

    /**
     * Instance of Cloudinary
     * @var Cloudinary
     */
    protected $cloudinary;

    /**
     * Instance of Cloudinary Config
     * @var Configuration
     */
    protected $cloudinaryConfig;

    /**
     *  Response from Cloudinary
     * @var Array
     */
    protected $response;

    public function __construct()
    {
        $this->setUserPlatform();
        $this->setCloudinaryConfig();
        $this->bootCloudinary();
    }

    /**
     * Create a Cloudinary Config Instance
     *
     */
    public function setCloudinaryConfig()
    {
        $config = config('cloudinary.cloud_url');
        $this->cloudinaryConfig = $config;
    }

    /**
     * Set User Agent and Platform
     *
     */
    public function setUserPlatform()
    {
        BaseApiClient::$userPlatform = 'CloudinaryLaravel/' . self::PACKAGE_VERSION;
    }

    /**
     * Create a Cloudinary Instance
     *
     */
    public function bootCloudinary()
    {
        $this->cloudinary = new Cloudinary($this->cloudinaryConfig);
    }

    /**
     * Expose the Cloudinary Admin Functionality
     *
     */
    public function admin()
    {
        return $this->cloudinary->adminApi();
    }

    /**
     * Expose the Cloudinary Search Functionality
     *
     */
    public function search()
    {
        return $this->cloudinary->searchApi();
    }

    /**
     * Uploads an asset to a Cloudinary account.
     *
     * The asset can be:
     * * a local file path
     * * the actual data (byte array buffer)
     * * the Data URI (Base64 encoded), max ~60 MB (62,910,000 chars)
     * * the remote FTP, HTTP or HTTPS URL address of an existing file
     * * a private storage bucket (S3 or Google Storage) URL of a whitelisted bucket
     *
     * @param string $file The asset to upload.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @throws ApiError
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#upload_method
     */
    public function upload($file, $options = [])
    {
        $this->response = $this->uploadApi()->upload($file, $options);

        return $this;
    }

    /**
     * Expose the Cloudinary Upload Functionality
     *
     */
    public function uploadApi()
    {
        return $this->cloudinary->uploadApi();
    }

    /**
     * Uploads an asset to a Cloudinary account.
     *
     * The asset can be:
     * * a local file path
     * * the actual data (byte array buffer)
     * * the Data URI (Base64 encoded), max ~60 MB (62,910,000 chars)
     * * the remote FTP, HTTP or HTTPS URL address of an existing file
     * * a private storage bucket (S3 or Google Storage) URL of a whitelisted bucket
     *
     *  This is asynchronous
     */
    public function uploadAsync($file, $options = [])
    {
        return $this->uploadApi()->uploadAsync($file, $options);
    }

    /**
     * Uploads an asset to a Cloudinary account.
     *
     * The upload is not signed so an upload preset is required.
     *
     * @param string $file The asset to upload.
     * @param string $uploadPreset The name of an upload preset.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @throws ApiError
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#unsigned_upload_syntax
     */
    public function unsignedUpload($file, $uploadPreset, $options = [])
    {
        $this->response = $this->uploadApi()->unsignedUpload($file, $uploadPreset, $options);

        return $this;
    }

    /**
     * Uploads an asset to a Cloudinary account.
     *
     * The upload is not signed so an upload preset is required.
     *
     * This is asynchronous
     */
    public function unsignedUploadAsync($file, $uploadPreset, $options = [])
    {
        return $this->uploadApi()->unsignedUploadAsync($file, $uploadPreset, $options);
    }

    /**
     * @param $file
     * @param array $options
     * @return $this
     * @throws ApiError
     */
    public function uploadFile($file, $options = [])
    {
        $uploadOptions = array_merge($options, ['resource_type' => 'auto']);

        $this->response = $this->uploadApi()->upload($file, $uploadOptions);

        return $this;
    }

    /**
     * @param $file
     * @param array $options
     * @return $this
     * @throws ApiError
     */
    public function uploadVideo($file, $options = [])
    {
        $videoUploadOptions = array_merge($options, ['resource_type' => 'video']);

        $this->response = $this->uploadApi()->upload($file, $videoUploadOptions);

        return $this;
    }

    /**
     * @return Array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getAssetId()
    {
        return $this->response[self::ASSET_ID];
    }

    /**
     * Get the name of the file after it has been uploaded to Cloudinary
     * @return string
     */
    public function getFileName()
    {
        return $this->response[self::PUBLIC_ID];
    }

    /**
     * Get the public id of the file (also known as the name of the file) after it has been uploaded to Cloudinary
     * @return string
     */
    public function getPublicId()
    {
        return $this->response[self::PUBLIC_ID];
    }

    /**
     * Get the name of the file before it was uploaded to Cloudinary
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->response[self::ORIGINAL_FILENAME];
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->response[self::VERSION];
    }

    /**
     * @return mixed
     */
    public function getVersionId()
    {
        return $this->response[self::VERSION_ID];
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->response[self::SIGNATURE];
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->response[self::WIDTH];
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->response[self::HEIGHT];
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->response[self::FORMAT];
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->response[self::RESOURCE_TYPE];
    }

    /**
     * @return mixed
     */
    public function getTimeUploaded()
    {
        return $this->response[self::CREATED_AT];
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->response[self::TAGS];
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->response[self::PAGES];
    }

    /**
     * @return string
     */
    public function getReadableSize()
    {
        return $this->getHumanReadableSize($this->getSize());
    }

    /**
     * Formats filesize in the way every human understands
     *
     * @param file $file
     * @return string Formatted Filesize, e.g. "113.24 MB".
     */
    private function getHumanReadableSize($sizeInBytes)
    {
        if ($sizeInBytes >= 1073741824) {
            return number_format($sizeInBytes / 1073741824, 2) . ' GB';
        } elseif ($sizeInBytes >= 1048576) {
            return number_format($sizeInBytes / 1048576, 2) . ' MB';
        } elseif ($sizeInBytes >= 1024) {
            return number_format($sizeInBytes / 1024, 2) . ' KB';
        } elseif ($sizeInBytes > 1) {
            return $sizeInBytes . ' bytes';
        } elseif ($sizeInBytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->response[self::BYTES];
    }

    /**
     * @return mixed
     */
    public function getPlaceHolder()
    {
        return $this->response[self::PLACEHOLDER];
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->response[self::URL];
    }

    /**
     * @return mixed
     */
    public function getSecurePath()
    {
        return $this->response[self::SECURE_URL];
    }

    /**
     * @return mixed
     */
    public function getPhash()
    {
        return $this->response[self::PHASH];
    }

    /**
     * Fetches a new Image with current instance configuration.
     *
     * @param string $publicId The public ID of the image.
     *
     * @return Image
     */
    public function getImage($publicId)
    {
        return $this->cloudinary->image($publicId);
    }

    /**
     * Fetches a new Video with current instance configuration.
     *
     * @param string|mixed $publicId The public ID of the video.
     *
     * @return Video
     */
    public function getVideo($publicId)
    {
        return $this->cloudinary->video($publicId);
    }

    /**
     * Fetches a raw file with current instance configuration.
     *
     * @param string|mixed $publicId The public ID of the file.
     *
     * @return File
     */
    public function getFile($publicId)
    {
        return $this->cloudinary->raw($publicId);
    }

    /**
     * @param $publicId
     * @return ImageTag
     */
    public function getImageTag($publicId)
    {
        return $this->cloudinary->imageTag($publicId);
    }

    /**
     * @param $publicId
     * @return VideoTag
     */
    public function getVideoTag($publicId)
    {
        return $this->cloudinary->videoTag($publicId);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Tags
    |--------------------------------------------------------------------------
    */

    /**
     * Adds a tag to the assets specified.
     *
     * @param string $tag The name of the tag to add.
     * @param array $publicIds The public IDs of the assets to add the tag to.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     */
    public function addTag($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addTag($tag, $publicIds, $options);
    }

    /**
     * Adds a tag to the assets specified.
     *
     * This is an asynchronous function.
     */
    public function addTagAsync($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addTagAsync($tag, $publicIds, $options);
    }

    /**
     * Removes a tag from the assets specified.
     *
     * @param string $tag The name of the tag to remove.
     * @param array|string $publicIds The public IDs of the assets to remove the tags from.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     */
    public function removeTag($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeTag($tag, $publicIds, $options);
    }

    /**
     * Removes a tag from the assets specified.
     *
     * This is an asynchronous function.
     *
     */
    public function removeTagAsync($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeTagAsync($tag, $publicIds, $options);
    }

    /**
     * Removes all tags from the assets specified.
     *
     * @param array $publicIds The public IDs of the assets to remove all tags from.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     */
    public function removeAllTags($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllTags($publicIds, $options);
    }

    /**
     * Removes all tags from the assets specified.
     *
     * This is an asynchronous function.
     *
     */
    public function removeAllTagsAsync($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllTagsAsync($publicIds, $options);
    }

    /**
     * Replaces all existing tags on the assets specified with the tag specified.
     *
     * @param string $tag The new tag with which to replace the existing tags.
     * @param array|string $publicIds The public IDs of the assets to replace the tags of.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     */
    public function replaceTag($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->replaceTag($tag, $publicIds, $options);
    }

    /**
     * Replaces all existing tags on the assets specified with the tag specified.
     *
     * This is an asynchronous function.
     *
     */
    public function replaceTagAsync($tag, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->replaceTagAsync($tag, $publicIds, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Sprite & Image Generation
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a sprite from all images that have been assigned a specified tag.
     *
     * The process produces two files:
     * * A single image file containing all the images with the specified tag (PNG by default).
     * * A CSS file that includes the style class names and the location of the individual images in the sprite.
     *
     * @param string $tag The tag that indicates which images to include in the sprite.
     * @param array $options The optional parameters. See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#sprite_method
     */
    public function generateSprite($tag, $options = [])
    {
        return $this->uploadApi()->generateSprite($tag, $options);
    }

    /**
     * Creates a sprite from all images that have been assigned a specified tag.
     *
     * This is an asynchronous function.
     */
    public function generateSpriteAsync($tag, $options = [])
    {
        return $this->uploadApi()->generateSpriteAsync($tag, $options);
    }

    /**
     * Creates a PDF file from images in your media library that have been assigned a specific tag.
     *
     * Important note for free accounts:
     * By default, while you can use this method to generate PDF files, free Cloudinary accounts are blocked from delivering
     * files in PDF format for security reasons.
     *For details or to request that this limitation be removed for your free account, see Media delivery.
     *
     * @see https://cloudinary.com/documentation/paged_and_layered_media#creating_pdf_files_from_images
     */
    public function generatePDF($tag, $options = [])
    {
        $pdfOptions = array_merge($options, ['async' => false, 'format' => 'pdf']);

        return $this->uploadApi()->multi($tag, $pdfOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generatePDFAsync($tag, $options = [])
    {
        $pdfOptions = array_merge($options, ['async' => true, 'format' => 'pdf']);

        return $this->uploadApi()->multi($tag, $pdfOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedGIF($tag, $options = [])
    {
        $gifOptions = array_merge($options, ['async' => false, 'format' => 'gif']);

        return $this->uploadApi()->multi($tag, $gifOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedPNG($tag, $options = [])
    {
        $pngOptions = array_merge($options, ['async' => false, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedPNGAsync($tag, $options = [])
    {
        $pngOptions = array_merge($options, ['async' => true, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBP($tag, $options = [])
    {
        $webpOptions = array_merge($options, ['async' => false, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBPAsync($tag, $options = [])
    {
        $webpOptions = array_merge($options, ['async' => true, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedMP4($tag, $options = [])
    {
        $mp4Options = array_merge($options, ['async' => false, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedMP4Async($tag, $options = [])
    {
        $mp4Options = array_merge($options, ['async' => true, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBM($tag, $options = [])
    {
        $webmOptions = array_merge($options, ['async' => false, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBMAsync($tag, $options = [])
    {
        $webmOptions = array_merge($options, ['async' => true, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function multi($tag, $options = [])
    {
        return $this->uploadApi()->multi($tag, $options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return PromiseInterface
     */
    public function multiAsync($tag, $options = [])
    {
        return $this->uploadApi()->multiAsync($tag, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return ApiResponse
     */
    public function explode($publicId, $options = [])
    {
        return $this->uploadApi()->explode($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function explodeAsync($publicId, $options = [])
    {
        return $this->uploadApi()->explodeAsync($publicId, $options);
    }

    /**
     * Dynamically generates an image from a given textual string.
     *
     * @param string $text The text string to generate an image for.
     * @param array $options The optional parameters.  See the upload API documentation.
     *
     * @return ApiResponse
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#text_method
     */
    public function generateImageFromText($text, $options = [])
    {
        $this->response = $this->uploadApi()->text($text, $options);

        return $this;
    }

    /**
     * @param $text
     * @param array $options
     * @return PromiseInterface
     */
    public function generateImageFromTextAsync($text, $options = [])
    {
        return $this->uploadApi()->textAsync($text, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Zip & Archives
    |--------------------------------------------------------------------------
    */

    /**
     * @param array $options
     * @param null $targetFormat
     * @return ApiResponse
     */
    public function createArchive($options = [], $targetFormat = null)
    {
        return $this->uploadApi()->createArchive($options, $targetFormat);
    }

    /**
     * @param array $options
     * @param null $targetFormat
     * @return PromiseInterface
     */
    public function createArchiveAsync($options = [], $targetFormat = null)
    {
        return $this->uploadApi()->createArchiveAsync($options, $targetFormat);
    }

    /**
     * @param array $options
     * @return ApiResponse
     */
    public function createZip($options = [])
    {
        return $this->uploadApi()->createZip($options);
    }

    /**
     * @param array $options
     * @return PromiseInterface
     */
    public function createZipAsync($options = [])
    {
        return $this->uploadApi()->createZipAsync($options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function downloadZipUrl($options = [])
    {
        return $this->uploadApi()->downloadZipUrl($options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function downloadArchiveUrl($options = [])
    {
        return $this->uploadApi()->downloadArchiveUrl($options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Context
    |--------------------------------------------------------------------------
    */

    /**
     * @param $context
     * @param array $publicIds
     * @param array $options
     * @return ApiResponse
     */
    public function addContext($context, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addContext($context, $publicIds, $options);
    }

    /**
     * @param $context
     * @param array $publicIds
     * @param array $options
     * @return PromiseInterface
     */
    public function addContextAsync($context, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addContextAsync($context, $publicIds, $options);
    }

    /**
     * @param array $publicIds
     * @param array $options
     * @return ApiResponse
     */
    public function removeAllContext($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllContext($publicIds, $options);
    }

    /**
     * @param array $publicIds
     * @param array $options
     * @return PromiseInterface
     */
    public function removeAllContextAsync($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllContextAsync($publicIds, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Delete & Rename Assets
    |--------------------------------------------------------------------------
    */

    /**
     * @param $publicId
     * @param array $options
     * @return ApiResponse
     */
    public function destroy($publicId, $options = [])
    {
        return $this->uploadApi()->destroy($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function destroyAsync($publicId, $options = [])
    {
        return $this->uploadApi()->destroyAsync($publicId, $options);
    }

    /**
     * @param $from
     * @param $to
     * @param array $options
     * @return mixed
     */
    public function rename($from, $to, $options = [])
    {
        return $this->uploadApi()->rename($from, $to, $options);
    }

    /**
     * @param $from
     * @param $to
     * @param array $options
     * @return PromiseInterface
     */
    public function renameAsync($from, $to, $options = [])
    {
        return $this->uploadApi()->renameAsync($from, $to, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return mixed
     */
    public function explicit($publicId, $options = [])
    {
        return $this->uploadApi()->explicit($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function explicitAsync($publicId, $options = [])
    {
        return $this->uploadApi()->explicitAsync($publicId, $options);
    }

    /**
     * Get Resource data
     * @param string $path
     * @return array
     */
    public function getResource($path)
    {
        try {
            return $this->admin()->asset($path);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get the url of a file
     *
     * @param string $publicId
     *
     * @return string|false
     */
    public function getUrl($publicId)
    {

        $resource = $this->getResource($publicId);
        return $resource['secure_url'] ?? '';
    }
}
