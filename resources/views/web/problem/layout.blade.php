@extends('web.app')

@section('content')
    <div class="d-flex mb-5">
        <div class="col-sm-9">
            <h2>{{ $problem->id }} - {{ $problem->title }}</h2>
        </div>
        <div class="col-sm-3">
            <ul class="nav nav-pills">
                <li class="nav-item {{ app('router')->is('problem.view') ? 'active':''}}">
                    <a class="nav-link"
                       href="{{ route('problem.view', ['problem' => $problem->id]) }}">@lang('problem.nav.description')</a>
                </li>
                <li class="nav-item {{ app('router')->is('problem.summary')? 'active':'' }}">
                    <a class="nav-link"
                       href="{{ route('problem.summary', ['problem'=>$problem->id]) }}">@lang('problem.nav.summary')</a>
                </li>
            </ul>
        </div>
    </div>

    @yield('main')

@stop
