@extends('web.app')

@section('title')
    @lang('solution.title.compile_information_of_solution')
    @parent
@stop

@section('content')
    <pre>@if($solution->compileInfo)
{{ $solution->compileInfo->content }}@endif
</pre>
@stop
