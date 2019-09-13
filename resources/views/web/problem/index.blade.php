@extends('web.app')

@section('title')
    Problem Set Volume {{ request('page', 1) }} :: @parent
@stop

@section('content')
    <div class="d-flex justify-content-between mb-4">
        {!! $problems->render() !!}
        @include('web.problem.searchform')
    </div>
    @include('web.problem.table', ['plist' => $problems])
@stop
