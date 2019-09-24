@extends('web.app')

@section('title')
    @lang('user.rank') ::@parent
@stop

@section('content')
    <h2 class="text-center">{{ __('user.rank') }}</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <td>@lang('user.list.rank')</td>
            <td>@lang('user.list.username')</td>
            <td>@lang('user.list.nick')</td>
            <td>@lang('user.list.solved')</td>
            <td>@lang('user.list.submit')</td>
            <td>@lang('user.list.ratio')</td>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $loop->index + $offset}}</td>
            <td><a href="{{ route('user.profile', $user->username) }}">{{ $user->username }}</a></td>
            <td>{{ $user->nick }}</td>
            <td>{{ $user->solved }}</td>
            <td>{{ $user->submit }}</td>
            <td>{{ show_ratio($user->solved, $user->submit) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {!! $users->render() !!}
    </div>
@stop
