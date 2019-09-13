@extends('web.app')

@section('title')
    @lang('topic.create') ::
    @parent
@stop

@section('content')
    @include('web.topic._compose')
@stop
