<?php

/*
 * This file is part of the Laravel Cloudinary package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unicodeveloper\Cloudinary;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Config;
use Unicodeveloper\Cloudinary\Exceptions\IsNullException;

class CloudinaryEngine
{

    const ASSET_ID          = 'asset_id';
    const PUBLIC_ID         = 'public_id';
    const VERSION           = 'version';
    const VERSION_ID        = 'version_id';
    const SIGNATURE         = 'signature';
    const WIDTH             = 'width';
    const HEIGHT            = 'height';
    const FORMAT            = 'format';
    const RESOURCE_TYPE     = 'resource_type';
    const CREATED_AT        = 'created_at';
    const TAGS              = 'tags';
    const PAGES             = 'pages';
    const BYTES             = 'bytes';
    const TYPE              = 'type';
    const ETAG              = 'etag';
    const PLACEHOLDER       = 'placeholder';
    const URL               = 'url';
    const SECURE_URL        = 'secure_url';
    const PHASH             = 'phash';
    const ORIGINAL_FILENAME = 'original_filename';

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
    * @param string $file    The asset to upload.
    * @param array  $options The optional parameters. See the upload API documentation.
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
    * @param string $file         The asset to upload.
    * @param string $uploadPreset The name of an upload preset.
    * @param array  $options      The optional parameters. See the upload API documentation.
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
    * Uploads an asset to a Cloudinary account, not limited to images, but any type of file.
    */
    public function uploadFile($file, $options = [])
    {
        $uploadOptions = array_merge($options, ['resource_type' => 'auto']);

        $this->response = $this->uploadApi()->upload($file, $uploadOptions);

        return $this;
    }

    public function uploadVideo($file, $options = [])
    {
        $videoUploadOptions = array_merge($options, ['resource_type' => 'video']);

        $this->response = $this->uploadApi()->upload($file, $videoUploadOptions);

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

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

    public function getVersion()
    {
        return $this->response[self::VERSION];
    }

    public function getVersionId()
    {
        return $this->response[self::VERSION_ID];
    }

    public function getSignature()
    {
        return $this->response[self::SIGNATURE];
    }

    public function getWidth()
    {
        return $this->response[self::WIDTH];
    }

    public function getHeight()
    {
        return $this->response[self::HEIGHT];
    }

    public function getExtension()
    {
        return $this->response[self::FORMAT];
    }

    public function getFileType()
    {
        return $this->response[self::RESOURCE_TYPE];
    }

    public function getTimeUploaded()
    {
        return $this->response[self::CREATED_AT];
    }

    public function getTags()
    {
        return $this->response[self::TAGS];
    }

    public function getPages()
    {
        return $this->response[self::PAGES];
    }

    public function getSize()
    {
        return $this->response[self::BYTES];
    }

    public function getReadableSize()
    {
        return $this->getHumanReadableSize($this->getSize());
    }

    public function getPlaceHolder()
    {
        return $this->response[self::PLACEHOLDER];
    }

    public function getPath()
    {
        return $this->response[self::URL];
    }

    public function getSecurePath()
    {
        return $this->response[self::SECURE_URL];
    }

    public function getPhash()
    {
        return $this->response[self::PHASH];
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
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($sizeInBytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($sizeInBytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($sizeInBytes > 1) {
            return $sizeInBytes . ' bytes';
        } elseif ($sizeInBytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
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

    public function getImageTag($publicId)
    {
        return $this->cloudinary->imageTag($publicId);
    }

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
    * @param string $tag       The name of the tag to add.
    * @param array  $publicIds The public IDs of the assets to add the tag to.
    * @param array  $options   The optional parameters. See the upload API documentation.
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
     * @param string       $tag       The name of the tag to remove.
     * @param array|string $publicIds The public IDs of the assets to remove the tags from.
     * @param array        $options   The optional parameters. See the upload API documentation.
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
    * @param array $options   The optional parameters. See the upload API documentation.
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
    * @param string       $tag       The new tag with which to replace the existing tags.
    * @param array|string $publicIds The public IDs of the assets to replace the tags of.
    * @param array        $options   The optional parameters. See the upload API documentation.
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
    * @param string $tag     The tag that indicates which images to include in the sprite.
    * @param array  $options The optional parameters. See the upload API documentation.
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

    public function generatePDFAsync($tag, $options = [])
    {
        $pdfOptions = array_merge($options, ['async' => true, 'format' => 'pdf']);

        return $this->uploadApi()->multi($tag, $pdfOptions);
    }

    public function generateAnimatedGIF($tag, $options = [])
    {
        $gifOptions = array_merge($options, ['async' => false, 'format' => 'gif']);

        return $this->uploadApi()->multi($tag, $gifOptions);
    }

    public function generateAnimatedPNG($tag, $options = [])
    {
        $pngOptions = array_merge($options, ['async' => false, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    public function generateAnimatedPNGAsync($tag, $options = [])
    {
        $pngOptions = array_merge($options, ['async' => true, 'format' => 'png']);

        return $this->uploadApi()->multi($tag, $pngOptions);
    }

    public function generateAnimatedWEBP($tag, $options = [])
    {
        $webpOptions = array_merge($options, ['async' => false, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    public function generateAnimatedWEBPAsync($tag, $options = [])
    {
        $webpOptions = array_merge($options, ['async' => true, 'format' => 'webp']);

        return $this->uploadApi()->multi($tag, $webpOptions);
    }

    public function generateAnimatedMP4($tag, $options = [])
    {
        $mp4Options = array_merge($options, ['async' => false, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    public function generateAnimatedMP4Async($tag, $options = [])
    {
        $mp4Options = array_merge($options, ['async' => true, 'format' => 'mp4']);

        return $this->uploadApi()->multi($tag, $mp4Options);
    }

    public function generateAnimatedWEBM($tag, $options = [])
    {
        $webmOptions = array_merge($options, ['async' => false, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    public function generateAnimatedWEBMAsync($tag, $options = [])
    {
        $webmOptions = array_merge($options, ['async' => true, 'format' => 'webm']);

        return $this->uploadApi()->multi($tag, $webmOptions);
    }

    public function multi($tag, $options = [])
    {
        return $this->uploadApi()->multi($tag, $options);
    }

    public function multiAsync($tag, $options = [])
    {
        return $this->uploadApi()->multiAsync($tag, $options);
    }

    public function explode($publicId, $options = [])
    {
        return $this->uploadApi()->explode($publicId, $options);
    }

    public function explodeAsync($publicId, $options = [])
    {
        return $this->uploadApi()->explodeAsync($publicId, $options);
    }

    /**
    * Dynamically generates an image from a given textual string.
    *
    * @param string $text    The text string to generate an image for.
    * @param array  $options The optional parameters.  See the upload API documentation.
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

    public function generateImageFromTextAsync($text, $options = [])
    {
        return $this->uploadApi()->textAsync($text, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Zip & Archives
    |--------------------------------------------------------------------------
    */
    public function createArchive($options = [], $targetFormat = null)
    {
        return $this->uploadApi()->createArchive($options, $targetFormat);
    }

    public function createArchiveAsync($options = [], $targetFormat = null)
    {
        return $this->uploadApi()->createArchiveAsync($options, $targetFormat);
    }

    public function createZip($options = [])
    {
        return $this->uploadApi()->createZip($options);
    }

    public function createZipAsync($options = [])
    {
        return $this->uploadApi()->createZipAsync($options);
    }

    public function downloadZipUrl($options = [])
    {
        return $this->uploadApi()->downloadZipUrl($options);
    }

    public function downloadArchiveUrl($options = [])
    {
        return $this->uploadApi()->downloadArchiveUrl($options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Context
    |--------------------------------------------------------------------------
    */
    public function addContext($context, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addContext($context, $publicIds, $options);
    }

    public function addContextAsync($context, $publicIds = [], $options = [])
    {
        return $this->uploadApi()->addContextAsync($context, $publicIds, $options);
    }

    public function removeAllContext($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllContext($publicIds, $options);
    }

    public function removeAllContextAsync($publicIds = [], $options = [])
    {
        return $this->uploadApi()->removeAllContextAsync($publicIds, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Delete & Rename Assets
    |--------------------------------------------------------------------------
    */
    public function destroy($publicId, $options = [])
    {
        return $this->uploadApi()->destroy($publicId, $options);
    }

    public function destroyAsync($publicId, $options = [])
    {
       return $this->uploadApi()->destroyAsync($publicId, $options);
    }

    public function rename($from, $to, $options = [])
    {
       return $this->uploadApi()->rename($from, $to, $options);
    }

    public function renameAsync($from, $to, $options = [])
    {
       return $this->uploadApi()->renameAsync($from, $to, $options);
    }

    public function explicit($publicId, $options = [])
    {
       return $this->uploadApi()->explicit($publicId, $options);
    }

    public function explicitAsync($publicId, $options = [])
    {
       return $this->uploadApi()->explicitAsync($publicId, $options);
    }
}