<html>
<head>
    <script
            src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
            crossorigin="anonymous"></script>
    <link ref="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;
    </script>
</head>
<body>
<div class="text-center">

    <form action="{{ url('demo-upload') }}"
          class="dropzone"
          id="my-awesome-dropzone">
        <input type="file" name="video" style="display: none;">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
    </form>
    <small>Works only in Chrome</small>
    <ul id="file-upload-list" class="list-unstyled">

    </ul>
</div>
<script>
    var $ = window.$; // use the global jQuery instance

    if ($("#my-awesome-dropzone").length > 0) {
        var token = $('input[name=_token]').val();

        // A quick way setup
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            // Setup chunking
            chunking: true,
            method: "POST",
            maxFilesize: 400000000,
            chunkSize: 1000000,
            // If true, the individual chunks of a file are being uploaded simultaneously.
            parallelChunkUploads: true,
            success: function (file, response) {
                console.log(response);
            }, error: function (file, response) {
                console.log(file, response)
            }
        });

        // Append token to the request - required for web routes
        myDropzone.on('sending', function (file, xhr, formData) {
            formData.append("_token", token);
        })
    }

</script>
</body>
</html>