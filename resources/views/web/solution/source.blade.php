@extends('web.app')

@section('title')
    @lang('solution.title.source') :: @parent
@stop

@section('content')

    <pre class="prettyprint linenums">
{{ $solution->source->code }}
</pre>
@stop

@section('scripts')
    <script type="text/javascript"
            src="{{ asset('assets/google-code-prettify/prettify.js?skin=sunburst') }}"></script>
    <script type="text/javascript">
        $(function () {
            prettyPrint();
        });
    </script>
@stop

@section('styles')
    <link href="{{ asset('assets/google-code-prettify/prettify.css') }}" rel="stylesheet" type="text/css"/>
@stop