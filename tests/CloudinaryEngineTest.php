<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Exception\ApiError;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Foundation\Testing\WithFaker;

class CloudinaryEngineTest extends TestCase
{
    use withFaker;

    public function setUp(): void
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

    /**
     * @throws ApiError
     */
    public function test_can_receive_a_cloudinary_response_object()
    {
        $this->withoutExceptionHandling();

        $engine = app(CloudinaryEngine::class);
        $response = $engine->admin()->asset("test");

        $engine->withResponse($response);

        $this->assertInstanceOf(ApiResponse::class, $engine->getResponse());
    }
}
