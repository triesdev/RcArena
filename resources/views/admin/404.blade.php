<html lang="en">
<!--begin::Head-->

<head>
    <base href="../../">
    <title>Sistem Antrian</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="/assets/logo/logo-tenis-demo.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        body {
            background-image: url('/assets/media/auth/bg1.jpg');
        }

        [data-theme="dark"] body {
            background-image: url('/assets/media/auth/bg1-dark.jpg');
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
    <div class="d-flex flex-column flex-root" id="app">
        <router-view></router-view>
    </div>
    <script src="{{ mix('js/app-1.js') }}"></script>
</body>
<!--end::Body-->

</html>