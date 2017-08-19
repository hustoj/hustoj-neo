@extends('web.app')
{{-- Web site Title --}}
@section('title')
    @lang('contest.title.list') :: @parent
@stop

{{-- Content --}}
@section('content')
    <h1>@lang('contest.contest_list')</h1>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>@lang('contest.table.id')</th>
            <th>@lang('contest.table.title')</th>
            <th>@lang('contest.table.private')</th>
            <th>@lang('contest.table.date')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contests as $contest)
            <tr>
                <td>{{ $contest->id }}</td>
                <td><a href="{{ route('contest.view', ['id' => $contest->id]) }}">{{ $contest->title }}</a></td>
                <td>@if($contest->private) {{ trans('contest.table_list.private') }} @else {{trans('contest.table_list.public')}} @endif</td>
                <td>{{ $contest->start_time }} -- {{ $contest->end_time }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {!! $contests->render() !!}
    </div>
@stop
