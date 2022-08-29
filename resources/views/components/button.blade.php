<script type="text/javascript">

    var cloudName = @json(Str::after(config('cloudinary.cloud_url'),'@'));
    var uploadPreset = @json(config('cloudinary.upload_preset'));

    function openWidget() {
        window.cloudinary.openUploadWidget(
            { cloud_name: cloudName,
              upload_preset: uploadPreset
            },
            (error, result) => {
              if (!error && result && result.event === "success") {
                console.log('Done uploading..');
                localStorage.setItem("cloud_image_url", result.info.url);
              }
        }).open();
    }
</script>

<button type="button" onclick="openWidget()" {{ $attributes }}>
  {{ $slot }}
</button>

