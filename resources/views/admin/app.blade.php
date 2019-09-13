<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HUST OJ DashBoard</title>
    <link rel="stylesheet" href="{{asset('css/admin.css')}}">
    <link rel="stylesheet" href="{{asset('css/font-awesome.min.css')}}">
</head>
<body>
<div id="app">
    <div class="container">
        <navbar></navbar>
        <div class="page-container el-main">
            <router-view></router-view>
        </div>
        <ojfooter></ojfooter>
    </div>
</div>
<script src="{{ asset('admin/js/manifest.js')}}"></script>
<script src="{{ asset('admin/js/vendor.js') }}"></script>
<script src="{{ asset('admin/js/app.js') }}"></script>
</body>
</html>
