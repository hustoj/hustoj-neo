@if (count($errors->all()) > 0)
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">Error</h4>
    <p class="mb-0">Please check the form below for errors</p>
</div>
@endif
@if ($message = session('success'))
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">Success</h4>
	@if(is_array($message))
        <p class="mb-0">
            @foreach ($message as $m) {{ $m }} @endforeach
        </p>
	@else
        <p class="mb-0">
            {{ $message }}
        </p>
    @endif
</div>
@endif
@if ($message = session('error'))
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">Error</h4>
    @if(is_array($message))
        <p class="mb-0">
            @foreach ($message as $m) {{ $m }} @endforeach
        </p>
    @else
        <p class="mb-0">
            {{ $message }}
        </p>
    @endif
</div>
@endif
@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">Warning</h4>
    @if(is_array($message))
        <p class="mb-0">
            @foreach ($message as $m) {{ $m }} @endforeach
        </p>
    @else
        <p class="mb-0">
            {{ $message }}
        </p>
    @endif
</div>
@endif
@if ($message = Session::get('info'))
<div class="alert alert-info alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">Info</h4>
    @if(is_array($message))
        <p class="mb-0">
            @foreach ($message as $m) {{ $m }} @endforeach
        </p>
    @else
        <p class="mb-0">
            {{ $message }}
        </p>
    @endif
</div>
@endif
