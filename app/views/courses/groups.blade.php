@extends('layouts.master') 
@section('head')

	<style>
		ul {-webkit-column-count:3}
		#my-div
		{
		    width    : 600px;
		    height   : 350px;
		    overflow : hidden;
		    position : relative;
		}
		 
		#my-iframe
		{
		    position : absolute;
		    top      : -200px;
		    left     : -50px;
		    width    : 1280px;
		    height   : 1200px;
		}
	</style>

@stop
@section('main')
@if (isset($roles))
	<ul>
	@foreach ($in AS $key=>$name)
		<li>{{$name}} ({{$roles[$key]}})</li>
	@endforeach
	</ul>
	<hr/>
	<div id="my-div">
	<iframe src="http://www.rrrather.com/" id="my-iframe" scrolling="no"></iframe>
	
	</div>
	From {{HTML::link('http://www.rrrather.com')}}
	<hr/>
	<ul>
	<?php asort($roles); ?>
	@foreach ($roles AS $key=>$role)
		<li>{{$role}}:  {{$in[$key]}}</li>
	@endforeach
	</ul>
@else
{{Form::open(['method'=>'post', 'action'=>['CoursesController@postGroups']])}}
<ul class="list-group">
	@foreach ($students AS $student)
		<li class="list-group-item">
			{{Form::checkbox('in[]', $student, true)}} {{$student}}
		</li>
	@endforeach
</ul>
{{Form::text('max', 4)}} {{Form::submit('submit')}}
@endif



@stop
