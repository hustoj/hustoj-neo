<table class="table">
    <thead>
    <tr>
        <td>@lang('contest.standing.rank')</td>
        <td>@lang('contest.standing.user_id')</td>
        <td>@lang('contest.standing.ac_count')</td>
        <td>@lang('contest.standing.submit')</td>
        <td>@lang('contest.standing.penalty')</td>
        @for($i = 0; $i < $contest->problems->count(); $i++)
            <td>{{ chr(ord('A') + $i) }}</td>
        @endfor
    </tr>
    </thead>
    <tbody>
    @foreach($teams as $team)
    <tr>
        <td>{{$loop->index + 1}}</td>
        <td>
            <a href="{{ route('user.profile', ['username' => $team->user()->username]) }}">{{$team->user()->username}}</a>
        </td>
        <td>{{$team->numberOfAccept()}}</td>
        <td>{{$team->getNumberOfSubmit()}}</td>
        <td>{{ display_penalize_time($team->totalPenalty())}}</td>
        @for($i = 0; $i < $contest->problems->count(); $i++)
            <td>
                @if ($team->isProblemAccept($i))
                    {{ display_penalize_time($team->getProblemAcceptTime($i))}}
                    @if($team->getProblemWACount($i) > 0)
                        (-{{$team->getProblemWACount($i)}})
                    @endif
                @else
                    @if($team->getProblemWACount($i) > 0)
                        -{{$team->getProblemWACount($i)}}
                    @endif
                @endif
            </td>
        @endfor
    </tr>
    @endforeach
    </tbody>
</table>
