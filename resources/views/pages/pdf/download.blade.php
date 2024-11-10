<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anbar</title>
</head>
<body>
    <script>
        window.onload = function() {
            const fileName = "{{ $fileName }}";
            const downloadUrl = "{{ route('download.pdf') }}?file=" + encodeURIComponent(fileName);

            window.location.href = downloadUrl;

            setTimeout(function() {
                window.location.href = "{{ route('dashboard.index') }}";
            }, 1500);
        };
    </script>
</body>
</html>
