@extends('web.app')

@section('title')
    Problem Set Volume {{ request('page', 1) }} :: @parent
@stop

@section('content')
    <div class="problemset-pagination">
        {!! $problems->render() !!}
    </div>
    <div class="problem-search">
        @include('web.problem.searchform')
    </div>
    @include('web.problem.table', ['plist' => $problems])
@stop