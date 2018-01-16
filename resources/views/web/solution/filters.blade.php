<div class="s-filter text-center">
    <form class="form-inline" method="get">
        <div class="form-group">
            <label class="sr-only" for="username">@lang('solution.filter.username')</label>
            <input type="text" name="username" class="form-control" id="username"
                   placeholder="@lang('solution.filter.username')" value="{{ request('username', '') }}">
        </div>
        <div class="form-group">
            <label class="sr-only" for="problem_id">@lang('solution.filter.problem_id')</label>
            <input type="text" name="problem_id" class="form-control" id="problem_id"
                   placeholder="@lang('solution.filter.problem_id')"
                   value="{{ request('problem_id', '') }}">
        </div>
        <div class="form-group">
            <label class="sr-only" for="language">@lang('solution.filter.language')</label>
            <select class="form-control" id="language" name="language">
                <option value="-1"
                        @if(request('language', -1) !== -1)selected="selected" @endif>@lang('solution.filter.language')</option>
                @foreach(App\Entities\Solution::$languages as $key => $name)
                    <option value="{{ $key }}"
                            @if(request()->has('language') && request('language') == $key)selected="selected" @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="sr-only" for="status">@lang('solution.filter.status')</label>
            <select class="form-control" id="status" name="status">
                <option value="-1"
                        @if(request('status', -1) !== -1)selected="selected" @endif>@lang('solution.filter.status')</option>
                @foreach(App\Entities\Solution::$status as $key => $name)
                    <option value="{{ $key }}"
                            @if(request()->filled('status') && request('status') == $key)selected="selected" @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-default">@lang('solution.filter.filter')</button>
    </form>
</div>