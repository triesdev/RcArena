<!doctype html>
<html lang="en">

<head>
    <base href="../">
    <title>Mine</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="/assets/logo/logo-tenis-demo.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    @if (ENV('APP_ENV') == 'local')
        <link href="/css/app.css" rel="stylesheet" type="text/css" />
    @else
        @include('admin.css_version')
    @endif
</head>

<body>
    <div id="app">
        <router-view></router-view>
    </div>
    </script>
    @if (ENV('APP_ENV') != 'local')
        @include('admin.js_version')
    @else
        <script src="/js/app.js"></script>
    @endif
</body>

</html>
