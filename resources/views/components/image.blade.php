@php
 echo cloudinary()->getImageTag($publicId ?? '')->scale($width ?? '', $height ?? '')->serialize();
@endphp