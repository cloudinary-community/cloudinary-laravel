<?php

namespace Unicodeveloper\Cloudinary\Model;

use Illuminate\Support\Facades\Facade;
use Unicodeveloper\Cloudinary\CloudinaryEngine;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Media extends Model
{

    protected $table = 'media';

    /**
    * Create the polymorphic relation.
    *
    * @return \Illuminate\Database\Eloquent\Relations\MorphTo
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
