<?php

use Illuminate\Http\UploadedFile;
use Tests\Fixtures\MyModel;

it('can attach media to a model', function () {

    $model = MyModel::create([]);

    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($model->fetchAllMedia())->toHaveCount(1);
});

it('can delete one media from a model', function () {

    $model = MyModel::create([]);

    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($model->fetchAllMedia())->toHaveCount(2);

    $media1 = $model->fetchAllMedia()->first();
    $model->detachMedia($media1);

    expect($model->fetchAllMedia())->toHaveCount(1);
});

it('can delete multiple media from a model', function () {

    $model = MyModel::create([]);

    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($model->fetchAllMedia())->toHaveCount(3);

    $multiple_media = $model->fetchAllMedia()->slice(0, 2);
    $model->detachMedia($multiple_media);

    expect($model->fetchAllMedia())->toHaveCount(1);
});

it('can delete all media from a model', function () {

    $model = MyModel::create([]);

    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));
    $model->attachMedia(UploadedFile::fake()->image('file.jpg'));

    expect($model->fetchAllMedia())->toHaveCount(3);

    $model->detachMedia();

    expect($model->fetchAllMedia())->toHaveCount(0);
});
