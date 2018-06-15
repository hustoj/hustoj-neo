@extends('web.app')

@section('content')
<div class="page-header clearfix" style="margin-bottom: 20px">
<div class="col-sm-6">
    <h4><a href="{{ route('contest.view', $contest) }}">{{ $contest->title }}</a></h4>
</div>
<div class="col-sm-6">
<ul class="nav nav-pills nav-justified group-tabs" role="tablist">
    <li class="dropdown {{ (app('router')->is('contest.view') or app('router')->is('contest.problem'))? 'active':''}}">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">@lang('contest.nav.problems')<span class="caret"></span></a>
        <ul class="dropdown-menu">
            @foreach($contest->problems as $p)
            <li><a href="{{ route('contest.problem', ['contest' => $contest->id, 'order' => $p->order()]) }}">{{$p->order()}} : {{$p->title}}</a></li>
            @endforeach
        </ul>
    </li>
    <li {{ app('router')->is('contest.standing')? 'class=active':'' }}><a href="{{ route('contest.standing', ['id' => $contest->id]) }}">@lang('contest.nav.standing')</a></li>
    <li {{ app('router')->is('contest.status')? 'class=active':'' }}><a href="{{ route('contest.status', ['id' => $contest->id]) }}">@lang('contest.nav.status')</a></li>
    <li {{ app('router')->is('contest.clarify')? 'class=active':'' }}><a href="{{ route('contest.clarify', ['id' => $contest->id]) }}">@lang('contest.nav.clarify')</a></li>
</ul>
</div>
</div>

@yield('main')

@stop