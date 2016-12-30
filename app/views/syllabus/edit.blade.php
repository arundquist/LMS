@extends('layouts.master')
@section('main')
{{Form::model($course, ['route'=>['syllabus.update', $course->id], 'method'=>'put'])}}
{{link_to_route('syllabus.show', 'student view', [$course->id])}}
<div>
{{Form::label('classname', 'Class Name')}}
{{Form::text('classname')}}
</div>
<div class="col-md-8">
{{Form::label('syllabus', 'Syllabus')}}<br/>
{{Form::textarea('syllabus')}}
</div>


<div class="col-md-4">
<ul class="list-group">
@foreach($course->dates AS $date)
	<li class="list-group-item">
		{{$date->date->format('D M j')}}:{{Form::text("topics[$date->id]", $date->maintopic)}}
	</li>
@endforeach
</ul>


</div>
<div>
{{Form::submit('submit')}}
</div>
{{Form::close()}}



@stop
