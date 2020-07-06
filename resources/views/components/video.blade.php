@php
 echo cloudinary()->getVideoTag($publicId ?? '')->setAttributes(['controls', 'loop', 'preload'])->fallback('Your browser does not support HTML5 video tagsssss.')->scale($width ?? '', $height ?? '');
@endphp