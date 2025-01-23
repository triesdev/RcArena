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
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css" />
</head>

<body data-kt-name="metronic" id="kt_app_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
    <div id="app" class="d-flex flex-column flex-root" id="kt_app_root">
        <router-view></router-view>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
    {{--<script src="/assets/plugins/global/plugins.bundle.js"></script>--}}
    {{--<script src="/assets/js/scripts.bundle.js"></script>--}}
    {{--<!--end::Global Javascript Bundle-->--}}
    {{--<!--begin::Vendors Javascript(used by this page)-->--}}
    {{--<script src="/assets/js/custom/authentication/sign-in/general.js"></script>--}}
</body>

</html>