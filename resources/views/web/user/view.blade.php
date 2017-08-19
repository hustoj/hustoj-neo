@extends('web.app')

{{-- Web site Title --}}
@section('title')
    @lang('user.profile') ::
    @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h1>@lang('user.profile')</h1>
    </div>

    <div class="col-md-6 col-md-offset-3 col-md-offset-3 toppad">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $user->nick }}</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3" align="center">
                    </div>
                    <div class="col-md-9">
                        <table class="table table-user-information">
                            <tbody>
                            <tr>
                                <td>@lang('user.nick'):</td>
                                <td>{{ $user->nick }}</td>
                            </tr>
                            <tr>
                                <td>@lang('user.created_at'):</td>
                                <td>{{ $user->created_at }}</td>
                            </tr>
                            <tr>
                                <td>@lang('user.school')</td>
                                <td>{{ $user->school}}</td>
                            </tr>
                            <tr>
                                <td>@lang('user.email')</td>
                                <td>{{ base64_encode($user->email) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
