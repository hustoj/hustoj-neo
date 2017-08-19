@extends('web.contest.layout')

@section('title')
    @lang('contest.title.problems') - {{ $contest->title }}::
    @parent
@stop

@section('main')
    <div class="contest-meta">
        <div class="jumbotron">
            <div class="contest-time clearfix text-center">
                <div class="alert alert-success col-sm-3 col-sm-offset-1">
                     @lang('contest.start_time')<br/>{{ $contest->start_time }}
                </div>
                @if($contest->isOpen())
                    <div class="alert alert-info col-sm-2 col-sm-offset-1">
                        @lang('contest.contest_is_running')
                        <br/> @lang('contest.:time left', ['time' => $contest->time_left()])
                    </div>
                @elseif($contest->isEnd())
                    <div class="alert alert-danger col-sm-2 col-sm-offset-1">
                        @lang('contest.contest_is_over')
                    </div>
                @else
                    <div class="alert alert-info col-sm-3 col-sm-offset-1">
                        @lang('contest.contest will beging at :time', ['time' => $contest->start_time])
                    </div>
                @endif
                <div class="alert alert-warning col-sm-3 col-sm-offset-1">
                    @lang('contest.end_time')<br/>{{ $contest->end_time }}
                </div>
            </div>
            <p class="bg-info"><pre>{!! $contest->description !!}</pre></p>
        </div>
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