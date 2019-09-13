@extends('web.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10 col-md-offset-1">
			<div class="card">
				<h2 class="card-header">Welcome to HUSTOJ!</h2>
				<div class="card-body">
                    @foreach($news as $post)
                    <div class="card-body">
                        <h4 class="card-title">{{ $post->title }}</h4>
                        <p class="card-text">
                            {!! $post->content !!}
                        </p>
                        <div class="card-footer">
                            <small class="text-muted"> <a href="{{ route('user.view', ['username' => $post->author->username]) }}">{{ $post->author->username }}</a> @ {{ $post->created_at }}</small>
                        </div>
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
