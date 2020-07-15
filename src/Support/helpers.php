<?php

use Illuminate\Contracts\Foundation\Application;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

if (!function_exists("cloudinary")) {

    /**
     * @return Application|mixed
     */
    function cloudinary()
    {
        return app(CloudinaryEngine::class);
    }
}
