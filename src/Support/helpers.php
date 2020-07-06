<?php

use Illuminate\Contracts\Foundation\Application;
use Unicodeveloper\Cloudinary\CloudinaryEngine;

if (!function_exists("cloudinary")) {
    /**
     * @return Application|mixed
     */
    function cloudinary()
    {
        return app(CloudinaryEngine::class);
    }
}
