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

it('removes extensions from media resources but not raw resources', function ($actual, $expected) {
    $adapter = Storage::disk('cloudinary')->getAdapter();

    expect($adapter->preparePublicId($actual))->toBe($expected);
})->with([
    ['file.jpg', 'file'],
    ['file.png', 'file'],
    ['file.gif', 'file'],
    ['file.xlsx', 'file.xlsx'],
    ['file.zip', 'file.zip'],
    ['file.csv', 'file.csv'],
]);
