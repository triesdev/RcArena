<!doctype html>
<html class="h-full light" data-theme="true" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rocket Arena</title>

{{--    @vite('resources/css/app.css')--}}
{{--    <link rel="icon" href="/assets/images/accurate-pro.png">--}}
</head>
<body class="antialiased flex h-full demo1 sidebar-fixed header-fixed bg-[#fefefe] dark:bg-coal-500" style="height: 100% !important;">
<script>
    const defaultThemeMode = 'light'; // light|dark|system
    let themeMode;

    if (document.documentElement) {
        if (localStorage.getItem('theme')) {
            themeMode = localStorage.getItem('theme');
        } else if (document.documentElement.hasAttribute('data-theme-mode')) {
            themeMode = document.documentElement.getAttribute('data-theme-mode');
        } else {
            themeMode = defaultThemeMode;
        }

        if (themeMode === 'system') {
            themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        document.documentElement.classList.add(themeMode);
    }
</script>
<div id="rocket-app"></div>
    @vite('resources/js/script.js')
</body>
</html>
