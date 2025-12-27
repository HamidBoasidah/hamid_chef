<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Custom fonts are loaded via @font-face in app.css -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>

<body class="font-sans antialiased">
  @inertia
</body>

</html>