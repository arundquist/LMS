@extends('layouts.master')
@section('main')

<h1>Recent {{$model}} for {{$course->short}}</h1>
<h2>Total = {{count($comments)}}</h2>
<div class="row">
<ul class="list-group">
	@foreach ($comments AS $comment)
		<li class="list-group-item">
			{{$comment->created_at->diffForHumans()}}({{$comment->created_at}})
			{{$comment->assignment->comments}}
			{{link_to_action('GradesController@getSingle', $comment->score->student->name,
				[$comment->score->student->id, $comment->score->assignment_id])}}
			@if ($model=='comments')
				{{Markdown::render($comment->comment)}}
			@else
				{{link_to($comment->link, $comment->description)}}
			@endif
			
		</li>
	@endforeach
</ul>


@stop