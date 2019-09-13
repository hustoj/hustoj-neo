@extends('web.app')

{{-- Web site Title --}}
@section('title')
    @lang('user.profile') ::
    @parent
@stop

{{-- Content --}}
@section('content')
    <div class="col-md-6 col-md-offset-3 col-md-offset-3">
        <div class="card">
            <div class="card-header">
                <h3>{{ $user->nick }}</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">@lang('user.nick'): {{ $user->nick }}</li>
                    <li class="list-group-item">@lang('user.created_at'): {{ $user->created_at }}</li>
                    <li class="list-group-item">@lang('user.school'): {{ $user->school }}</li>
                    <li class="list-group-item">@lang('user.email'): {{ base64_encode($user->email) }}</li>
                </ul>
            </div>
        </div>
    </div>
@stop
