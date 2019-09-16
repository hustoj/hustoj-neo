@extends('web.app')

@section('title')
    @lang('user.profile_title') ::
    @parent
@stop


{{-- Content --}}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">@lang('user.edit_profile')</div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="{{ url(route('user.edit')) }}"
                              autocomplete="off">
                            @csrf

                            <!-- General tab -->
                            <div class="tab-pane active" id="tab-general">
                                <!-- username -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="username">@lang('user.username')</label>

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

                                <!-- nick -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="nick">@lang('user.nick')</label>

                                    <div class="col-md-9">
                                        <input class="form-control" type="text" name="nick" id="nick"
                                               value="{{ old('nick', $user->nick) }}"/>
                                    </div>

                                    @error('nick')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- ./ nick -->

                                <!-- Email -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="email">@lang('user.email')</label>

                                    <div class="col-md-9">
                                        <input class="form-control @if($user->hasVerifiedEmail()) border border-success @endif" type="text" name="email" id="email"
                                               value="{{ old('email', $user->email) }}"/>
                                        @if($user->hasVerifiedEmail())
                                            <small class="form-text text-success">
                                                {{ __('You email has verified.') }}
                                            </small>
                                        @else
                                            <small class="form-text text-danger">
                                                {{ __('You email was not verified.') }}
                                            </small>
                                        @endif
                                    </div>


                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- ./ email -->

                                <!-- locale -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="locale">@lang('user.locale')</label>

                                    <div class="col-md-9">
                                        <select name="locale" class="form-control" id="locale">
                                            <option value="en" @if($user->locale === 'en') selected @endif>English</option>
                                            <option value="zh" @if($user->locale === 'zh') selected @endif>中文</option>
                                        </select>
                                    </div>

                                    @error('locale')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- ./ locale -->

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="email_level">@lang('user.form.email.privacy_level')</label>

                                    <div class="col-md-9">
                                        <select name="email_level" class="form-control" id="email_level">
                                            <option value="0" @if($user->email_level == 0) selected @endif>{{ __('user.form.email.level.Hidden') }}</option>
                                            <option value="1" @if($user->email_level == 1) selected @endif>{{ __('user.form.email.level.Show') }}</option>
                                            <option value="2" @if($user->email_level == 2) selected @endif>{{ __('user.form.email.level.Base64') }}</option>
                                        </select>
                                    </div>

                                    @error('email_level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <!-- language -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="language">@lang('user.language')</label>

                                    <div class="col-md-9">
                                        <select name="language" class="form-control" id="language">
                                            @foreach(App\Language::allLanguages() as $value => $name)
                                                <option value="{{ $value }}" @if($user->language === $value) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @error('language')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- ./ language -->

                                <!-- ./ general tab -->

                                <!-- Form Actions -->
                                <div class="form-group">
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
