@extends('layouts.master')
@section('head')
<script>
  $('.datepick').each(function(){
    $(this).datepicker({ dateFormat: "yy-mm-dd" });
});
  </script>
@stop
@section('main')
<h1>Add dates for {{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
{{Form::open(['method'=>'post', 'action'=>['CoursesController@postAdddates', $course->id]])}}
<p>Existing dates (check if you want to delete)</p>
<ul>
@foreach ($currentdates AS $date)
	<li>
		{{$date->date->format('D m/j/y')}}
		{{Form::checkbox('deletedates[]', $date->id)}}
		{{$date->maintopic}}
	</li>
@endforeach
</ul>
<div>
	{{Form::text('adddate', null, ['placeholder'=>'add date', 'class'=>'datepick'])}}
</div>
<div>
	<p>Add a whole semester</p>
	<?php 
		$dayslist=['0'=>'Sunday', '1'=>'Monday', '2'=>'Tuesday', '3'=>'Wednesday',
				'4'=>'Thursday', '5'=>'Friday', '6'=>'Saturday'];
	?>
	<ul>
		@foreach ($dayslist AS $key=>$day)
			<li>{{Form::checkbox("days[]", $key)}}{{$day}}</li>
		@endforeach
	</ul>
	<p>
		{{Form::text('startdate', null, ['placeholder'=>'start date',
		'class'=>'datepick'])}}
		{{Form::text('enddate', null, ['placeholder'=>'end date',
		'class'=>'datepick'])}}
	</p>
</div>
{{Form::submit('submit')}}
@stop