<?php

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

if (!function_exists("cloudinary")) {

    /**
     * @return CloudinaryEngine
     */
    function cloudinary(): CloudinaryEngine
    {
        return app(CloudinaryEngine::class);
    }
}
