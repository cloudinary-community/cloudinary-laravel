<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Support\Facades\Storage;

/**
 *
 */
class CloudinaryAdapterTest extends TestCase
{

    protected function setUp(): void
    {
       parent::setUp();
    }

    protected function getEnvironmentSetup($app)
    {
        $app['config']->set('cloudinary.cloud_url', env('CLOUDINARY_URL'));
    }

    protected function getPackageProviders($app)
    {
        return ['CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider'];
    }

    public function test_can_get_url_given_public_id()
    {
        //file already exists
        $this->assertEquals('https://res.cloudinary.com/dwinzyahj/image/upload/v1616774832/sample_qgqooj.jpg', Storage::disk('cloudinary')->url('sample_qgqooj.jpg'));
    }
}
