@extends('layouts.master')
@section('main')
<h1>{{$faculty->name}}'s Courses</h1>
<div>
<ul class="list-group">
	<li class="list-group-item">email: {{HTML::mailto($faculty->email)}}</li>
	<li class="list-group-item">office: {{$faculty->office}}</li>
	<li class="list-group-item">phone: {{HTML::link("tel: +1-{$faculty->phone}", $faculty->phone)}}</li>
</ul>
</div>
<div>
	<ul class="list-group">
		@foreach ($faculty->courses AS $course)
			<li class="list-group-item">
				{{$course->short}}
			</li>
		@endforeach
	</ul>
</div>
		






@stop