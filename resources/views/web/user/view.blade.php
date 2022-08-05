@extends('web.app')

{{-- Web site Title --}}
@section('title')
    @lang('user.profile_title') ::
    @parent
@stop

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="card">
                <div class="card-header">
                    <h3>{{ $user->nick }}</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">@lang('user.nick'): {{ $user->nick }}</li>
                        <li class="list-group-item">@lang('user.created_at'): {{ $user->created_at }}</li>
                        <li class="list-group-item">@lang('user.school'): {{ $user->school }}</li>
                        <li class="list-group-item">@lang('user.list.submit'): {{ $user->submit }}</li>
                        <li class="list-group-item">@lang('user.list.solved'): {{ $user->solved }}</li>

                        @if($user->showEmail())
                        <li class="list-group-item">@lang('user.email'): {{ $user->getEmail() }}</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <ul class="user-resolved-problem">
            @foreach($problems as $pid)
                    <li><a href="{{ route('problem.view', $pid) }}">{{ $pid }}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
@stop
