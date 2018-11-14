<form class="form-horizontal submit" role="form" method="post" action="{{ route('solution.store') }}">
    <div class="form-group submit-meta">
        <label for="problem" class="col-sm-2 control-label">Problem</label>

        <div class="col-sm-4">
            <input name="problem" id="problem" class="form-control" value="{{$problem->order()}}" type="hidden"/>
            <span class="form-control">{{ $problem->order() }} : {{ $problem->title }}</span>
        </div>
    </div>
    <div class="form-group">
        <label for="language" class="col-sm-2 control-label">Language</label>

        <div class="col-sm-4">
            <select class="form-control" name="language" id="language">
                @foreach(App\Language::allLanguages() as $code => $display)
                    <option value="{{$code}}" @if(app('auth')->user()->language == $code) selected @endif>{{$display}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="code" class="col-sm-2 control-label">Code</label>

        <div class="col-sm-10">
            <input type="hidden" name="problem_id" value="{{ $problem->id }}"/>
            {{ csrf_field() }}
            <textarea id="code" class="form-control" name="code" rows="10"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>