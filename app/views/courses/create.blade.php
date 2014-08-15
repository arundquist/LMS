@extends('layouts.master')

@section('main')

<h1>Create a new class</h1>
<h2>taught by {{Auth::user()->userable->name}}</h2>
<div>
	{{Form::open(['method'=>'post', 'action'=>'CoursesController@postCreate'])}}
	<p>{{Form::text('classname', '', ['placeholder'=>'class name'])}}</p>
	<p>{{Form::select('semester', array('fall'=>'Fall', 'winter'=>'Winter', 'spring'=>'Spring', 'summer'=>'Summer'), 'fall')}}</p>
	<p>{{Form::text('year', '', ['placeholder'=>'year'])}}</p>
	<p>{{Form::text('time', '', ['placeholder'=>'time'])}}</p>
	<p>{{Form::textarea('syllabus', '', ['placeholder'=>'syllabus'])}}</p>
	{{Form::submit('submit')}}
	{{Form::close()}}
</div>



@stop