@extends('web.app')

@section('title')
    Search Result ::
    @parent
@stop

@section('content')
    <div class="problem-search">
        @include('web.problem.searchform')
    </div>
    @include('web.problem.index', ['plist' => $problemList])

@stop