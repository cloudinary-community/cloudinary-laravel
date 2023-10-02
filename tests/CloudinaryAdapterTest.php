<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use Illuminate\Support\Facades\Storage;

/**
 *
 */
class CloudinaryAdapterTest extends TestCase
{
    public function test_can_get_url_given_public_id()
    {
        Storage::fake("cloudinary");

        Storage::disk("cloudinary")->put("file.jpg", "contents");

        Storage::disk("cloudinary")->assertExists("file.jpg");
    }
}
