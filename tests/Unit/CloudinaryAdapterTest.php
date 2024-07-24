<?php

use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

it('can get url given public id', function () {
    $file = 'baz.jpg';
    $url = 'https://res.cloudinary.com/foo/image/upload/bar/' . $file;

    $this->mock('overload:' . Cloudinary\Api\Admin\AdminApi::class, function (MockInterface $mock) use ($url) {
        $mock->shouldReceive('asset')->once()->andReturn(['secure_url' => $url]);
    })->makePartial();

    $result = Storage::disk('cloudinary')->url($file);
    expect($result)->toEqual($url);
});
