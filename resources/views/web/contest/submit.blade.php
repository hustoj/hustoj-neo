@extends('web.contest.layout')

@section('title')
    {{ $problem->title }} - {{ $contest->title }}::
    @parent
@stop

@section('main')

    @include('web.contest.problem_header')

    @include('web.problem.submit_form')
@stop
