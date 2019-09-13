<table class="table table-striped">
<thead>
    <tr>
        <th>@lang('problem.list.id')</th>
        <th>@lang('problem.list.title')</th>
        <th>@lang('problem.list.ratio_ac_submit')</th>
    </tr>
</thead>
<tbody>
@foreach($plist as $problem)
    <tr>
        <td class="pid">{{ $problem->id }}</td>
        <td class="ptitle"><a href="{{ route('problem.view', ['problem' => $problem->id]) }}">{{$problem->title}}</a></td>
        <td>{{ $problem->accepted }}/{{ $problem->submit }}</td>
    </tr>
@endforeach
</tbody>
</table>
