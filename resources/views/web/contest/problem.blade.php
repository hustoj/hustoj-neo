@extends('web.contest.layout')

@section('title')
{{ $problem->title }} - {{ $contest->title }}::
@parent
@stop

@section('main')

@include('web.contest.problem_header')

@include('web.problem.detail', ['problem' => $problem])
<div class="text-center">
    <a class="btn btn-info btn-lg" href="{{ route('contest.submit', ['contest' => $contest->id, 'order' => $problem->order()]) }}">Submit</a>
</div>
@stop
