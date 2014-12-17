@extends('layouts.master')
@section('main')
<h1>Welcome {{$user->userable->name}} ({{$user->userable_type}})</h1>


<p>Your courses {{link_to_action('CoursesController@getCreate', 'or create a new one')}}</p>
<table class='table table-striped table-bordered'>
	@foreach ($user->userable->courses AS $course)
		<tr>
			<td>{{$course->classname}}
			@if ($user->userable_type == "Faculty")
				{{link_to_action('CoursesController@getAddroster',"roster", [$course->id])}} 
				{{link_to_action('CoursesController@getAdddates',"dates", [$course->id])}} 
				{{link_to_action('CoursesController@getAlgorithms',"Algorithms", [$course->id])}}
				{{link_to_action('CoursesController@getGroups', "Groups", [$course->id])}}
				{{link_to_action('GradesController@getRecentlinksactive', "to grade", [$course->id])}}
				{{$course->classemail}}
			@endif
			
			
			</td>
			<td>{{$course->semester}} {{$course->year}}</td>
			<td>{{link_to_route('syllabus.show', 'syllabus', [$course->id])}}</td>
			@if ($user->userable_type == 'Faculty')
				<td>{{link_to_action('GradesController@getWholeclass', 
						'grades', [$course->id])}}</td>
			@elseif ($user->userable_type == 'Student')
				<td>{{link_to_action('GradesController@getStudentgrades',
				'grades', [$user->userable_id, $course->id])}}</td>
			@endif
		</tr>
	@endforeach
</table>

@stop
