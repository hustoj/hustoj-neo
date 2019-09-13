@extends('web.app')

@section('content')
<div class="d-flex mb-5">
<div class="col-sm-7">
    <h4><a href="{{ route('contest.view', $contest) }}">{{ $contest->title }}</a></h4>
</div>
<div class="col-sm-5">
<ul class="nav nav-pills">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false" role="button" data-toggle="dropdown" href="#">@lang('contest.nav.problems')<span class="caret"></span></a>
        <div class="dropdown-menu">
            @foreach($contest->problems as $p)
            <a class="dropdown-item" href="{{ route('contest.problem', ['contest' => $contest->id, 'order' => $p->order()]) }}">{{$p->order()}} : {{$p->title}}</a>
            @endforeach
        </div>
    </li>
    <li class="nav-item {{ app('router')->is('contest.standing')? 'active':'' }}">
        <a class="nav-link" href="{{ route('contest.standing', ['contest' => $contest->id]) }}">@lang('contest.nav.standing')</a>
    </li>
    <li class="nav-item {{ app('router')->is('contest.status')? 'active':'' }}">
        <a class="nav-link" href="{{ route('contest.status', ['contest' => $contest->id]) }}">@lang('contest.nav.status')</a>
    </li>
    <li class="nav-item {{ app('router')->is('contest.clarify')? 'active':'' }}">
        <a class="nav-link" href="{{ route('contest.clarify', ['contest' => $contest->id]) }}">@lang('contest.nav.clarify')</a>
    </li>
</ul>
</div>
</div>

@yield('main')

@stop
