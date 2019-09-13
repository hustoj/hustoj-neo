@extends('web.app')

@section('title')
    @lang('solution.title.runtime_information_of_solution')
    @parent
@stop

@section('content')
    <pre>@if($solution->runtimeInfo){{ $solution->runtimeInfo->content }}@endif
</pre>
@stop
