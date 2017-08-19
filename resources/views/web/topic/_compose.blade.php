@if(auth()->user())
<form class="form-horizontal" role="form" method="post" action="{{ route('topic.store') }}">
    <div class="form-group">
        <label for="title" class="col-sm-1">Title</label>
        {{csrf_field()}}
        <div class="col-sm-4">
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
    </div>
    <div class="form-group">
        <label for="problem" class="col-sm-1">Problem</label>
        <div class="col-sm-4">
            @if(isset($contest))
                <select name="problem" id="problem" class="form-control">
                    @foreach($contest->problems as $problem)
                        <option value="{{ $problem->pivot->order }}">{{ $problem->order() }} : {{ $problem->title }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" id="problem" name="problem" class="form-control" value="0">
            @endif
        </div>
    </div>

    <div class="form-group">
        <label for="content" class="col-sm-1">Content</label>

        <div class="col-sm-8">
            <textarea id="content" name="content" class="form-control" rows="6"></textarea>
        </div>
    </div>

    <div class="from-group">
        <div class="col-sm-8 col-sm-offset-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
@endif