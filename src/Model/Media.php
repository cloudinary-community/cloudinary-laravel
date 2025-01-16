<?php

namespace CloudinaryLabs\CloudinaryLaravel\Model;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Media
 */
class Media extends Model
{
    protected $table = 'media';

    /**
     * Create the polymorphic relation.
     */
    public function medially(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the file url / path of a Media File
     */
    public function getSecurePath(): string
    {
        return $this->file_url;
    }

    /**
     * Get the file name of a Media File
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * Get the mime type of a Media File
     */
    public function getFileType(): string
    {
        return $this->file_type;
    }

    /**
     * Get the Size of a Media File
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the Readable Size of a Media File
     */
    public function getReadableSize(): string
    {
        return resolve(CloudinaryEngine::class)->getHumanReadableSize($this->size);
    }
}
