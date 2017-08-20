@extends('web.app')

@section('title')
    {{ $topic->title }} ::
    @parent
@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $topic->title() }}
        </div>
        <div class="panel-body">
            <div class="topic-header">
                <a href="{{ route('user.profile', ['username'=> $topic->user->username]) }}">{{ $topic->user->username }}</a>
                <small class="date">{{ $topic->created_at }}</small>
            </div>
            <div class="topic-body">{!! e($topic->content) !!}</div>

        </div>
    </div>
    @foreach($topic->replies as $r)
        <div class="panel panel-default clearfix">
            <div class="panel-body">
                <div class="topic-header">
                    <a href="{{ route('user.profile', ['username'=> $topic->user->username]) }}">{{ $r->user->username }}</a>
                    <small class="date">{{ $topic->created_at }}</small>
                </div>
                <div class="topic-body">{!! e($r->content) !!}</div>
            </div>
        </div>
    @endforeach

    @if( auth()->check())
        <form class="form" role="form" method="post">
            <div class="form-group">
                <label for="content">Add Reply</label>
                {{ csrf_field() }}
                <textarea id="content" name="content" class="form-control" rows="5"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add</button>

        </form>
    @endif
@stop
