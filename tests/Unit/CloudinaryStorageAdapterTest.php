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

function createApiResponse(array $data, int $statusCode = 200): ApiResponse
{
    return new ApiResponse($data, ['headers' => [], 'statusCode' => $statusCode]);
}

beforeEach(function () {
    $this->cloudinary = $this->prophesize(Cloudinary::class);
    $this->uploadApi = $this->prophesize(UploadApi::class);
    $this->adminApi = $this->prophesize(AdminApi::class);

    $this->cloudinary->uploadApi()->willReturn($this->uploadApi->reveal());
    $this->cloudinary->adminApi()->willReturn($this->adminApi->reveal());

    $this->adapter = new CloudinaryStorageAdapter($this->cloudinary->reveal());

    // Also create an adapter instance configured with a prefix for tests
    // that assert prefix behavior.
    $this->prefixedAdapter = new CloudinaryStorageAdapter($this->cloudinary->reveal(), null, 'Fixtures');
});

it('can copy a file', function () {
    $this->uploadApi->explicit(
        Argument::exact('./source'),
        Argument::any(),
    )->willReturn(createApiResponse(['public_id' => 'destination']))->shouldBeCalled();

    $this->adapter->copy('source.jpg', 'destination.jpg', new Config);
});

it('can delete a file', function () {
    $this->uploadApi->destroy(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse(['result' => 'ok']))->shouldBeCalled();

    $this->adapter->delete('Fixtures/test-file.jpg');
});

it('throws exception on delete failure', function () {
    $this->uploadApi->destroy(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse(['result' => 'error', 'error' => 'Failed']))->shouldBeCalled();

    expect(fn () => $this->adapter->delete('Fixtures/test-file.jpg'))
        ->toThrow(UnableToDeleteFile::class);
});

it('can delete a directory', function () {
    $this->adminApi->deleteAssetsByPrefix(
        Argument::exact('Fixtures/test-dir')
    )->willReturn(createApiResponse(['result' => 'ok']))->shouldBeCalled();

    $this->adapter->deleteDirectory('Fixtures/test-dir');
});

it('can check if file exists', function () {
    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse([
        'public_id' => 'Fixtures/test-file',
        'bytes' => 1234,
        'secure_url' => 'https://example.com/test-file',
    ]))->shouldBeCalled();

    expect($this->adapter->fileExists('Fixtures/test-file.jpg'))->toBeTrue();
});

it('handles non-existent files', function () {
    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willThrow(new Exception('Not found'))->shouldBeCalled();

    expect($this->adapter->fileExists('Fixtures/test-file.jpg'))->toBeFalse();
});

it('can get file size', function () {
    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse([
        'public_id' => 'Fixtures/test-file',
        'bytes' => 1234,
        'secure_url' => 'https://example.com/test-file',
    ]))->shouldBeCalled();

    $size = $this->adapter->fileSize('Fixtures/test-file.jpg');
    expect($size->fileSize())->toBe(1234);
});

it('can get last modified time', function () {
    $date = '2023-01-01T00:00:00Z';
    $expectedTimestamp = strtotime($date);

    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse([
        'public_id' => 'Fixtures/test-file',
        'created_at' => $date,
        'bytes' => 1234,
    ]))->shouldBeCalled();

    $time = $this->adapter->lastModified('Fixtures/test-file.jpg');
    expect($time->lastModified())->toBe($expectedTimestamp);
});

it('can list contents', function () {
    $response = createApiResponse([
        'resources' => [
            [
                'public_id' => 'Fixtures/test-file',
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

it('applies configured prefix when listing contents', function () {
    $response = createApiResponse([
        'resources' => [],
    ]);

    $this->adminApi->assets(Argument::that(function ($options) {
        return $options['type'] === 'upload'
            && $options['prefix'] === 'Fixtures/test-dir'
            && $options['max_results'] === 500;
    }))->willReturn($response)->shouldBeCalled();

    $contents = $this->prefixedAdapter->listContents('test-dir', false);
    expect(iterator_to_array($contents))->toHaveCount(0);
});

it('can read a file', function () {
    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse([
        'secure_url' => __DIR__.'/test.jpg',
    ]))->shouldBeCalled();

    // Create a test file
    file_put_contents(__DIR__.'/test.jpg', 'test content');

    $content = $this->adapter->read('Fixtures/test-file.jpg');
    expect($content)->toBe('test content');

    unlink(__DIR__.'/test.jpg');
});

it('can read a file as stream', function () {
    $this->adminApi->asset(
        Argument::exact('Fixtures/test-file'),
        Argument::any()
    )->willReturn(createApiResponse([
        'secure_url' => __DIR__.'/test.jpg',
    ]))->shouldBeCalled();

    // Create a test file
    file_put_contents(__DIR__.'/test.jpg', 'test content');

    $stream = $this->adapter->readStream('Fixtures/test-file.jpg');
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
        Argument::exact('Fixtures/source'),
        Argument::any(),
    )->willReturn(createApiResponse(['public_id' => 'destination']))->shouldBeCalled();

    $this->uploadApi->destroy(
        Argument::exact('Fixtures/source'),
        Argument::any()
    )->willReturn(createApiResponse(['result' => 'ok']))->shouldBeCalled();

    $this->adapter->move('Fixtures/source.jpg', 'Fixtures/destination.jpg', new Config);
});

it('can calculate checksum', function () {
    //
})->todo();
