@extends('web.contest.layout')

@section('title')
    @lang('contest.nav.standing') :: @parent
@stop

@section('main')
    @include('web.contest.standing_table', ['contest' => $contest, 'teams' => $teams])
@stop