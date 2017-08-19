@extends('web.problem.layout')

@section('title')
    {{ $problem->id }} - {{ $problem->title }}::
    @parent
@stop

@section('main')
    @include('web.problem.detail', ['problem' => $problem])
    <a class="btn btn-primary center-block" href="{{ route('problem.submit', ['problem' => $problem->id]) }}">Submit</a>
@stop