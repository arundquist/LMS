@extends('layouts.master')
@section('main')
<h1>Roster for {{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
{{Form::open(['method'=>'post', 'action'=>['CoursesController@postAddroster', $course->id]])}}
<div class="col-md-3">
<p>Current Students</p>
<ul class="list-group">
@foreach ($students AS $student)
	<li class="list-group-item">{{Form::checkbox('delete[]', $student->id)}} {{$student->name}} 
					{{HTML::mailto($student->email)}} {{$student->user->username}}</li>
@endforeach
</ul>
</div>

<div class="col-md-3">
<div>{{Form::text('name', null, ['placeholder'=>'new student name'])}}</div>
<div>{{Form::text('username', null, ['placeholder'=>'hamline id'])}}</div>
<div>{{Form::text('email', null, ['placeholder'=>'email'])}}</div>



</div>

<div class="col-md-6">
{{Form::textarea('roster', null, ['placeholder'=>'paste in summary view page source',
'rows'=>20, 'cols'=>80])}}

</div>
{{Form::submit('submit')}}




@stop