@extends('web.contest.layout')

@section('title')
    @lang('contest.title.problems') - {{ $contest->title }}::
    @parent
@stop

@section('main')
    <div class="contest-meta align-items-center">
        <ul class="list-group list-group-horizontal-xl align-items-center ml-auto mr-auto w-50 mb-3">
            <li class="list-group-item alert alert-success text-center"> @lang('contest.start_time')<br/>{{ $contest->start_time }}</li>
            @if($contest->isOpen())
            <li class="list-group-item alert alert-info"> @lang('contest.contest_is_running')<br/> @lang('contest.:time left', ['time' => $contest->time_left()])</li>
            @elseif($contest->isEnd())
            <li class="list-group-item alert alert-warning"> @lang('contest.contest_is_over') </li>
            @else
            <li class="list-group-item alert alert-info">@lang('contest.contest will beging at :time', ['time' => $contest->start_time])}</li>
            @endif
            <li class="list-group-item alert alert-info text-center"> @lang('contest.end_time')<br/>{{ $contest->end_time }}</li>
        </ul>
        <pre class="bg-secondary p-4">{!! $contest->description !!}</pre>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td>@lang('contest.problem.order') </td>
            <td>@lang('contest.problem.title') </td>
        </tr>
        </thead>
        <tbody>
        @foreach($problems as $p)
            <tr>
                <td>{{$p->order()}}</td>
                <td>
                    <a href="{{ route('contest.problem', ['contest' => $contest->id, 'order' => $p->order()]) }}">{{$p->title}}</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop
