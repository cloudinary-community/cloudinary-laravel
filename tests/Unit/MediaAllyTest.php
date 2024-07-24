<?php

use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use Tests\Fixtures\Models\Example;

beforeEach(function () {
    $this->mock('overload:' . Cloudinary\Api\Upload\UploadApi::class, function (MockInterface $mock) {
        $mock->shouldReceive('upload')->andReturn([
            'public_id' => 'file',
            'bytes' => '123',
            'secure_url' => 'https://example.com/file.jpg',
            'resource_type' => 'image',
        ]);

        $mock->shouldReceive('destroy')->andReturn([
            'result' => 'ok',
        ]);
    })->makePartial();
});

it('can attach media to a model', function () {
    $example = Example::create([]);

    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));


    expect($example->fetchAllMedia())->toHaveCount(1);
});

it('can delete one media from a model', function () {

    $example = Example::create([]);

    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($example->fetchAllMedia())->toHaveCount(2);

    $media = $example->fetchAllMedia()->first();
    $example->detachMedia($media);

    expect($example->fetchAllMedia())->toHaveCount(1);
});

it('can delete multiple media from a model', function () {

    $example = Example::create([]);

    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($example->fetchAllMedia())->toHaveCount(3);

    $media = $example->fetchAllMedia()->slice(0, 2);
    $example->detachMedia($media);

    expect($example->fetchAllMedia())->toHaveCount(1);
});

it('can delete all media from a model', function () {

    $example = Example::create([]);

    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $example->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($example->fetchAllMedia())->toHaveCount(3);

    $example->detachMedia();

    expect($example->fetchAllMedia())->toHaveCount(0);
});
