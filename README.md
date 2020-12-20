<div align="center">
    <h1> Cloudinary Laravel Package</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel">
        <img src="https://img.shields.io/packagist/dt/cloudinary-labs/cloudinary-laravel.svg?style=flat-square" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel">
        <img src="https://poser.pugx.org/cloudinary-labs/cloudinary-laravel/v/stable.svg" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel">
        <img src="https://poser.pugx.org/cloudinary-labs/cloudinary-laravel/license.svg" alt="License">
    </a>
</p>

> A Laravel Package for uploading, optimizing, transforming and delivering media files with Cloudinary. Furthermore, it provides a fluent and expressive API to easily attach your media files to Eloquent models.

## Disclaimer

> _This software/code provided under Cloudinary Labs is an unsupported pre-production prototype undergoing further development and provided on an “AS IS” basis without warranty of any kind, express or implied, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. Furthermore, Cloudinary is not under any obligation to provide a commercial version of the software.</br> </br> Your use of the Software/code is at your sole risk and Cloudinary will not be liable for any direct, indirect, incidental, special, exemplary, consequential or similar damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of the Software, even if advised of the possibility of such damage.</br> </br> You should refrain from uploading any confidential or personally identifiable information to the Software. All rights to and in the Software are and will remain at all times, owned by, or licensed to Cloudinary._

## Contributions
Contributions from the community via PRs are welcome and will be fully credited. For details, see [contributions.md](contributing.md).


## Usage

**Upload** a file (_Image_, _Video_ or any type of _File_) to **Cloudinary**:

```php

/**
*  Using the Cloudinary Facade
*/

// Upload an Image File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath())->getSecurePath();

// Upload a Video File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::uploadVideo($request->file('file')->getRealPath())->getSecurePath();

// Upload any File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::uploadFile($request->file('file')->getRealPath())->getSecurePath();

/**
 *  This package also exposes a helper function you can use if you are not a fan of Facades
 *  Shorter, expressive, fluent using the
 *  cloudinary() function
 */

// Upload an image file to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

// Upload a video file to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadVideo($request->file('file')->getRealPath())->getSecurePath();

// Upload any file  to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadFile($request->file('file')->getRealPath())->getSecurePath();

// Upload an existing remote file to Cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadFile($remoteFileUrl)->getSecurePath();

/**
 *  You can also skip the Cloudinary Facade or helper method and laravel-ize your uploads by simply calling the
 *  storeOnCloudinary() method on the file itself
 */

// Store the uploaded file on Cloudinary
$result = $request->file('file')->storeOnCloudinary();

// Store the uploaded file on Cloudinary
$result = $request->file->storeOnCloudinary();

// Store the uploaded file in the "lambogini" directory on Cloudinary
$result = $request->file->storeOnCloudinary('lambogini');

// Store the uploaded file in the "lambogini" directory on Cloudinary with the filename "prosper"
$result = $request->file->storeOnCloudinaryAs('lambogini', 'prosper');

$result->getPath(); // Get the url of the uploaded file; http
$result->getSecurePath(); // Get the url of the uploaded file; https
$result->getSize(); // Get the size of the uploaded file in bytes
$result->getReadableSize(); // Get the size of the uploaded file in bytes, megabytes, gigabytes or terabytes. E.g 1.8 MB
$result->getFileType(); // Get the type of the uploaded file
$result->getFileName(); // Get the file name of the uploaded file
$result->getOriginalFileName(); // Get the file name of the file before it was uploaded to Cloudinary
$result->getPublicId(); // Get the public_id of the uploaded file
$result->getExtension(); // Get the extension of the uploaded file
$result->getWidth(); // Get the width of the uploaded file
$result->getHeight(); // Get the height of the uploaded file
$result->getTimeUploaded(); // Get the time the file was uploaded
```

**Attach Files** to Laravel **Eloquent Models**:

First, import the `CloudinaryLabs\CloudinaryLaravel\MediaAlly` trait into your Model like so:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

class Page extends Model
{
    use MediaAlly;

    ...
}
```

Next, publish the package's migration file using this command:

```bash
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-migration"
```

**Note:** Once this has been published, run `php artisan migrate` to create the required table in your Database.

You can now attach media assets to your model like so:

```php
/**
 *  How to attach a file to a Model by model creation
 */
$page = Page::create($this->request->input());
$page->attachMedia($file);   // Example of $file is $request->file('file');

/**
 *  How to attach a file to a Model by retrieving model records
 */
$page = Page::find(2);
$page->attachMedia($file);  // Example of $file is $request->file('file');

/**
 *  How to retrieve files that were attached to a Model
 */
$filesBelongingToSecondPage = Page::find(2)->fetchAllMedia();

/**
 *  How to retrieve the first file that was attached to a Model
 */
$fileBelongingToSecondPage = Page::find(2)->fetchFirstMedia();

/**
 *  How to retrieve the last file that was attached to a Model
 */
$fileBelongingToSecondPage = Page::find(2)->fetchLastMedia();

/**
 *  How to replace/update files attached to a Model
 */
$page = Page::find(2);
$page->updateMedia($file);  // Example of $file is $request->file('file');

/**
*  How to detach a file from a Model
*/
$page = Page::find(2);
$page->detachMedia($file)  // Example of $file is $request->file('file');
```

**Upload Files Via An Upload Widget**:

Use the `x-cld-upload-button` Blade upload button component that ships with this Package like so:
```
<!DOCTYPE html>
<html>
    <head>
        ...
        @cloudinaryJS
    </head>
    <body>
        <x-cld-upload-button>
            Upload Files
        </x-cld-upload-button>
    </body>
</html>
````

Other Blade components you can use are:

```php
<x-cld-image public-id="prosper" width="300" height="300"></x-cld-image> // Blade Image Component for displaying images

<x-cld-video public-id="awesome"></x-cld-video> // Blade Video Component for displaying videos
```

**Media Management via The Command Line**:

```bash
/**
*  Back-up Files on Cloudinary
*/
php artisan cloudinary:backup

/**
 *  Delete a File on Cloudinary
 */
php artisan cloudinary:delete

/**
 * Fetch a File from Cloudinary
 */
php artisan cloudinary:fetch

/**
 * Rename a File from Cloudinary
 */
php artisan cloudinary:rename

/**
 * Upload a File to Cloudinary
 */
php artisan cloudinary:upload
```


## Installation

[PHP](https://php.net) 7.2+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Cloudinary, simply require it:

```bash
composer require cloudinary-labs/cloudinary-laravel
```

Or add the following line to the require block of your `composer.json` file.

```
"cloudinary-labs/cloudinary-laravel": "1.0.1"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.


Once Laravel Cloudinary is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

```php
'providers' => [
    ...
    CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class,
    ...
]
```

> Note: If you use **Laravel >= 5.5** , you can skip this step (adding the code above to the providers key) and go to [**`configuration`**](https://github.com/cloudinary-labs/cloudinary-laravel#configuration)

Also, register the Cloudinary Facade like so:

```php
'aliases' => [
    ...
    'Cloudinary' => CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::class,
    ...
]
```

## Configuration

You can publish the configuration file using this command:

```bash
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config"
```

A configuration file named `cloudinary.php` with some sensible defaults will be placed in your `config` directory:

```php
<?php
return [
     /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | An HTTP or HTTPS URL to notify your application (a webhook) when the process of uploads, deletes, and any API
    | that accepts notification_url has completed.
    |
    |
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),


    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud hosted
    | media management service for all file uploads, storage, delivery and transformation needs.
    |
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    /**
    * Upload Preset From Cloudinary Dashboard
    *
    */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET')
];
```

### API Keys
Open your `.env` file and add your API Environment variable, upload_preset (this is optional, until you need to use the widget) like so:

```php
CLOUDINARY_URL=xxxxxxxxxxxxx
CLOUDINARY_UPLOAD_PRESET=xxxxxxxxxxxxx
CLOUDINARY_NOTIFICATION_URL=
```

***Note:** You need to get these credentials from your [Cloudinary Dashboard](https://cloudinary.com/console)*

*If you are using a hosting service like heroku, forge, digital ocean, etc, please ensure to add the above details to your configuration variables.*

### Cloudinary JS

Cloudinary relies on its own JavaScript library to initiate the Cloudinary Upload Widget. You can load the JavaScript library by placing the @cloudinaryJS directive right before your application layout's closing </head> tag:

```html
<head>
    ...

    @cloudinaryJS
</head>
```

***Note:** ONLY LOAD THIS IF YOU HAVE DECIDED TO USE THE UPLOAD WIDGET. IF YOU ARE USING THIS PACKAGE FOR A LARAVEL API BACKEND, YOU DON'T NEED TO DO THIS!*

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
