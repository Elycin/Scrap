<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield("title")</title>

    <link rel="stylesheet" href="/css/app.css">
    @yield("css")
</head>

<body>
<!-- Navigation Begin -->
@include("layouts.navigation")
<!-- Navigation End -->


<!-- Main Content Begin -->
@yield("body")
<!-- Main Content End -->
</body>
</html>
