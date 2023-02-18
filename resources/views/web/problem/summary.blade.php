@extends('web.problem.layout')

@section('title')
    {{ $problem->id }} - {{ $problem->title }}::
    @parent
@stop

@section('main')
    <div class="row">
        <div class="col-sm-3">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @lang('problem.summary.total')<span
                                class="badge badge-secondary badge-pill">{{ $summary->total }}</span></li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @lang('problem.summary.submit_user')<span
                                class="badge badge-secondary badge-pill">{{ $summary->submit() }}</span></li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @lang('problem.summary.solved_user')
                        <span class="badge badge-secondary badge-pill">{{ $summary->accepted() }}</span></li>
                @foreach($summary->statistics as $retType => $number)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ show_status($retType) }}
                        <span class="badge badge-secondary badge-pill">{{ $number }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-sm-9">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>@lang('problem.summary.rank')</th>
                    <th>@lang('problem.summary.runid')</th>
                    <th>@lang('problem.summary.memory')</th>
                    <th>@lang('problem.summary.time')</th>
                    <th>@lang('problem.summary.user')</th>
                    <th>@lang('problem.summary.language')</th>
                    <th>@lang('problem.summary.submit_time')</th>
                </tr>
                </thead>
                <tbody>
                <?php $solutions = $summary->bestSolutions();
                $rankStart = (current_page() - 1) * $perPage;?>
                @foreach( $solutions as $solution)
                <tr>
                    <td>{{ ++$rankStart }}</td>
                    <td>@if ((int)$solution->att > 1)
                            <a href="{{ route('solution.index', ['problem_id' => $solution->problem_id,
                             'status' => App\Status::ACCEPT,
                             'username' => $solution->user->username
                             ]) }}">{{ $solution->id }}({{$solution->att}})</a>
                        @else
                            {{ $solution->id }}
                        @endif
                    </td>
                    <td>{{ $solution->memory_cost }}kb</td>
                    <td>{{ $solution->time_cost }}ms</td>
                    <td>
                        <a href="{{ route('user.profile', ['username' => $solution->user->username]) }}">{{ $solution->user->username }}</a>
                    </td>
                    <td>{{ $solution->lang() }}</td>
                    <td>{{ $solution->created_at }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {!! $solutions->render() !!}
        </div>
    </div>
@stop
