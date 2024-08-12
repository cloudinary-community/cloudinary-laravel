<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Cloudinary\Api\Exception\ApiError;
use Exception;
use CloudinaryLabs\CloudinaryLaravel\Model\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * MediaAlly
 *
 * Provides functionality for attaching Cloudinary files to an eloquent model.
 * Whether the model should automatically reload its media relationship after modification.
 *
 */
trait MediaAlly
{

    /**
     * Relationship for all attached media.
     */
    public function medially(): MorphMany
    {
        return $this->morphMany(Media::class, 'medially');
    }


    /**
     * Attach Media Files to a Model
     * @throws Exception
     */
    public function attachMedia($file, $options = []): void
    {
        if(!$file instanceof UploadedFile) {
            throw new Exception('Please pass in a file that exists');
        }

        $response = resolve(CloudinaryEngine::class)->uploadFile($file->getRealPath(), $options);

        $media = new Media();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();

        $this->medially()->save($media);
    }

    /**
     * Attach Rwmote Media Files to a Model
     * @throws ApiError
     */
    public function attachRemoteMedia($remoteFile, $options = []): void
    {
        $response = resolve(CloudinaryEngine::class)->uploadFile($remoteFile, $options);

        $media = new Media();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();

        $this->medially()->save($media);
    }

    /**
    * Get all the Media files relating to a particular Model record
    */
    public function fetchAllMedia(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->medially()->get();
    }

    /**
    * Get the first Media file relating to a particular Model record
    */
    public function fetchFirstMedia()
    {
        return $this->medially()->first();
    }

    /**
     * Delete all/one/multiple file(s) associated with a particular Model record
     *
     * @param Media|Collection|null $media
     * @return void
     */
    public function detachMedia(Media|Collection $media = null): void
    {
        $items = [];

        if (is_null($media)) {
            $items = $this->medially()->get();
        } elseif ($media instanceof Media) {
            $items = new Collection([$this->medially()->find($media->id)]);
        } elseif ($media instanceof Collection) {
            $items = $this->medially()->whereIn('id', $media->pluck('id'))->get();
        }

        foreach($items as $item) {
            resolve(CloudinaryEngine::class)->destroy($item->getFileName());
            $item->delete();
        }
    }

    /**
    * Get the last Media file relating to a particular Model record
    */
    public function fetchLastMedia()
    {
        return $this->medially()->get()->last();
    }

    /**
     * Update the Media files relating to a particular Model record
     * @throws Exception
     */
    public function updateMedia($file, $options = []): void
    {
        $this->detachMedia();
        $this->attachMedia($file, $options);
    }

    /**
     * Update the Media files relating to a particular Model record (Specificially existing remote files)
     * @throws ApiError
     */
    public function updateRemoteMedia($file, $options = []): void
    {
        $this->detachMedia();
        $this->attachRemoteMedia($file, $options);
    }

}
