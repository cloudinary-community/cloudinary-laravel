@php
  $defaultFormatMethod = 'scale';
  $retrieveFormattedVideo = cloudinary()
      ->videoTag($publicId ?? '')
      ->setAttributes(['controls', 'loop', 'preload'])
      ->fallback('Your browser does not support HTML5 video tagsssss.')
      ->$defaultFormatMethod($width ?? '', $height ?? '');
@endphp
