<div class="text-center mb-3">
    <form class="form-inline align-items-center" method="get">
        <div class="col-auto">
            <label class="sr-only" for="username">@lang('solution.filter.username')</label>
            <input type="text" name="username" class="form-control" id="username"
                   placeholder="@lang('solution.filter.username')" value="{{ request('username', '') }}">
        </div>
        <div class="col-auto">
            <label class="sr-only" for="problem_id">@lang('solution.filter.problem_id')</label>
            <input type="text" name="problem_id" class="form-control" id="problem_id"
                   placeholder="@lang('solution.filter.problem_id')"
                   value="{{ request('problem_id', '') }}">
        </div>
        <div class="col-auto">
            <label class="sr-only" for="language">@lang('solution.filter.language')</label>
            <select class="form-control" id="language" name="language">
                <option value=""
                        @if(!request()->filled('language'))selected="selected" @endif>@lang('solution.filter.language')</option>
                @foreach(App\Language::allLanguages() as $key => $name)
                    <option value="{{ $key }}"
                            @if(request()->filled('language') && request('language') == $key)selected="selected" @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="sr-only" for="status">@lang('solution.filter.status')</label>
            <select class="form-control" id="status" name="status">
                <option value=""
                        @if(!request()->filled('status'))selected="selected" @endif>@lang('solution.filter.status')</option>
                @foreach(App\Entities\Solution::$status as $key => $name)
                    <option value="{{ $key }}"
                            @if(request()->filled('status') && request('status') == $key)selected="selected" @endif>{!! $name !!}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">@lang('solution.filter.filter')</button>
    </form>
</div>
