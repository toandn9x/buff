<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool nạp thẻ</title>
</head>
<body>
    <h2>Tool nạp thẻ Viettel</h2>
    <form action="/paicevn" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="text" name="phone_number" required placeholder="Nhập SĐT">
        <textarea name="code" required placeholder="Nhập DS Mã" rows="50" cols="150"></textarea>
        <input type="submit" value="send">
    </form>
</body>
</html>