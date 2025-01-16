<?php

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;

if (! function_exists('cloudinary')) {

    function cloudinary(): CloudinaryEngine
    {
        return app(CloudinaryEngine::class);
    }
}
