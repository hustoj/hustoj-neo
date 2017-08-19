@extends('web.contest.layout')

@section('title')
    @lang('contest.nav.status') - {{ $contest->title }}::
    @parent
@stop


@section('main')
    @include('web.solution.table', ['solutions' => $solutions])
@stop