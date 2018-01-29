@extends('layouts.master')
@section('main')
{{Form::open(['method'=>'post', 'route'=>'extras.store'])}}
<p>
{{Form::textarea('content', null, ['placeholder'=>'content'])}}
</p>
<p>
{{Form::submit('submit')}}
</p>
<table class="table-striped table-bordered">
@foreach ($courses AS $course)
	<tr>
		<td>{{$course->classname}}</td>
		<td>
			<ul class="list-group">
				@foreach ($course->assignments AS $assignment)
					<li class = "list-group-item">
						{{Form::checkbox('asses[]', $assignment->id)}}
					{{$assignment->type->type}}-{{$assignment->comments}}
					</li>
				@endforeach
			</ul>
		</td>
	</tr>
@endforeach


{{Form::close()}}
@stop
