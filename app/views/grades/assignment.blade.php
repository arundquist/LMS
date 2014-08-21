@extends('layouts.master')
@section('main')
<?php
if (Session::get('scorelist'))
	$scorelist=Session::get('scorelist');
else
	$scorelist=array();
?>
<h1>{{$course->short}}</h1>
<h2>{{$assignment->type->type}}: {{$assignment->comments}}</h2>
{{Form::open(['method'=>'post', 'action'=>['GradesController@postAssignment', $assignment->id]])}}
{{Form::text('description', Session::get('description'), ['placeholder'=>'description'])}}
<table class="table-striped table-bordered">
<tbody>
@foreach ($students AS $student)
<tr>
	<td>{{$student->name}}</td>
	@if (array_key_exists($student->id, $scorelist))
		<td>{{Form::text("score[$student->id]", $scorelist[$student->id]['score'])}}
			{{Form::hidden("scoreids[$student->id]", $scorelist[$student->id]['id'])}}</td>
	@else
		<td>{{Form::text("score[$student->id]")}}
			{{Form::hidden("scoreids[$student->id]", '')}}</td>
	@endif
</tr>
@endforeach
</tbody>
</table>
{{Form::submit('submit')}}

@if(Session::get('return'))
yep, I'm back
@endif

@stop