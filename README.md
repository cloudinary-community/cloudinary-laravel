<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://user-images.githubusercontent.com/62209650/196528621-b68e9e10-7e55-4c7d-9177-904cadbb4296.png" align="center" height=50>
  <source media="(prefers-color-scheme: light)" srcset="https://user-images.githubusercontent.com/62209650/196528761-a815025a-271a-4d8e-ac7e-cea833728bf9.png" align="center" height=50>
  <img alt="Cloudinary" src="https://user-images.githubusercontent.com/62209650/196528761-a815025a-271a-4d8e-ac7e-cea833728bf9.png" align="center" height=50>
</picture>
&ensp;&ensp;
<picture style="padding: 50px">
  <source media="(prefers-color-scheme: dark)" srcset="https://user-images.githubusercontent.com/1045274/200928533-47539867-07ff-406e-aa8b-25c594652dc8.png" align="center" height=50>
  <source media="(prefers-color-scheme: light)" srcset="https://user-images.githubusercontent.com/1045274/200928533-47539867-07ff-406e-aa8b-25c594652dc8.png" align="center" height=50>
  <img alt="Laravel" src="https://user-images.githubusercontent.com/1045274/200928533-47539867-07ff-406e-aa8b-25c594652dc8.png" align="center" height=50>
</picture>

######

<a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel"><img src="https://img.shields.io/packagist/dt/cloudinary-labs/cloudinary-laravel.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel"><img src="https://poser.pugx.org/cloudinary-labs/cloudinary-laravel/v/stable.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://github.com/cloudinary-devs/cloudinary-laravel/blob/main/LICENSE"><img alt="GitHub" src="https://img.shields.io/github/license/cloudinary-devs/cloudinary-laravel?label=License&style=flat-square"></a>

> A Laravel Package for uploading, optimizing, transforming and delivering media files with Cloudinary. Furthermore, it provides a fluent and expressive API to easily attach your media files to Eloquent models.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Upload, Retrieval & Transformation](#upload-retrieval--transformation)
- [Attach Files to Eloquent Models](#attach-files-to-eloquent-models)
- [Media Management via CLI](#media-management-via-cli)
- [Cloudinary URL Generation](#cloudinary-url-generation)
- [Blade Components](#blade-components)
- [Disclaimer](#disclaimer)
- [Contributions](#contributions)
- [License](#license)

## Installation

Requires PHP 8.1+ and Laravel 10+.

```bash
composer require cloudinary-labs/cloudinary-laravel
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config"
```

Add your Cloudinary credentials to your `.env` file:

```
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_UPLOAD_PRESET=your_upload_preset
CLOUDINARY_NOTIFICATION_URL=
```

> [!NOTE]  
> You can get your `CLOUDINARY_URL` from your [Cloudinary console](https://cloudinary.com/console). It typically looks like this: `cloudinary://API_KEY:API_SECRET@CLOUD_NAME`. Make sure to replace `API_KEY`, `API_SECRET`, and `CLOUD_NAME` with your actual Cloudinary credentials.

## Usage

### Upload, Retrieval & Transformation

```php
// Upload
$uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

// Upload with transformation
$uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath(), [
    'folder' => 'uploads',
    'transformation' => [
        'width' => 400,
        'height' => 400,
        'crop' => 'fill'
    ]
])->getSecurePath();

// Get URL
$url = cloudinary()->getUrl($publicId);

// Check if file exists
$exists = Storage::disk('cloudinary')->fileExists($publicId);
```

### Attach Files to Eloquent Models

First, add the `MediaAlly` trait to your model:

```php
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

class Page extends Model
{
    use MediaAlly;
    // ...
}
```

Then, you can use the following methods:

```php
// Attach media
$page->attachMedia($request->file('file'));

// Retrieve media
$allMedia = $page->fetchAllMedia();
$firstMedia = $page->fetchFirstMedia();
$lastMedia = $page->fetchLastMedia();

// Update media
$page->updateMedia($newFile);

// Detach media
$page->detachMedia($file);
```

### Media Management via CLI

```bash
php artisan cloudinary:backup
php artisan cloudinary:delete
php artisan cloudinary:fetch
php artisan cloudinary:rename
php artisan cloudinary:upload
```

## Cloudinary URL Generation

Use the `Cloudinary` facade or the `cloudinary()` helper function to generate URLs:

```php
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

$url = Cloudinary::getUrl($publicId);
// or
$url = cloudinary()->getUrl($publicId);
```

## Blade Components

This package provides several Blade components for easy integration of Cloudinary media in your Laravel views

### Upload files via an Upload Widget

You can use the `<x-cld-upload-button />` Blade component that ships with this page like so:

```blade
<!DOCTYPE html>
<html>
  <head>
    ... @cloudinaryJS
  </head>
  <body>
    <x-cld-upload-button>Upload Files</x-cld-upload-button>
  </body>
</html>
```

### Image Component

Basic usage:

```blade
<x-cld-image public-id="example" />
```

With additional properties:

```blade
<x-cld-image public-id="example" width="300" height="300" />
```

#### Properties available:

| Property              | Required |
| --------------------- | -------- |
| `public-id`           | Yes      |
| `width`               | No       |
| `height`              | No       |
| `alt`                 | No       |
| `class`               | No       |
| `crop`                | No       |
| `gravity`             | No       |
| `effect`              | No       |
| `rotate`              | No       |
| `colorize`            | No       |
| `trim`                | No       |
| `blur`                | No       |
| `gray-scale`          | No       |
| `black-white`         | No       |
| `sepia`               | No       |
| `redeye`              | No       |
| `negate`              | No       |
| `oil-paint`           | No       |
| `vignette`            | No       |
| `simulate-colorblind` | No       |
| `pixelate`            | No       |
| `unsharp-mask`        | No       |
| `saturation`          | No       |
| `contrast`            | No       |
| `brightness`          | No       |
| `gamma`               | No       |
| `improve-mode`        | No       |
| `shadow`              | No       |
| `border`              | No       |
| `round-corners`       | No       |
| `bg-color`            | No       |
| `art`                 | No       |
| `cartoonify`          | No       |

### Video Component

Basic usage:

```blade
<x-cld-video public-id="example"></x-cld-video>
```

With additional properties:

```blade
<x-cld-video public-id="example" width="300" height="300" />
```

#### Properties available:

| Property    | Required |
| ----------- | -------- |
| `public-id` | Yes      |
| `width`     | No       |
| `height`    | No       |

## Disclaimer

> _This software/code provided under Cloudinary Labs is an unsupported pre-production prototype undergoing further development and provided on an “AS IS” basis without warranty of any kind, express or implied, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. Furthermore, Cloudinary is not under any obligation to provide a commercial version of the software.</br> </br> Your use of the Software/code is at your sole risk and Cloudinary will not be liable for any direct, indirect, incidental, special, exemplary, consequential or similar damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of the Software, even if advised of the possibility of such damage.</br> </br> You should refrain from uploading any confidential or personally identifiable information to the Software. All rights to and in the Software are and will remain at all times, owned by, or licensed to Cloudinary._

## Contributions

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
