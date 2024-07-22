<?php

use Illuminate\Support\Facades\Storage;

it('can get url given public id', function () {
    expect(Storage::disk('cloudinary')->url(basename(env('TEST_FILE_URL'))))->toEqual(env('TEST_FILE_URL'));
});
