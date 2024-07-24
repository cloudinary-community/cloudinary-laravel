<?php

use Illuminate\Http\UploadedFile;
use Tests\Fixtures\Models\Example;

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
