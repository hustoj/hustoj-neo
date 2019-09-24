<form class="form-row align-items-center" role="form" action="{{ route('problem.index') }}" method="GET">
    <div class="col-sm-6">
        <label class="sr-only" for="text">@lang('problem.searchform.search_text')</label>
        <input class="form-control" placeholder="@lang('problem.searchform.search_text')" name="text" id="text" value="{{ request('text') }}"/>
    </div>
    <div class="col-sm-4">
        <label class="sr-only" for="area">@lang('problem.searchform.type')</label>
        <select name="area" class="form-control">
            <option value="title">@lang('problem.searchform.title')</option>
            <option value="source">@lang('problem.searchform.source')</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">@lang('problem.searchform.go')</button>
    </div>
</form>
