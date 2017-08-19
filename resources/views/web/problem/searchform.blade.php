<form class="form-inline well" role="form" action="{{ route('problem.index') }}" method="GET">
    <div class="form-group">
        <label class="sr-only" for="text">@lang('problem.searchform.search_text')</label>
        <input placeholder="@lang('problem.searchform.search_text')" name="text" id="text" value="{{ request('text') }}" class="form-control"/>
    </div>
    <div class="form-group">
        <label class="sr-only" for="area">@lang('problem.searchform.type')</label>
        <select name="area" class="form-control">
            <option value="title">@lang('problem.searchform.title')</option>
            <option value="source">@lang('problem.searchform.source')</option>
        </select>
    </div>
    <input type="submit" value="@lang('problem.searchform.go')" class="btn btn-default">
</form>
