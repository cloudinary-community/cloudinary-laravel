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
- [Usage](#usage)
  - [File Storage Driver](#file-storage-driver)
  - [Blade Components](#blade-components)
- [Disclaimer](#disclaimer)
- [Contributions](#contributions)
- [License](#license)

## Installation

Requires PHP 8.2+ and Laravel 11+.

```bash
composer require cloudinary-labs/cloudinary-laravel
```

After you have installed the SDK, you can invoke the install command to set everything up:

```bash
php artisan cloudinary:install
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

### File Storage Driver

This SDK implements the [File Storage](https://laravel.com/docs/12.x/filesystem#main-content) Driver interface allowing you to use it as just another storage destination like s3, azure or local disk.

Add a new `cloudinary` key to your `config/filesystems.php` disk key like so:

```php
  ...,
  'cloudinary' => [
      'driver' => 'cloudinary',
      'key' => env('CLOUDINARY_KEY'),
      'secret' => env('CLOUDINARY_SECRET'),
      'cloud' => env('CLOUDINARY_CLOUD_NAME'),
      'url' => env('CLOUDINARY_URL'),
      'secure' => (bool) env('CLOUDINARY_SECURE', true),
      'prefix' => env('CLOUDINARY_PREFIX'),
  ],
...,
```

### Blade Components

This package provides a few Blade components for easy integration of Cloudinary media in your Laravel views.

#### Upload Widget

You can use the `<x-cloudinary::widget />` Blade component that ships with this like so:

```blade
<!DOCTYPE html>
<html>
  <body>
    <x-cloudinary::widget>Upload Files</x-cloudinary::widget>
  </body>
</html>
```

#### Image Component

Basic usage:

```blade
<x-cloudinary::image public-id="example" />
```

With additional properties:

```blade
<x-cloudinary::image public-id="example" width="300" height="300" />
```

##### Properties available:

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

#### Video Component

Basic usage:

```blade
<x-cloudinary::video public-id="example" />
```

With additional properties:

```blade
<x-cloudinary::video public-id="example" width="300" height="300" />
```

##### Properties available:

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
