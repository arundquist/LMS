@extends('layouts.master')
@section('main')

<h1>Grade Later for {{$course->short}}</h1>
<h2>Total = {{count($scores)}}</h2>
<div class="row">
<ul class="list-group">
	@foreach ($scores AS $score)
		<li class="list-group-item">
			{{$score->created_at->diffForHumans()}}({{$score->created_at}})
			{{$score->assignment->comments}}
			{{link_to_action('GradesController@getSingle', $score->student->name,
				[$score->student->id, $score->assignment_id])}}
			
			
		</li>
	@endforeach
</ul>


@stop