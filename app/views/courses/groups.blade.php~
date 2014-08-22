@extends('layouts.master') 
@section('head')

	<style>
		ul {-webkit-column-count:3}
	</style>

@stop
@section('main')
@if (isset($roles))
	<ul>
	@foreach ($in AS $key=>$name)
		<li>{{$name}} ({{$roles[$key]}})</li>
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
