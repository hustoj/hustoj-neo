@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>@lang('auth.whoops')</strong>@lang('auth.problems_with_input')<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
