<?php

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryStorageAdapter;
use League\Flysystem\Config;
use League\Flysystem\UnableToDeleteFile;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

uses(ProphecyTrait::class);

function createApiResponse(array $data): ApiResponse
{
    return new ApiResponse($data, ['headers' => [], 'statusCode' => 200]);
}

beforeEach(function () {
    $this->cloudinary = $this->prophesize(Cloudinary::class);
    $this->uploadApi = $this->prophesize(UploadApi::class);
    $this->adminApi = $this->prophesize(AdminApi::class);

    $this->cloudinary->uploadApi()->willReturn($this->uploadApi->reveal());
    $this->cloudinary->adminApi()->willReturn($this->adminApi->reveal());

    $this->adapter = new CloudinaryStorageAdapter($this->cloudinary->reveal(), 'prefix');
});

it('can copy a file', function () {
    $this->uploadApi->explicit(
        Argument::exact('source'),
        Argument::that(fn ($args) => $args['public_id'] === 'destination' && $args['type'] === 'upload')
    )->willReturn(createApiResponse(['public_id' => 'destination']))->shouldBeCalled();

    $this->adapter->copy('source.jpg', 'destination.jpg', new Config);
});

it('can delete a file', function () {
    $this->uploadApi->destroy(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse(['result' => 'ok']))->shouldBeCalled();

    $this->adapter->delete('test-file.jpg');
});

it('throws exception on delete failure', function () {
    $this->uploadApi->destroy('test-file', ['resource_type' => 'image'])
        ->willReturn(createApiResponse(['result' => 'error', 'error' => 'Failed']))
        ->shouldBeCalled();

    expect(fn () => $this->adapter->delete('test-file.jpg'))
        ->toThrow(UnableToDeleteFile::class);
});

it('can delete a directory', function () {
    $this->adminApi->deleteAssetsByPrefix(
        Argument::exact('test-dir')
    )->willReturn(createApiResponse(['result' => 'ok']))->shouldBeCalled();

    $this->adapter->deleteDirectory('test-dir');
});

it('can check if file exists', function () {
    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse(['public_id' => 'test-file']))->shouldBeCalled();

    expect($this->adapter->fileExists('test-file.jpg'))->toBeTrue();
});

it('handles non-existent files', function () {
    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willThrow(new Exception('Not found'))->shouldBeCalled();

    expect($this->adapter->fileExists('test-file.jpg'))->toBeFalse();
});

it('can get file size', function () {
    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse(['bytes' => 1234]))->shouldBeCalled();

    $size = $this->adapter->fileSize('test-file.jpg');
    expect($size->fileSize())->toBe(1234);
});

it('can get last modified time', function () {
    $date = '2023-01-01T00:00:00Z';
    $expectedTimestamp = strtotime($date);

    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse([
        'public_id' => 'test-file',
        'created_at' => $date,
        'bytes' => 1234,
    ]))->shouldBeCalled();

    $time = $this->adapter->lastModified('test-file.jpg');
    expect($time->lastModified())->toBe($expectedTimestamp);
});

it('can list contents', function () {
    $response = createApiResponse([
        'resources' => [
            [
                'public_id' => 'test-file',
                'bytes' => 1234,
                'created_at' => '2023-01-01',
            ],
        ],
    ]);

    $this->adminApi->assets(Argument::that(function ($options) {
        return $options['type'] === 'upload'
            && $options['prefix'] === 'test-dir'
            && $options['max_results'] === 500;
    }))->willReturn($response)->shouldBeCalled();

    $contents = $this->adapter->listContents('test-dir', false);
    expect(iterator_to_array($contents))->toHaveCount(1);
});

it('can read a file', function () {
    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse([
        'secure_url' => __DIR__.'/test.jpg',
    ]))->shouldBeCalled();

    // Create a test file
    file_put_contents(__DIR__.'/test.jpg', 'test content');

    $content = $this->adapter->read('test-file.jpg');
    expect($content)->toBe('test content');

    unlink(__DIR__.'/test.jpg');
});

it('can read a file as stream', function () {
    $this->adminApi->asset(
        Argument::exact('test-file'),
        Argument::exact(['resource_type' => 'image'])
    )->willReturn(createApiResponse([
        'secure_url' => __DIR__.'/test.jpg',
    ]))->shouldBeCalled();

    // Create a test file
    file_put_contents(__DIR__.'/test.jpg', 'test content');

    $stream = $this->adapter->readStream('test-file.jpg');
    expect($stream)->toBeResource();
    expect(stream_get_contents($stream))->toBe('test content');

    unlink(__DIR__.'/test.jpg');
});

it('throws exception on visibility set', function () {
    expect(fn () => $this->adapter->setVisibility('test.jpg', 'public'))
        ->toThrow('Cloudinary does not support visibility');
});

it('can move a file', function () {
    $this->uploadApi->explicit(
        Argument::exact('source'),
        Argument::that(fn ($args) => $args['public_id'] === 'destination' && $args['type'] === 'upload')
    )->willReturn(createApiResponse(['public_id' => 'destination']))->shouldBeCalled();

    $this->uploadApi->destroy('source', ['resource_type' => 'image'])
        ->willReturn(createApiResponse(['result' => 'ok']))
        ->shouldBeCalled();

    $this->adapter->move('source.jpg', 'destination.jpg', new Config);
});

it('can calculate checksum', function () {
    //
})->todo();
