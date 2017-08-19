<div class="problem-detail">
    <div class="problem-meta text-center clearfix">
        <div class="col-sm-3 col-sm-offset-2 alert alert-info">
            @lang('problem.view.time_limit') : <span
                    class="label label-warning">{{ $problem->time_limit }} @lang('problem.view.second')</span>
        </div>
        <div class="col-sm-3 col-sm-offset-2 alert alert-info">
            @lang('problem.view.memory_limit') : <span
                    class="label label-danger">{{ $problem->memory_limit }} @lang('problem.view.MB')</span><br/>
        </div>
        <div class="col-sm-3 col-sm-offset-2 alert alert-info">
            @lang('problem.view.submissions_:count', ['count' => $problem->submit])
        </div>
        @if($problem->isSpecialJudge())
            <div class="col-sm-2 alert alert-warning">
                @lang('problem.special_judge')
            </div>
        @endif
        <div class="col-sm-3 @if(!$problem->isSpecialJudge()) col-sm-offset-2 @endif alert alert-info">
            @lang('problem.view.solved_:count', ['count' => $problem->accepted])
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
            <dd>{{ $problem->hint }}</dd>
            <dt>@lang('problem.source')</dt>
            <dd>{{ $problem->source }}</dd>
        </dl>
    </div>
</div>
