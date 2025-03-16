# Changelog

> [!IMPORTANT]
> For more details about the changes in each version, please refer to the [releases](https://github.com/cloudinary-labs/cloudinary-laravel/releases) page.

# 3.0.0 / 2024-03-16

### Notable Changes

With the v3 update, we've brought the Blade components to use the modern Blade system like `<x-cloudinary::image />`. We've also removed the limited upload, retrieval, transformation and `MediaAlly` system for just setting up and exposing the underlining Cloudinary PHP SDK directly with a helper in `cloudinary()`. We've also integrated directly into the File Storage disk system and auto detects and installs appropriate SDK's for when you're installing into an Inertia application.

If you find any bugs or feature requests please open an issue!

**Full Changelog**: https://github.com/cloudinary-community/cloudinary-laravel/compare/2.3.0...3.0.0

# 2.3.0 / 2024-03-06

- See what's new in [2.3.0](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/2.3.0)

# 2.2.3 / 2025-01-28

- See what's new in [2.2.3](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/2.2.3)

# 2.2.1 / 2024-08-14

- See what's new in [2.2.1](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/2.2.1)

# 2.2.0 / 2024-08-12

- See what's new in [2.2.0](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/2.2.0)

# 1.0.7 / 2024-06-30

- See what's new in [1.0.7](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/1.0.7)

# 1.0.6 / 2024-06-29

- See what's new in [1.0.6](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/1.0.6)

# 2.1.0 / 2024-03-19

- See what's new in [2.1.0](https://github.com/cloudinary-labs/cloudinary-laravel/releases/tag/2.1.0)

# 2.0.3 / 2023-03-01

- Bump to support Laravel 10

# 2.0.2 / 2023-02-07

- Update Analytics

# 2.0.1 / 2022-06-03

- Fix listContents() and make it compatible with Flysystem v3. @brandon14

# 2.0.0 / 2022-06-02

- Rewrite Cloudinary Adapter to work with Flysystem v3
- Support for Laravel 9 without issues
- Remove deprecated methods

# 1.0.5 / 2022-03-03

- Upgrade cloudinary php library
- Add support for Laravel 9
- Add more options to attach Media
- Fix Flystem Adapter for Cloudinary Adapter
- Add support for getting asset url using only public id
- Replace deprecated method names with v2

# 1.0.4 / 2021-02-17

- Upgrade cloudinary php library
- Fix Bug with HumanReadableSize Function - Cybersai

# 1.0.3 / 2020-12-04

- Bump to support versions greater than PHP 7

# 1.0.2 / 2020-11-27

- Add Fix to support Laravel 6

# 1.0.1 / 2020-09-23

- Bump to support Laravel 8

# 1.0.0 / 2020-07-15

- The first public version (1.0.0) of Cloudinary Laravel Package.
