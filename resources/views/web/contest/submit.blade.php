@extends('web.contest.layout')

@section('title')
    {{ $problem->title }} - {{ $contest->title }}::
    @parent
@stop

@section('main')

    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px">
        <li style="position: absolute"><h4>{{ $problem->title }}</h4></li>
        <li style="margin-left: 80%" class="{{ app('router')->is('contest.problem') ? 'active':''}}" role="presentation"><a href="{{ route('contest.problem', ['id' => $contest->id, 'order' => $problem->order()]) }}">Problem</a></li>
        <li class="{{ app('router')->is('contest.submit')? 'active':'' }}" role="presentation" ><a href="{{ route('contest.submit', ['id' => $contest->id, 'order' => $problem->order()]) }}">Submit</a></li>
    </ul>

    @include('web.problem.submit_form')
@stop