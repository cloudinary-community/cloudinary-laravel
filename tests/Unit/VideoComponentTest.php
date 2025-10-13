<?php

beforeEach(function () {
    $this->app->instance(
        \Cloudinary\Cloudinary::class,
        new \Cloudinary\Cloudinary('cloudinary://key:secret@demo')
    );
});

it('renders video component with default attributes and method', function () {
    $html = view('cloudinary::components.video', [
        'publicId' => 'example',
        'width' => 640,
        'height' => 360,
    ])->render();

    expect($html)->toContain('controls');
    expect($html)->toContain('preload');
    expect($html)->toContain('src="');
});
