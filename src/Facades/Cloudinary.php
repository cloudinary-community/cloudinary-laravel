<?php

namespace CloudinaryLabs\Facades;

use Illuminate\Support\Facades\Facade;
use CloudinaryLabs\CloudinaryEngine;

/**
 * Class Cloudinary
 * @package CloudinaryLabs\Facades
 */
class Cloudinary extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CloudinaryEngine::class;
    }
}
