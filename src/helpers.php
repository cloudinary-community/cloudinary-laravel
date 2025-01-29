<?php

use Cloudinary\Cloudinary;

if (! function_exists('cloudinary')) {

    function cloudinary(): Cloudinary
    {
        return app(Cloudinary::class);
    }
}
