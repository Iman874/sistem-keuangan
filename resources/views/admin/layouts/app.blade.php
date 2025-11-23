<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin Panel">
    <meta name="author" content="">
    <title>@yield('title') - Admin Panel</title>

    @include('admin.partials.css')
    @stack('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('admin.partials.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('admin.partials.header')
                @yield('content')
            </div>
            @include('admin.partials.footer')
        </div>
    </div>
    <a class="rounded scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    @yield('modal')
    @include('admin.partials.js')
    @stack('scripts')
</body>
</html>