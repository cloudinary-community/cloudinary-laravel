<?php

/*
 * This file is part of the Laravel Cloudinary package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Search\SearchApi;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Asset\File;
use Cloudinary\Asset\Image;
use Cloudinary\Asset\Video;
use Cloudinary\Cloudinary;
use Cloudinary\Asset\Analytics;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Tag\ImageTag;
use Cloudinary\Tag\VideoTag;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\BaseApiClient;
use Cloudinary\Api\Exception\ApiError;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Class CloudinaryEngine
 * @package CloudinaryLabs\CloudinaryLaravel
 */
class CloudinaryEngine
{
    /**
     * Cloudinary Laravel Package Version.
     *
     * @var string PACKAGE_VERSION
     */
    const PACKAGE_VERSION = '2.2.2';

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
    protected Cloudinary $cloudinary;

    /**
     * Cloudinary url
     * @var string
     */
    protected string $url;

    /**
     *  Response from Cloudinary
     * @var array|ApiResponse
     */
    protected array|ApiResponse $response;

    public function __construct()
    {
        $this->setUserPlatform();
        $this->setAnalytics();
        $this->setCloudinaryConfig();
        $this->bootCloudinary();
    }

    /**
     * Create a Cloudinary Config Instance
     *
     */
    public function setCloudinaryConfig(): void
    {
        $this->url = config('cloudinary.cloud_url');
    }

    /**
     * Set User Agent and Platform
     *
     */
    public function setUserPlatform(): void
    {
        BaseApiClient::$userPlatform = 'CloudinaryLaravel/' . self::PACKAGE_VERSION;
    }

    /**
     * Set Analytics
     */
    public function setAnalytics(): void
    {
        Analytics::sdkCode('W');
        Analytics::sdkVersion(self::PACKAGE_VERSION);
        Analytics::techVersion(app()->version());
    }

    /**
     * Create a Cloudinary Instance
     *
     */
    public function bootCloudinary(): void
    {
        $this->cloudinary = new Cloudinary($this->url);
    }

    /**
     * Expose the Cloudinary Admin Functionality
     *
     */
    public function admin(): AdminApi
    {
        return $this->cloudinary->adminApi();
    }

    /**
     * Expose the Cloudinary Search Functionality
     *
     */
    public function search(): SearchApi
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
     * @return CloudinaryEngine
     *
     * @throws ApiError
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#upload_method
     */
    public function upload(string $file, array $options = []): static
    {
        $this->response = $this->uploadApi()->upload($file, $options);

        return $this;
    }

    /**
     * Expose the Cloudinary Upload Functionality
     *
     */
    public function uploadApi(): UploadApi
    {
        return $this->cloudinary->uploadApi();
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
     * @return CloudinaryEngine
     *
     * @throws ApiError
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#unsigned_upload_syntax
     */
    public function unsignedUpload(string $file, string $uploadPreset, array $options = []): static
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
     * @throws ApiError
     */
    public function unsignedUploadAsync($file, $uploadPreset, $options = []): PromiseInterface
    {
        return $this->uploadApi()->unsignedUploadAsync($file, $uploadPreset, $options);
    }

    /**
     * @param $file
     * @param array $options
     * @return $this
     * @throws ApiError
     */
    public function uploadFile($file, array $options = []): static
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
    public function uploadVideo($file, array $options = []): static
    {
        $videoUploadOptions = array_merge($options, ['resource_type' => 'video']);

        $this->response = $this->uploadApi()->upload($file, $videoUploadOptions);

        return $this;
    }

    /**
     * @return array|ApiResponse
     */
    public function getResponse(): array|ApiResponse
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getAssetId(): mixed
    {
        return $this->response[self::ASSET_ID];
    }

    /**
     * Get the name of the file after it has been uploaded to Cloudinary
     * @return string
     */
    public function getFileName(): string
    {
        return $this->response[self::PUBLIC_ID];
    }

    /**
     * Get the public id of the file (also known as the name of the file) after it has been uploaded to Cloudinary
     * @return string
     */
    public function getPublicId(): string
    {
        return $this->response[self::PUBLIC_ID];
    }

    /**
     * Get the name of the file before it was uploaded to Cloudinary
     * @return string
     */
    public function getOriginalFileName(): string
    {
        return $this->response[self::ORIGINAL_FILENAME];
    }

    /**
     * @return mixed
     */
    public function getVersion(): mixed
    {
        return $this->response[self::VERSION];
    }

    /**
     * @return mixed
     */
    public function getVersionId(): mixed
    {
        return $this->response[self::VERSION_ID];
    }

    /**
     * @return mixed
     */
    public function getSignature(): mixed
    {
        return $this->response[self::SIGNATURE];
    }

    /**
     * @return mixed
     */
    public function getWidth(): mixed
    {
        return $this->response[self::WIDTH];
    }

    /**
     * @return mixed
     */
    public function getHeight(): mixed
    {
        return $this->response[self::HEIGHT];
    }

    /**
     * @return mixed
     */
    public function getExtension(): mixed
    {
        return $this->response[self::FORMAT];
    }

    /**
     * @return mixed
     */
    public function getFileType(): mixed
    {
        return $this->response[self::RESOURCE_TYPE];
    }

    /**
     * @return mixed
     */
    public function getTimeUploaded(): mixed
    {
        return $this->response[self::CREATED_AT];
    }

    /**
     * @return mixed
     */
    public function getTags(): mixed
    {
        return $this->response[self::TAGS];
    }

    /**
     * @return mixed
     */
    public function getPages(): mixed
    {
        return $this->response[self::PAGES];
    }

    /**
     * @return string
     */
    public function getReadableSize(): string
    {
        return $this->getHumanReadableSize($this->getSize());
    }

    /**
     * Formats filesize in the way every human understands
     *
     * @param $sizeInBytes
     * @return string Formatted Filesize, e.g. "113.24 MB".
     */
    public function getHumanReadableSize($sizeInBytes): string
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
    public function getSize(): mixed
    {
        return $this->response[self::BYTES];
    }

    /**
     * @return mixed
     */
    public function getPlaceHolder(): mixed
    {
        return $this->response[self::PLACEHOLDER];
    }

    /**
     * @return mixed
     */
    public function getPath(): mixed
    {
        return $this->response[self::URL];
    }

    /**
     * @return mixed
     */
    public function getSecurePath(): mixed
    {
        return $this->response[self::SECURE_URL];
    }

    /**
     * @return mixed
     */
    public function getPhash(): mixed
    {
        return $this->response[self::PHASH];
    }

    /**
     * @return mixed
     */
    public function getEtag(): mixed
    {
        return $this->response[self::ETAG];
    }

    /**
     * Fetches a new Image with current instance configuration.
     *
     * @param string $publicId The public ID of the image.
     *
     * @return Image
     */
    public function getImage(string $publicId): Image
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
    public function getVideo(mixed $publicId): Video
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
    public function getFile(mixed $publicId): File
    {
        return $this->cloudinary->raw($publicId);
    }

    /**
     * @param $publicId
     * @return ImageTag
     */
    public function getImageTag($publicId): ImageTag
    {
        return $this->cloudinary->imageTag($publicId);
    }

    /**
     * @param $publicId
     * @return VideoTag
     */
    public function getVideoTag($publicId): VideoTag
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
    public function addTag(string $tag, array $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->addTag($tag, $publicIds, $options);
    }

    /**
     * Adds a tag to the assets specified.
     *
     * This is an asynchronous function.
     */
    public function addTagAsync($tag, $publicIds = [], $options = []): PromiseInterface
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
    public function removeTag(string $tag, array|string $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->removeTag($tag, $publicIds, $options);
    }

    /**
     * Removes a tag from the assets specified.
     *
     * This is an asynchronous function.
     *
     */
    public function removeTagAsync($tag, $publicIds = [], $options = []): PromiseInterface
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
    public function removeAllTags(array $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->removeAllTags($publicIds, $options);
    }

    /**
     * Removes all tags from the assets specified.
     *
     * This is an asynchronous function.
     *
     */
    public function removeAllTagsAsync($publicIds = [], $options = []): PromiseInterface
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
    public function replaceTag(string $tag, array|string $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->replaceTag($tag, $publicIds, $options);
    }

    /**
     * Replaces all existing tags on the assets specified with the tag specified.
     *
     * This is an asynchronous function.
     *
     */
    public function replaceTagAsync($tag, $publicIds = [], $options = []): PromiseInterface
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
    public function generateSprite(string $tag, array $options = []): ApiResponse
    {
        return $this->uploadApi()->generateSprite($tag, $options);
    }

    /**
     * Creates a sprite from all images that have been assigned a specified tag.
     *
     * This is an asynchronous function.
     */
    public function generateSpriteAsync($tag, $options = []): PromiseInterface
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
    public function generatePDF($tag, $options = []): ApiResponse
    {
        $pdfOptions = array_merge($options, ['async' => false, 'format' => 'pdf']);

        return $this->uploadApi()->multi($tag, $pdfOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generatePDFAsync($tag, array $options = []): ApiResponse
    {
        $pdfOptions = array_merge($options, ['async' => true, 'format' => 'pdf']);

        return $this->uploadApi()->multi($tag, $pdfOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedGIF($tag, array $options = []): ApiResponse
    {
        $gifOptions = array_merge($options, ['async' => false, 'format' => 'gif']);

        return $this->uploadApi()->multi($tag, $gifOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedPNG($tag, array $options = []): ApiResponse
    {
        $pngOptions = array_merge($options, ['async' => false, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedPNGAsync($tag, array $options = []): ApiResponse
    {
        $pngOptions = array_merge($options, ['async' => true, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBP($tag, array $options = []): ApiResponse
    {
        $webpOptions = array_merge($options, ['async' => false, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBPAsync($tag, array $options = []): ApiResponse
    {
        $webpOptions = array_merge($options, ['async' => true, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedMP4($tag, array $options = []): ApiResponse
    {
        $mp4Options = array_merge($options, ['async' => false, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedMP4Async($tag, array $options = []): ApiResponse
    {
        $mp4Options = array_merge($options, ['async' => true, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBM($tag, array $options = []): ApiResponse
    {
        $webmOptions = array_merge($options, ['async' => false, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function generateAnimatedWEBMAsync($tag, array $options = []): ApiResponse
    {
        $webmOptions = array_merge($options, ['async' => true, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    /**
     * @param $tag
     * @param array $options
     * @return ApiResponse
     */
    public function multi($tag, array $options = []): ApiResponse
    {
        return $this->uploadApi()->multi($tag, $options);
    }

    /**
     * @param $tag
     * @param array $options
     * @return PromiseInterface
     */
    public function multiAsync($tag, array $options = []): PromiseInterface
    {
        return $this->uploadApi()->multiAsync($tag, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return ApiResponse
     */
    public function explode($publicId, array $options = []): ApiResponse
    {
        return $this->uploadApi()->explode($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function explodeAsync($publicId, array $options = []): PromiseInterface
    {
        return $this->uploadApi()->explodeAsync($publicId, $options);
    }

    /**
     * Dynamically generates an image from a given textual string.
     *
     * @param string $text The text string to generate an image for.
     * @param array $options The optional parameters.  See the upload API documentation.
     *
     * @return CloudinaryEngine
     *
     * @see https://cloudinary.com/documentation/image_upload_api_reference#text_method
     */
    public function generateImageFromText(string $text, array $options = []): static
    {
        $this->response = $this->uploadApi()->text($text, $options);

        return $this;
    }

    /**
     * @param $text
     * @param array $options
     * @return PromiseInterface
     */
    public function generateImageFromTextAsync($text, array $options = []): PromiseInterface
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
    public function createArchive(array $options = [], $targetFormat = null): ApiResponse
    {
        return $this->uploadApi()->createArchive($options, $targetFormat);
    }

    /**
     * @param array $options
     * @param null $targetFormat
     * @return PromiseInterface
     */
    public function createArchiveAsync(array $options = [], $targetFormat = null): PromiseInterface
    {
        return $this->uploadApi()->createArchiveAsync($options, $targetFormat);
    }

    /**
     * @param array $options
     * @return ApiResponse
     */
    public function createZip(array $options = []): ApiResponse
    {
        return $this->uploadApi()->createZip($options);
    }

    /**
     * @param array $options
     * @return PromiseInterface
     */
    public function createZipAsync(array $options = []): PromiseInterface
    {
        return $this->uploadApi()->createZipAsync($options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function downloadZipUrl(array $options = []): string
    {
        return $this->uploadApi()->downloadZipUrl($options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function downloadArchiveUrl(array $options = []): string
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
    public function addContext($context, array $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->addContext($context, $publicIds, $options);
    }

    /**
     * @param $context
     * @param array $publicIds
     * @param array $options
     * @return PromiseInterface
     */
    public function addContextAsync($context, array $publicIds = [], array $options = []): PromiseInterface
    {
        return $this->uploadApi()->addContextAsync($context, $publicIds, $options);
    }

    /**
     * @param array $publicIds
     * @param array $options
     * @return ApiResponse
     */
    public function removeAllContext(array $publicIds = [], array $options = []): ApiResponse
    {
        return $this->uploadApi()->removeAllContext($publicIds, $options);
    }

    /**
     * @param array $publicIds
     * @param array $options
     * @return PromiseInterface
     */
    public function removeAllContextAsync(array $publicIds = [], array $options = []): PromiseInterface
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
     * @return array|ApiResponse
     */
    public function destroy($publicId, array $options = []): array|ApiResponse
    {
        return $this->uploadApi()->destroy($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function destroyAsync($publicId, array $options = []): PromiseInterface
    {
        return $this->uploadApi()->destroyAsync($publicId, $options);
    }

    /**
     * @param $from
     * @param $to
     * @param array $options
     * @return mixed
     */
    public function rename($from, $to, array $options = []): mixed
    {
        return $this->uploadApi()->rename($from, $to, $options);
    }

    /**
     * @param $from
     * @param $to
     * @param array $options
     * @return PromiseInterface
     */
    public function renameAsync($from, $to, array $options = []): PromiseInterface
    {
        return $this->uploadApi()->renameAsync($from, $to, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return mixed
     */
    public function explicit($publicId, array $options = []): mixed
    {
        return $this->uploadApi()->explicit($publicId, $options);
    }

    /**
     * @param $publicId
     * @param array $options
     * @return PromiseInterface
     */
    public function explicitAsync($publicId, array $options = []): PromiseInterface
    {
        return $this->uploadApi()->explicitAsync($publicId, $options);
    }

    /**
     * Get Resource data
     * @param string $path
     * @return ApiResponse|string;
     */
    public function getResource(string $path): string|ApiResponse
    {
        try {
            return $this->admin()->asset($path);
        } catch (Exception) {
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
    public function getUrl(string $publicId): bool|string
    {

        $resource = $this->getResource($publicId);
        return $resource['secure_url'] ?? '';
    }
}
