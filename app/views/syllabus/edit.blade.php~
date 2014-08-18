@extends('layouts.master')
@section('main')
{{Form::model($course, ['route'=>['syllabus.update', $course->id], 'method'=>'put'])}}
<div>
{{Form::label('classname', 'Class Name')}}
{{Form::text('classname')}}
</div>
<div>
{{Form::label('syllabus', 'Syllabus')}}<br/>
{{Form::textarea('syllabus')}}
</div>
<div>
{{Form::submit('submit')}}
</div>
{{Form::close()}}



@stop