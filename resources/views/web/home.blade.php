@extends('web.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Welcome to HUSTOJ!</div>

				<div class="panel-body news">
                    @foreach($news as $post)
                    <div class="post-header">
                        <h4>{{ $post->title }}</h4>
                        <div class="info">
                            <a href="{{ route('user.view', ['username' => $post->author->username]) }}">{{ $post->author->username }}</a> @ <span> {{ $post->created_at }}</span>
                        </div>
                    </div>
                    <div class="post-content">
                        {!! $post->content !!}
                    </div>
                    <hr />
                    @endforeach
                    {!! $news->render() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
