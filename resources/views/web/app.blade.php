<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="freefcw"/>
    <meta name="keywords" content="华中科技大学, ACM, freefcw, sempr, online judge, 计算机竞赛, 编程, ICPC"/>
    <meta name="robots" content="index,follow"/>
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <title>@section('title') {{ config('app.name') }} @show</title>

    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{{asset('assets/site/js/html5shiv.min.js')}}"></script>
    <script src="{{asset('assets/site/js/respond.min.js')}}"></script>
    <![endif]-->
    <link rel="shortcut icon" href="{{ asset('assets/site/ico/favicon.ico') }}">
</head>
<body>
@section('navbar')
    @include('web.partials.nav')
@show
<div id="wrap">
    <div class="container">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Notice!</strong> {{session('success')}}.
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Alert!</strong> {{$errors->first()}}.
            </div>
        @endif
        @yield('content')
    </div>
</div>
@include('web.partials.footer')
<!-- Scripts -->
<script src="{{ asset('js/manifest.js')}}"></script>
<script src="{{ asset('js/vendor.js')}}"></script>
<script src="{{ asset('js/app.js')}}"></script>
@yield('scripts')
<div id="footer" class="text-center">
    © 2015 HUST ACMICPC TEAM. All Right Reserved.
    <div class="sep-20"></div>
</div>
</body>
</html>
