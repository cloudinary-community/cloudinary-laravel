<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

/**
 *
 */
class DetachMediaTest extends TestCase
{
    public function test_can_detach_one_media_or_all()
    {
        $this->artisan('migrate')->run();

        $model = MyModel::create([]);

        $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
        $model->attachMedia(UploadedFile::fake()->image('file.jpg'));

        $this->assertCount(2, $model->fetchAllMedia());

        $media1 = $model->fetchAllMedia()->first();
        $model->detachMedia($media1);

        $this->assertCount(1, $model->fetchAllMedia());

        $model->detachMedia();
        $this->assertCount(0, $model->fetchAllMedia());
    }
}

/**
 *
 */
class MyModel extends Model
{
    protected $table = 'model';

    use MediaAlly;
}
