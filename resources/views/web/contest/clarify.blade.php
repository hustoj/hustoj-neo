@extends('web.contest.layout')

@section('title')
    @lang('contest.clarify.clarify') :: @parent
@stop

@section('main')

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Problem</th>
            <th>Title</th>
            <th>User</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($topics as $topic)
        <tr>
            <td><a href="">{{ $topic->showProblemId() }}</a> </td>
            <td><a href="{{ route('topic.view', ['id' => $topic->id]) }}"> {{ $topic->title }} </a></td>
            <td><a href="{{ route('user.profile', ['username' => $topic->user->username]) }}">{{ $topic->user->username }}</a></td>
            <td>{{ $topic->created_at }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    {{ $topics->render() }}

    @include('web.topic._compose')
@stop