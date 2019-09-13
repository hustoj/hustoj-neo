<div class="d-flex mb-5">
    <div class="col-sm-9">
        <h3>{{ $problem->title }}</h3>
    </div>
    <div class="col-sm-3">
        <ul class="nav nav-pills">
            <li class="nav-item {{ app('router')->is('contest.problem') ? 'active':''}}">
                <a class="nav-link"
                   href="{{ route('contest.problem', ['contest' => $contest->id, 'order' => $problem->order()]) }}">@lang('problem.nav.description')</a>
            </li>
            <li class="nav-item {{ app('router')->is('contest.submit')? 'active':'' }}">
                <a class="nav-link"
                   href="{{ route('contest.submit', ['contest' => $contest->id, 'order' => $problem->order()]) }}">@lang('problem.nav.submit')</a>
            </li>
        </ul>
    </div>
</div>
