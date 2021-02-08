@extends('web.app')

@section('title')
    {{ $topic->title }} ::
    @parent
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            {{ $topic->title() }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-2 border-right">
                    <a href="{{ route('user.profile', ['username'=> $topic->user->username]) }}">{{ $topic->user->username }}</a>
                </div>
                <div class="col-10">
                    <pre class="card-text text-monospace">{!! e($topic->content) !!}</pre>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <small>{{ $topic->created_at }}</small>
        </div>
    </div>
    @foreach($topic->replies as $r)
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-2 border-right">
                        <a href="{{ route('user.profile', ['username'=> $topic->user->username]) }}">{{ $r->user->username }}</a>
                    </div>
                    <div class="col-10">
                        <pre class="card-text text-monospace">{!! e($r->content) !!}</pre>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <small>{{ $topic->created_at }}</small>
            </div>
        </div>
    @endforeach

    @if(auth()->check() && $isUserCanReply)
        <form class="form mt-4" role="form" method="post">
            <div class="form-group">
                <label for="content">Add Reply</label>
                {{ csrf_field() }}
                <textarea id="content" name="content" class="form-control" rows="5" required minlength="10"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    @endif
@stop
