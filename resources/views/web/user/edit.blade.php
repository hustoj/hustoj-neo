@extends('web.app')

@section('title')
    @lang('user.profile') ::
    @parent
@stop

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
        <h3>@lang('user.edit_profile')</h3>
    </div>
    <form class="form-horizontal col-md-6 col-md-offset-3" method="post" action="{{ url(route('user.edit')) }}"
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

            <!-- nick -->
            <div class="form-group {{ $errors->has('nick') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="nick">@lang('user.nick')</label>

                <div class="col-md-10">
                    <input class="form-control" type="text" name="nick" id="nick"
                           value="{{ old('nick', $user->nick) }}"/>
                    {!! $errors->first('nick', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ nick -->

            <!-- Email -->
            <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="email">@lang('user.email')</label>

                <div class="col-md-10">
                    <input class="form-control" type="text" name="email" id="email"
                           value="{{ old('email', $user->email) }}"/>
                    {!! $errors->first('email', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ email -->

            <!-- locale -->
            <div class="form-group {{ $errors->has('locale') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="locale">@lang('user.locale')</label>

                <div class="col-md-10">
                    <select name="locale" class="form-control" id="locale">
                        <option value="en" @if($user->locale === 'en') selected @endif>English</option>
                        <option value="zh" @if($user->locale === 'zh') selected @endif>中文</option>
                    </select>
                    {!! $errors->first('locale', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ locale -->

            <!-- language -->
            <div class="form-group {{ $errors->has('language') ? 'error' : '' }}">
                <label class="col-md-2 control-label" for="language">@lang('user.language')</label>

                <div class="col-md-10">
                    <select name="language" class="form-control" id="language">
                        @foreach(App\Entities\Solution::$languages as $value => $name)
                        <option value="{{ $value }}" @if($user->language === $value) selected @endif>{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('language', '<span class="help-inline">:message</span>') !!}
                </div>
            </div>
            <!-- ./ locale -->

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
