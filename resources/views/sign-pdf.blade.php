<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Digital Signature</title>
</head>
<body>
    <h2>Tải lên file PDF để ký số</h2>
    <form action="/sign-pdf" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="pdf" accept="application/pdf" required>
        <button type="submit">Ký số</button>
    </form>
</body>
</html>