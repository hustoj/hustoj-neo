<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbrNavMarkup" aria-controls="navbrNavMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbrNavMarkup">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{ request()->is('/') ? 'active': '' }}">
                <a class="nav-link" href="{{ url('') }}">@lang('site.home') <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item {{ (request()->is('problemset') || request()->is('problem/*')) ? 'active': '' }}">
                <a class="nav-link" href="{{ route('problem.index') }}">@lang('site.problemset')</a>
            </li>
            <li class="nav-item {{ request()->is('contest*') ? 'active': ''  }}">
                <a class="nav-link" href="{{ route('contest.index') }}">@lang('site.contests')
                    @if(count(opening_contest()))<span class="badge badge-pill badge-info">{{ count(opening_contest()) }}</span>@endif
                </a>
            </li>
            <li class="nav-item {{ request()->is('status') ? 'active': '' }}">
                <a class="nav-link" href="{{ route('solution.index') }}">@lang('site.status')</a>
            </li>
            <li class="nav-item {{ request()->is('rank') ? 'active': '' }}">
                <a class="nav-link" href="{{ route('user.index') }}">@lang('site.rank')</a>
            </li>
            <li class="nav-item {{ request()->is('faqs') ? 'active': '' }}">
                <a class="nav-link" href="{{ route('pages', ['page'=>'faqs']) }}">@lang('site.faqs')</a>
            </li>
            <li class="nav-item {{ request()->is('clarify') ? 'active': '' }}">
                <a class="nav-link" href="{{ route('topic.list') }}">@lang('site.discuss')</a>
            </li>
        </ul>
        <ul class="form-inline my-2 my-md-0 navbar-nav">
            @if(auth()->check())
                <li class="nav-item dropdown">
                    <a class="nav-link btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->username }}
                    </a>
                    <div class="dropdown-menu" x-placement="bottom-start">
                        <a class="dropdown-item" href="{{ route('user.edit') }}">
                            @lang('site.my_setting')</a>
                        <a class="dropdown-item" href="{{ route('user.password') }}">
                            @lang('site.edit_password')</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ url('logout') }}">
                            @lang('site.logout')</a>
                    </div>
                </li>
                @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url(route('admin.home')) }}">@lang('site.admin_panel')</a>
                    </li>
                @endif
            @else
            <li class="nav-item {{ request()->is('login') ? 'active': '' }}">
                <a class="nav-link" href="{{ url('login') }}">@lang('site.login')</a>
            </li>
            <li class="nav-item {{ request()->is('register') ? 'active': '' }}">
                <a class="nav-link" href="{{ url('register') }}">@lang('site.sign_up')</a>
            </li>
            @endif
        </ul>
    </div>
</nav>
