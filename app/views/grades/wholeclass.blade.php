@extends('layouts.master')
@section('main')

<h1>{{$course->classname}}</h1>

<table class="table-striped table-bordered">
<thead>
<tr>
	<th>Student</th>
	<th>Total grade</th>
	@foreach ($course->types AS $type)
		<th>{{$type->type}} Total</th>
	@endforeach
	
	@foreach ($course->types AS $type)
		@foreach ($type->assignments AS $assignment)
			<th>{{$type->type}}<br/>{{link_to_action('GradesController@getAssignment', $assignment->comments, $assignment->id)}}</th>
		@endforeach
	@endforeach
</tr>
</thead>
@foreach ($course->students AS $student)
<tr>
	<td>{{link_to_action('GradesController@getStudentgrades', $student->name, [$student->id, $course->id])}}</td>
	<td>{{number_format($alltotals[$student->id]['totals'][-1],2)}}</td>
	@foreach ($course->types AS $type)
		<td>{{number_format($alltotals[$student->id]['totals'][$type->id],2)}}</td>
	@endforeach
	
	@foreach ($course->types AS $type)
		@foreach ($type->assignments AS $assignment)
			@if (array_key_exists($assignment->id, $alltotals[$student->id]['s']))
				<td>{{link_to_action('GradesController@getSingle', !is_null($alltotals[$student->id]['s'][$assignment->id])?$alltotals[$student->id]['s'][$assignment->id]:'--',[$student->id, $assignment->id])}}</td>
			@else
				<td></td>
			@endif
		@endforeach
	@endforeach
</tr>
@endforeach




</table>

@stop

