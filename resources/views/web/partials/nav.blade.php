<div class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li {!! (request()->is('/') ? ' class="active"' : '') !!}><a
                            href="{{ url('') }}">@lang('site.home')</a></li>
                <li {!! ((request()->is('problemset') || request()->is('problem/*')) ? ' class="active"' : '') !!}><a
                            href="{{ route('problem.index') }}">@lang('site.problemset')</a></li>
                <li {!! (request()->is('contest*') ? ' class="active"' : '') !!}><a href="{{ route('contest.index') }}">@lang('site.contests')
                        @if(count(opening_contest()))<span class="badge">{{ count(opening_contest()) }}</span>@endif</a></li>
                <li {!! (request()->is('status') ? ' class="active"' : '') !!}><a
                            href="{{ route('solution.index') }}">@lang('site.status')</a></li>
                <li {!! (request()->is('rank') ? ' class="active"' : '') !!}><a
                            href="{{ route('user.index') }}">@lang('site.rank')</a></li>
                <li {!! (request()->is('faqs') ? ' class="active"' : '') !!}><a
                            href="{{ route('pages', ['page'=>'faqs']) }}">@lang('site.faqs')</a></li>
                <li {!! (request()->is('clarify') ? ' class="active"' : '') !!}><a
                            href="{{ route('topic.list') }}">@lang('site.discuss')</a></li>
            </ul>

            <ul class="nav navbar-nav pull-right">
                @if ( auth()->check() )
                    @if (auth()->user()->hasRole('admin'))
                        <li><a href="{{ url(route('admin.home')) }}">@lang('site.admin_panel')</a></li>
                    @endif
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"  href="#">
                            <span class="glyphicon glyphicon-user"></span> {{ auth()->user()->username }}    <span
                                    class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('user.profile') }}"><span
                                            class="glyphicon glyphicon-wrench"></span> @lang('site.my_setting')
                                </a></li>
                            <li><a href="{{ route('user.password') }}"><span
                                            class="glyphicon glyphicon-sunglasses"></span> @lang('site.edit_password')
                                </a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('logout') }}"><span
                                            class="glyphicon glyphicon-share"></span> @lang('site.logout')</a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li {!! (request()->is('login') ? ' class="active"' : '') !!}><a
                                href="{{ url('login') }}">@lang('site.login')</a></li>
                    <li {!! (request()->is('register') ? ' class="active"' : '') !!}><a
                                href="{{ url('register') }}">@lang('site.sign_up')</a></li>
                @endif
            </ul>
            <!-- ./ nav-collapse -->
        </div>
    </div>
</div>