@extends('layouts.master')
@section('main')
<h1>{{link_to_route('syllabus.show', "{$type->course->classname} ({$type->course->semester} {$type->course->year})", [$type->course->id])}}</h1>
<h2>{{$type->type}} assignments</h2>
<div class="container">
<div class='row'>
<div class='col-md-8'>
<table class='table table-striped'>
<thead>
<tr>
	<th>id</th>
	<th>Assignment</th>
	<th>Details</th>
	<th>total possible</th>
	<th>Day assigned</th>
</tr>
</thead>
@foreach ($type->assignments AS $assignment)
	<tr>
		<td>{{$assignment->id}}</td>
		<td>{{$assignment->comments}}</td>
		<td>{{Markdown::render($assignment->details)}}</td>
		<td>{{$assignment->total}}</td>
		<td>{{link_to_route('dates.show', $assignment->date->date->toDateString(), [$assignment->date->id])}} {{$assignment->date->date->diffForHumans()}}</td>
	</tr>
@endforeach
</table>
</div>
<div class='col-md-4'>
<h3>algorithm</h3>
{{$type->algorithm}}
<div>
The numbers refer to the id of the assignments. s[xxx] represents the students score
on the xxx assignment and t[xxx] represents the total possible.
</div>
</div>
</div>
</div>
@stop