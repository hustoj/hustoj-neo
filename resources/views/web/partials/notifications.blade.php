@if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>{{ __('Cong!') }}</strong> {{session('success')}}.
    </div>
@endif

@if($errors->any())
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4 class="alert-heading">{{ __('Alert!') }}</h4>
        @if(is_array($message))
            <p class="mb-0">
                @foreach ($errors as $m) {{ $m }} @endforeach
            </p>
        @else
            <p class="mb-0">
                {{$errors->first()}}
            </p>
        @endif
    </div>
@endif

@if ($message = session('warning'))
<div class="alert alert-warning alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4 class="alert-heading">{{ __('Warning') }}</h4>
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

