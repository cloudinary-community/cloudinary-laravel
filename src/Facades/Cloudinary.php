<?php

namespace CloudinaryLabs\CloudinaryLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Cloudinary
 */
class Cloudinary extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Cloudinary\Cloudinary::class;
    }
}
