@extends('web.app')

@section('title')
    @lang('site.status') ::
    @parent
@stop

@section('content')
    @include('web.solution.filters')
    @include('web.solution.table', ['solutions' => $solutions])
@stop