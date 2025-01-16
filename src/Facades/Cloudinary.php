<?php

namespace CloudinaryLabs\CloudinaryLaravel\Facades;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Support\Facades\Facade;

/**
 * Class Cloudinary
 */
class Cloudinary extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CloudinaryEngine::class;
    }
}
