<?php

use Unicodeveloper\Cloudinary\CloudinaryEngine;

if (! function_exists("cloudinary"))
{
    function cloudinary() {
        return app(CloudinaryEngine::class);
    }
}