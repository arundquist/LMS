@extends('layouts.master')
@section('main')
<h1>Algorithms for {{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
{{Form::open(['method'=>'post', 'action'=>['CoursesController@postAlgorithms', $course->id]])}}

<table class="table table-striped table-bordered">
<thead>
<tr>
	<th>Assignments</th>
	<th>Algorithms</th>
</tr>
</thead>
<tbody>
<tr>
	<td>
		<ul>
			@foreach($course->types AS $type)
				<li>{{$type->type}} ({{$type->id}})</li>
			@endforeach
		</ul>
	</td>
	<td>
		{{Form::textarea('coursealgorithm', $course->algorithm->algorithm)}}
	</td>
</tr>
@foreach ($course->types AS $type)
	<tr>
		<td>
			{{$type->type}}
			@if(count($type->assignments)==0)
				{{Form::checkbox("deletetypes[$type->id]")}} Delete this type?
			@else
				<ul>
					@foreach ($type->assignments AS $assignment)
						<li>{{$assignment->comments}} ({{$assignment->id}})</li>
					@endforeach
				</ul>
			@endif
		</td>
		<td>
			{{Form::textarea("typealgorithms[$type->id]", $type->algorithm)}}
		</td>
	</tr>
@endforeach
</tbody>
</table>
{{Form::submit('submit')}}
@stop