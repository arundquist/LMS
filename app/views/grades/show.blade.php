@extends('layouts.master')
@section('main')
<h1>{{$student->name}} in {{$course->classname}}</h1>

<table class="table-striped table-bordered">
<tr>
	<th>Assignment type</th>
	<th>Assignment</th>
	<th>score</th>
	<th>total possible</th>
</tr>
<tr style="background-color: red;">
	<td>Grand</td>
	<td>TOTAL</td>
	<td>{{$totals['totals'][-1]}}</td>
	<td>100</td>
</tr>
@foreach ($course->types AS $type)
	<tr style="background-color: green;">
		<td>{{$type->type}}</td>
		<td>TOTAL</td>
		<td>{{$totals['totals'][$type->id]}}</td>
		<td>100</td>
	</tr>
@endforeach
@foreach ($course->types AS $type)
	@foreach ($type->assignments AS $assignment)
		<?php
			//$recentscoremodel=$student->scores()->where('assignment_id',$assignment->id)->orderBy('date', "DESC")->first();
		?>
		<tr>
			<td>{{link_to_route('types.show', $type->type, [$type->id])}}</td>
			<td>{{link_to_action('GradesController@getSingle', $assignment->comments, ['student_id'=>$student->id,'assignment_id'=>$assignment->id])}}</td>
			<td>{{link_to_action('GradesController@getSingle', "{$totals['s'][$assignment->id]}", ['student_id'=>$student->id, 'assignment_id'=>$assignment->id])}}</td>
			<td>{{$totals['t'][$assignment->id]}}</td>
		</tr>
	@endforeach
	
	
@endforeach
</table>



@stop

