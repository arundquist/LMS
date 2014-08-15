@extends('layouts.master')
@section('head')
<base target="_blank" />
@stop
@section('main')
<?php
$student=$score->student;
$course=$score->assignment->type->course;
$types=$course->types;
$assignment=$score->assignment;
$totals=$student->getTotalsnewAttribute($course->id);
?>


<h1 class="text-center">{{$student->name}}</h1>
<h2 class="text-center">{{$assignment->type->type}}: {{$assignment->comments}}</h2>
<h3 class="text-center">Assigned on {{link_to_route("dates.show", "{$assignment->date->date->toDateString()} ({$assignment->date->maintopic})", [$assignment->date_id])}}</h3>
<div>
{{$assignment->details}}
</div>

<div>
<ol>
@foreach ($student->comments()->where('assignment_id', $score->assignment_id)->get() AS $comment)
	<li @if($comment->faculty_id != -1)
		style='background-color:pink;'
		@endif
	>{{$comment->date->diffForHumans()}}: {{Markdown::render($comment->comment)}}</li>
@endforeach
</ol>
</div>

<div>
<h3>Scores</h3>
<table class='table table-striped'>
<thead>
	<tr>
		<th>Assignment type</th>
		<th>your score</th>
		<th>algorithm used</th>
	</tr>
</thead>

@foreach($types AS $type)
	<tr>
		<td>{{$type->type}}</td>
		<td>{{$totals['totals'][$type->id]}}</td>
		<td>{{$type->algorithm}}</td>
	</tr>
@endforeach
</table>
Total score = {{$totals['totals'][-1]}}
</div>




@stop