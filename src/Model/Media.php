<?php

namespace CloudinaryLabs\CloudinaryLaravel\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

/**
 * Class Media
 * @package CloudinaryLabs\CloudinaryLaravel\Model
 */
class Media extends Model
{

    protected $table = 'media';

    /**
     * Create the polymorphic relation.
     *
     * @return MorphTo
     */
    public function medially()
    {
        return $this->morphTo();
    }

    /**
     * Get the file url / path of a Media File
     * @return string
     */
    public function getSecurePath()
    {
        return $this->file_url;
    }

    /**
     * Get the file name of a Media File
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Get the mime type of a Media File
     * @return string
     */
    public function getFileType()
    {
        return $this->file_type;
    }

    /**
     * Get the Size of a Media File
     * @return Integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the Readable Size of a Media File
     * @return string
     */
    public function getReadableSize()
    {
        return resolve(CloudinaryEngine::class)->getHumanReadableSize($this->size);
    }
}
