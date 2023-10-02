<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class MediaAllyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        imagejpeg(
            imagecreatetruecolor(100, 100),
            storage_path("app/public/test.jpg")
        );

        app(CloudinaryEngine::class)->uploadFile(
            storage_path("app/public/test.jpg"),
            [
                "public_id" => "test",
            ]
        );
    }

    protected function tearDown(): void
    {
        app(CloudinaryEngine::class)->destroy("test");

        parent::tearDown();
    }

    public function test_has_public_id_field_in_db()
    {
        $this->assertTrue(Schema::hasColumn("media", "public_id"));
    }

    public function test_can_attach_file_from_cloudinary()
    {
        $model = new class extends Model {
            use MediaAlly;
        };

        $model->id = 1;

        $model->attachCloudinaryMedia("test");

        $this->assertEquals("test", $model->fetchLastMedia()->public_id);
        $this->assertDatabaseHas("media", ["public_id" => "test"]);
        $this->assertEquals("test", $model->fetchLastMedia()->getPublicId());
    }
}
