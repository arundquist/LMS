@extends('layouts.master')
@section('head')

	<style>
		ul {-webkit-column-count:4}
	</style>

@stop
@section('main')
<h1>Teams for {{$course->short}}</h1>
<h2>For {{$assignment->comments}}</h1>
{{Form::open(['method'=>'post', 'action'=>['CoursesController@postMaketeams', $course->id, $assignment->id]])}}
<p>{{$assignment->details}}</p>
<div>
<p>Existing teams</p>
{{-- put roster here with text boxes for group numbers --}}
<ul class="list-group">
	@foreach ($roster AS $student)
		<li class="list-group-item">
			{{Form::text("team[$student->id]",$teamarray[$student->id],['size'=>2])}}
			{{$student->name}}
		</li>
	@endforeach
</ul>

<p>Other teams</p>
<table class="table table-striped table-bordered">
	@foreach ($assignmentswithteams AS $assignment)
		<tr>
			<td>{{Form::radio("newteamset", $assignment->id)}}
			@foreach ($assignment->teams AS $team)
				{{implode(', ', $team->students()->lists('name'))}}<br/>
			@endforeach
			</td>
		</tr>
	@endforeach
</table>
{{Form::submit('submit')}}
{{Form::close()}}
				







@stop