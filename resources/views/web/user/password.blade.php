@extends('web.app')

{{-- Web site Title --}}
@section('title')
    @lang('user.edit_password') ::
    @parent
@stop

{{-- New Laravel 4 Feature in use --}}
@section('styles')
    <style type="text/css">
        body {
            background: #f2f2f2;
        }
    </style>
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>@lang('user.edit_password')</h3>
    </div>
    <form class="form-horizontal col-md-6 col-md-offset-3" method="post" action="{{ url(route('user.password')) }}"
          autocomplete="off">
        <!-- CSRF Token -->
    {{ csrf_field() }}
    <!-- ./ csrf token -->
        <!-- General tab -->
        <div class="tab-pane active" id="tab-general">
            <!-- username -->
            <div class="form-group {{ $errors->has('username') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="username">@lang('user.username')</label>

                <div class="col-md-10">
                    <input class="form-control" type="text" name="username" id="username"
                           value="{{ old('username', $user->username) }}" disabled="disabled"/>
                    {!! $errors->first('username', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ username -->

            <!-- Old Password -->
            <div class="form-group {{ $errors->has('password') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="password">@lang('user.password_old')</label>

                <div class="col-md-10">
                    <input class="form-control" type="password" name="password" id="password" value=""/>
                    {!! $errors->first('password', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./Old password -->

            <!-- Password -->
            <div class="form-group {{ $errors->has('password_new') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="password_new">@lang('user.password_new')</label>

                <div class="col-md-10">
                    <input class="form-control" type="password" name="password_new" id="password_new"/>
                    {!! $errors->first('password_new', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ password -->

            <!-- Password Confirm -->
            <div class="form-group {{ $errors->has('password_new_confirmation') ? 'error' : '' }}">
                <label class="col-md-2 control-label"
                       for="password_confirmation">@lang('user.confirm_password')</label>

                <div class="col-md-10">
                    <input class="form-control" type="password" name="password_new_confirmation" id="password_confirmation"
                           value=""/>
                    {!! $errors->first('password_confirmation', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ password confirm -->
        </div>
        <!-- ./ general tab -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <button type="submit" class="btn btn-success">@lang('site.save')</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop
