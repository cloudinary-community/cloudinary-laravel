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

<a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel"><img src="https://img.shields.io/packagist/dt/cloudinary-labs/cloudinary-laravel.svg?style=flat-square" alt="Total Downloads"></a> <a href="https://packagist.org/packages/cloudinary-labs/cloudinary-laravel"><img src="https://poser.pugx.org/cloudinary-labs/cloudinary-laravel/v/stable.svg?style=flat-square" alt="Latest Stable Version"></a> <a href="https://github.com/cloudinary-devs/cloudinary-laravel/blob/main/LICENSE"><img alt="GitHub" src="https://img.shields.io/github/license/cloudinary-devs/cloudinary-laravel?label=License&style=flat-square"></a>

> A Laravel Package for uploading, optimizing, transforming and delivering media files with Cloudinary. Furthermore, it provides a fluent and expressive API to easily attach your media files to Eloquent models.

## Contents

* [Usage](#usage)
    * [Upload, Retrieval, Transformation Method Calls](#upload-retrieval-transformation-method-calls)
    * [Attach Files to Laravel Eloquent Models](#attach-files-to-laravel-eloquent-models)
    * [Upload Files Via An Upload Widget](#upload-files-via-an-upload-widget)
    * [Media Management Via The Command Line](#media-management-via-the-command-line)
* [Installation](#installation)
* [Configuration](#configuration)
* [Disclaimer](#disclaimer)
* [Contributions](#contributions)
* [License](#license)


## Usage

> Laravel versions **8 and below** should use the **v1.x.x**.

## **Upload, Retrieval & Transformation Media Method Calls**:

**Upload** a file (_Image_, _Video_ or any type of _File_) to **Cloudinary**, **retrieve** and **transform** via any of the following ways:

```php

/**
*  Using the Cloudinary Facade
*/

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

// access the admin api
(https://cloudinary.com/documentation/admin_api)
Cloudinary::admin();

// access the search api
(https://cloudinary.com/documentation/search_api)
Cloudinary::search();

// access the upload api
(https://cloudinary.com/documentation/image_upload_api_reference)
Cloudinary::uploadApi();

// Upload an Image File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath())->getSecurePath();

// Upload a Video File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::uploadVideo($request->file('file')->getRealPath())->getSecurePath();

// Upload any File to Cloudinary with One line of Code
$uploadedFileUrl = Cloudinary::uploadFile($request->file('file')->getRealPath())->getSecurePath();

// get url from a file
$url = Cloudinary::getUrl($publicId);


/**
 *  This package also exposes a helper function you can use if you are not a fan of Facades
 *  Shorter, expressive, fluent using the
 *  cloudinary() function
 */

// access the admin api
(https://cloudinary.com/documentation/admin_api)
cloudinary()->admin();

// access the search api
(https://cloudinary.com/documentation/search_api)
cloudinary()->search();

// access the upload api
(https://cloudinary.com/documentation/image_upload_api_reference)
cloudinary()->uploadApi();

// Upload an image file to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

// Upload a video file to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadVideo($request->file('file')->getRealPath())->getSecurePath();

// Upload any file  to cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadFile($request->file('file')->getRealPath())->getSecurePath();

// Upload an existing remote file to Cloudinary with one line of code
$uploadedFileUrl = cloudinary()->uploadFile($remoteFileUrl)->getSecurePath();

// get url from a file
$url = cloudinary()->getUrl($publicId);

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

/**
 * You can also check if a file exists
 */

$url = Storage::disk('cloudinary')->fileExists($publicId);
```

## **Attach Files** to Laravel **Eloquent Models**:

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

## **Add Transformation to Uploads Using AttachMedia Method**:

```php

/**
*  How to resize an image to a specific width and height, and crop it using 'fill' mode
*/

$options = [
    'transformation' => [
        [
            'width' => 200,    // Desired width
            'height' => 200,   // Desired height
            'crop' => 'fill',  // Crop mode (you can change this to 'fit' or other modes)
        ],
    ],
]

$page->attachMedia($file, $options);   // Example of $file is $request->file('file');

/**
*  How to crop an image to a specific width and height.
*/

$options = [
    'transformation' => [
        [
            'width' => 200,    // Desired width
            'height' => 200,   // Desired height
            'crop' => 'crop',  // Crop mode
        ],
    ],
]

$page->attachMedia($file, $options);   // Example of $file is $request->file('file');

/**
*  How to rotate an image by a specific degree.
*/

$options = [
    'transformation' => [
        [
            'angle' => 45,    // Rotation angle
        ],
    ],
]

$page->attachMedia($file, $options);   // Example of $file is $request->file('file');

/**
*  How to apply a filter to an image.
*/

$options = [
    'transformation' => [
        [
            'effect' => 'grayscale',    // Filter effect
        ],
    ],
]

$page->attachMedia($file, $options);   // Example of $file is $request->file('file');

/**
*  How to overlay text on an image.
*/

$options = [
    'transformation' => [
        [
            'overlay' => [
                'font_family' => 'arial',
                'font_size' => 24,
                'text' => 'Hello World',
            ],
        ],
    ],
]

$page->attachMedia($file, $options);   // Example of $file is $request->file('file');

```

## **Upload Files Via An Upload Widget**:

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

To get the upload image link from the widget in your controller, simply set a route and controller action in your `.env`. For example:

```php
CLOUDINARY_UPLOAD_ROUTE=api/cloudinary-js-upload
CLOUDINARY_UPLOAD_ACTION=App\Http\Controllers\Api\CloudinaryController@upload
```

Make sure to specify the full path to the controller. You should be able to get the URL like so:

```php
...
class CloudinaryController extends Controller
{
    public function upload(Request $request)
    {
        $url = $request->get('cloud_image_url');
    }
}
```

## **Media Management via The Command Line**:

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

### Apps Using Laravel 9

```
"cloudinary-labs/cloudinary-laravel": "2.0.0"
```

### Apps Using Laravel 8 and below

```
"cloudinary-labs/cloudinary-laravel": "1.0.4"
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

> Note: If you use **Laravel >= 9.0** , you can skip the step (adding the code above for registering the facade) and can just import it in whatever class you need it like so:

```php
  ...
  use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
  ...
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
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    
    /**
     * Route to get cloud_image_url from Blade Upload Widget
     */
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),

    /**
     * Controller action to get cloud_image_url from Blade Upload Widget
     */
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),
];
```

### API Keys
Open your `.env` file and add your API Environment variable, upload_preset (this is optional, until you need to use the widget) like so:

```php
CLOUDINARY_URL=xxxxxxxxxxxxx
CLOUDINARY_UPLOAD_PRESET=xxxxxxxxxxxxx
CLOUDINARY_NOTIFICATION_URL=
```

***Note:** You need to get these credentials from your [Cloudinary Dashboard](https://cloudinary.com/console). The CLOUDINARY_URL is the API Environment variable shown in your Cloudinary Dashboard. Use the Copy button there to get the full URL*

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


## Disclaimer

> _This software/code provided under Cloudinary Labs is an unsupported pre-production prototype undergoing further development and provided on an “AS IS” basis without warranty of any kind, express or implied, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. Furthermore, Cloudinary is not under any obligation to provide a commercial version of the software.</br> </br> Your use of the Software/code is at your sole risk and Cloudinary will not be liable for any direct, indirect, incidental, special, exemplary, consequential or similar damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of the Software, even if advised of the possibility of such damage.</br> </br> You should refrain from uploading any confidential or personally identifiable information to the Software. All rights to and in the Software are and will remain at all times, owned by, or licensed to Cloudinary._

## Contributions
Contributions from the community via PRs are welcome and will be fully credited. For details, see [contributions.md](contributing.md).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
