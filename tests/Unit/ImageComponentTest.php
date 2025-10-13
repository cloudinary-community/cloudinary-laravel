<?php

beforeEach(function () {
    $this->app->instance(
        \Cloudinary\Cloudinary::class,
        new \Cloudinary\Cloudinary('cloudinary://key:secret@demo')
    );
});

it('renders image component with both class and alt attributes', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'class' => 'img-fluid',
        'alt' => 'Parallax Background Image',
    ])->render();

    expect($html)->toContain('class="img-fluid"');
    expect($html)->toContain('alt="Parallax Background Image"');
});

it('includes crop, width and height transformation when provided', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'crop' => 'fill',
        'width' => 300,
        'height' => 200,
    ])->render();

    expect($html)->toContain('w_');
    expect($html)->toContain('h_');
    expect($html)->toContain('c_');
});

it('includes rotation when rotate attribute is provided', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'rotate' => 90,
    ])->render();

    expect($html)->toContain('a_');
});

it('applies grayscale effect when requested', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'grayscale' => true,
    ])->render();

    expect($html)->toContain('e_grayscale');
});

it('applies round corners when requested', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'roundCorners' => true,
    ])->render();

    expect($html)->toContain('r_');
});

it('preserves multiple classes when provided', function () {
    $html = view('cloudinary::components.image', [
        'publicId' => 'example',
        'class' => 'img-fluid rounded mx-auto',
    ])->render();

    expect($html)->toContain('class="img-fluid rounded mx-auto"');
});
