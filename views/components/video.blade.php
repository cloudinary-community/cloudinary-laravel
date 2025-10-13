@php
  $defaultFormatMethod = 'scale';
  $retrieveFormattedVideo = cloudinary()
    ->videoTag($publicId ?? '')
    ->setAttributes([
      'controls' => true,
      'loop' => true,
      'preload' => 'auto',
    ])
    ->fallback('Your browser does not support HTML5 video tags.')
    ->$defaultFormatMethod($width ?? '', $height ?? '');
@endphp

@php
  echo $retrieveFormattedVideo->serialize();
@endphp
