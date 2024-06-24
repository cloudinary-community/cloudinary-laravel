<script type="text/javascript">

    var cloudName = @json(Str::after(config('cloudinary.cloud_url'),'@'));
    var uploadPreset = @json(config('cloudinary.upload_preset'));
    var uploadRoute = @json(config('cloudinary.upload_route'));

    function openWidget() {
        window.cloudinary.openUploadWidget(
            { cloud_name: cloudName,
              upload_preset: uploadPreset
            },
            (error, result) => {
              if (!error && result && result.event === "success") {
                  console.log('Done uploading..');
                  localStorage.setItem("cloud_image_url", result.info.url);
                  try {
                      if (uploadRoute) {
                          fetch(uploadRoute, {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                              },
                              body: JSON.stringify({cloud_image_url: result.info.url})
                          })
                              .then(response => response.json())
                              .then(data => {
                                  console.log(data);
                              })
                              .catch(error => {
                                  console.error('Error:', error);
                              });
                      }
                  } catch (e) {
                      console.error(e);
                  }
              }
        }).open();
    }
</script>

<button type="button" onclick="openWidget()">
  {{ $slot }}
</button>

