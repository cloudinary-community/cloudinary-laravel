<?php

namespace CloudinaryLabs\CloudinaryLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

/**
 * Class Cloudinary
 * @package CloudinaryLabs\CloudinaryLaravel\Facades
 */
class Cloudinary extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return CloudinaryEngine::class;
    }
}
