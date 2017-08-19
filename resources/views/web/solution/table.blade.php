<table class="table table-striped">
    <thead>
    <tr>
        <td>ID</td>
        <td>Problem</td>
        <td>User</td>
        <td>Language</td>
        <td>Status</td>
        <td>Time Cost</td>
        <td>Memory Cost</td>
        <td>Time</td>
        <td>Code Length</td>
    </tr>
    </thead>
    <tbody>
    @foreach($solutions as $s)
        <tr>
            <td>{{ $s->id }}</td>
            <td>
                @if($s->isContest())
                    <a href="{{ route('contest.problem', ['contest' => $s->contest_id, 'order'=>show_order($s->order)]) }}">@if(!app('router')->is('contest.status')){{$s->contest_id}} / @endif{{ show_order($s->order) }}</a>
                @else
                    <a href="{{ route('problem.view', ['problem'=>$s->problem_id]) }}">{{$s->problem_id}}</a>
                @endif
            </td>
            <td><a href="{{ route('user.view', ['username' => $s->user->username]) }}">{{ $s->user->username}}</a></td>
            <td>{{ $s->lang() }}</td>
            <td>@if ($s->isCompileError() )
                    <a target="_blank"
                       href="{{ route('solution.compile', ['solution' => $s->id]) }}">{{ $s->result() }}</a>
                @elseif($s->isRuntimeError())
                    <a target="_blank"
                       href="{{ route('solution.runtime', ['solution' => $s->id]) }}">{{ $s->result() }}</a>
                @else
                    {{ $s->result() }}
                @endif
            </td>
            <td>
                @if($s->isPending())
                    -
                @else
                    {{$s->time_cost}}ms
                @endif</td>
            <td>
                @if($s->isPending())
                    -
                @else
                    {{$s->memory_cost}}kb
                @endif</td>
            <td>{{$s->created_at}}</td>
            <td>
                @if( $cannotViewCode = false )
                    <a target="_blank"
                       href="{{ route('solution.code', ['solution' => $s->id]) }}">{{ $s->code_length }}</a>
                @else
                    {{ $s->code_length }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="text-center">{!! $solutions->appends(request()->all())->render() !!}</div>