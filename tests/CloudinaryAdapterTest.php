<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider;

class CloudinaryAdapterTest extends TestCase
{

    protected function getEnvironmentSetup($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);

        $app['config']->set('cloudinary.cloud_url', env('CLOUDINARY_URL'));
    }

    protected function getPackageProviders($app)
    {
        return CloudinaryServiceProvider::class;
    }

    public function test_can_get_url_given_public_id()
    {
        //file already exists
        $this->assertEquals(env('TEST_FILE_URL'), Storage::disk('cloudinary')->url(basename(env('TEST_FILE_URL'))));
    }
}
