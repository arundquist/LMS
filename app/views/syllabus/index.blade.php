@extends('layouts.master')
@section('main')
<h1>Courses</h1>
<ul>
@foreach ($courses AS $course)
	<li>{{link_to_route('syllabus.show',  "{$course->year} {$course->semester}: {$course->classname}",[$course->id])}}</li>
@endforeach
</ul>

@stop