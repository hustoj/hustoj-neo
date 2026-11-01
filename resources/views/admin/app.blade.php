<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HUST OJ DashBoard</title>
    @vite(['resources/assets/sass/admin.scss', 'resources/assets/js/app.js'])
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
</body>
</html>
