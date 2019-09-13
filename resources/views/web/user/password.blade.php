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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">@lang('user.edit_profile')</div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="{{ url(route('user.password')) }}"
                              autocomplete="off">
                            <!-- CSRF Token -->
                        @csrf
                        <!-- General tab -->
                            <!-- username -->
                            <div class="form-group row">
                                <label class="col-md-3 control-label" for="username">@lang('user.username')</label>

                                <div class="col-md-9">
                                    <input class="form-control" type="text" name="username" id="username"
                                           value="{{ old('username', $user->username) }}" disabled="disabled"/>
                                </div>

                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- ./ username -->

                            <!-- Old Password -->
                            <div class="form-group row">
                                <label class="col-md-3 control-label" for="password">@lang('user.password_old')</label>

                                <div class="col-md-9">
                                    <input class="form-control" type="password" name="password" id="password" value=""/>
                                </div>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- ./Old password -->

                            <!-- Password -->
                            <div class="form-group row">
                                <label class="col-md-3 control-label" for="password_new">@lang('user.password_new')</label>

                                <div class="col-md-9">
                                    <input class="form-control" type="password" name="password_new" id="password_new"/>
                                </div>

                                @error('password_new')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- ./ password -->

                            <!-- Password Confirm -->
                            <div class="form-group row">
                                <label class="col-md-3 control-label"
                                       for="password_confirmation">@lang('user.confirm_password')</label>

                                <div class="col-md-9">
                                    <input class="form-control" type="password" name="password_new_confirmation" id="password_confirmation"
                                           value=""/>
                                </div>
                            </div>
                            <!-- ./ password confirm -->

                            <!-- Form Actions -->
                            <div class="form-group row">
                                <div class="offset-md-3 col-md-9">
                                    <button type="submit" class="btn btn-success">@lang('site.save')</button>
                                </div>
                            </div>
                            <!-- ./ form actions -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
