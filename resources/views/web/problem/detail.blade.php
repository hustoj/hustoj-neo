<div class="problem-detail">
    <div class="card-group text-center mb-4" style="font-size: 0.95rem">
        <div class="card">
            <div class="card-body">
                <p class="card-text">@lang('problem.view.time_limit') : {{ $problem->time_limit }} @lang('problem.view.second')</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="card-text">@lang('problem.view.memory_limit') : {{ $problem->memory_limit }} @lang('problem.view.MB')</p>
            </div>
        </div>
        @if($problem->isSpecialJudge())
        <div class="card bg-warning text-light">
            <div class="card-body">
                    <p class="card-text">
                        @lang('problem.special_judge')
                    </p>
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-body">
                <p class="card-text">@lang('problem.view.submissions_:count', ['count' => $problem->submit])</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="card-text">@lang('problem.view.solved_:count', ['count' => $problem->accepted])</p>
            </div>
        </div>
    </div>

    <div class="problem-description">
        <dl class="detail">
            <dt>@lang('problem.description')</dt>
            <dd id="problem-desc">{!! nl2br($problem->description) !!}</dd>
            <dt>@lang('problem.input')</dt>
            <dd>{!! nl2br($problem->input) !!}</dd>
            <dt>@lang('problem.output')</dt>
            <dd>{!! nl2br($problem->output) !!}</dd>
            <dt>@lang('problem.sample_input')</dt>
            <dd>
                <pre>{{ $problem->sample_input }}</pre>
            </dd>
            <dt>@lang('problem.sample_output')</dt>
            <dd>
                <pre>{{ $problem->sample_output }}</pre>
            </dd>
            <dt>@lang('problem.hint')</dt>
            <dd>{!! $problem->hint !!}</dd>
            <dt>@lang('problem.source')</dt>
            <dd>{{ $problem->source }}</dd>
        </dl>
    </div>
</div>
