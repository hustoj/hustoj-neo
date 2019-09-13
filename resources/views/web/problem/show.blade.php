@extends('web.problem.layout')

@section('title')
    {{ $problem->id }} - {{ $problem->title }}::
    @parent
@stop

@section('main')
    @include('web.problem.detail', ['problem' => $problem])
    <div class="text-center">
        <a class="btn btn-outline-info btn-lg" href="{{ route('problem.submit', ['problem' => $problem->id]) }}">Submit</a>
    </div>
@stop
