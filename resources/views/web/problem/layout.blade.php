@extends('web.app')

@section('content')
    <div class="page-header clearfix" style="margin-bottom: 20px">
        <div class="col-sm-8">
            <h2>{{ $problem->id }} - {{ $problem->title }}</h2>
        </div>
        <div class="col-sm-4">
            <ul class="nav nav-pills nav-justified group-tabs" role="tablist" style="margin-top: 30px;">
                <li class="{{ app('router')->is('problem.view') ? 'active':''}} col-sm-10" role="presentation"><a
                            href="{{ route('problem.view', ['problem' => $problem->id]) }}">@lang('problem.nav.description')</a>
                </li>
                <li class="{{ (app('router')->is('contest.submit') || app('router')->is('problem.submit')) ? 'active':'' }}" role="presentation"><a
                            href="{{ route('problem.submit', ['problem' => $problem->id]) }}">@lang('problem.nav.submit')</a>
                </li>
                <li class="{{ app('router')->is('problem.summary')? 'active':'' }}" role="presentation"><a
                            href="{{ route('problem.summary', ['problem'=>$problem->id]) }}">@lang('problem.nav.summary')</a>
                </li>
            </ul>
        </div>
    </div>

    @yield('main')

@stop