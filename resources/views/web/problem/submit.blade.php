@extends('web.problem.layout')

@section('title')
    Submit solution of {{ $problem->id }} - {{ $problem->title }}::
    @parent
@stop

@section('main')
    <h4 class="text-center">Submit {{ $problem->id }} : {{ $problem->title }}</h4>
    @include('web.problem.submit_form')
@stop