@extends('layouts.master')
@section('main')     

<h1>{{link_to_route('syllabus.show',"{$date->course->classname} ({$date->course->semester} {$date->course->year})", [$date->course_id])}}</h1>
<h2>Daily outline for {{$date->date->toDayDateTimeString()}} ({{$date->date->diffForHumans()}})</h2>
<h3 class='text-center'>{{$date->maintopic}}</h3>
<div style="background-color:pink;">
assigned today:
<ul>
@foreach ($date->assignments AS $assignment)
	<li>{{$assignment->type->type}}-{{$assignment->comments}}: {{$assignment->details}}</li>
	<li>
		<ul class='list-group'>
			@foreach ($assignment->extras AS $extra)
				<li class='list-group-item'>
					{{Markdown::Render($extra->content)}}
					{{link_to_route('extras.edit','edit', $extra->id)}}
				</li>
			@endforeach
		</ul>
	</li>
@endforeach
</ul>
<hr/>
Due today:
<ul>
@foreach ($dueassignments AS $da)
	<li>{{$da->type->type}}-{{$da->comments}}: {{$da->details}}</li>
	<li>
		<ul class='list-group'>
			@foreach ($da->extras AS $extra)
				<li class='list-group-item'>
					{{Markdown::Render($extra->content)}}
					{{link_to_route('extras.edit','edit', $extra->id)}}
				</li>
			@endforeach
		</ul>
	</li>
@endforeach

</ul>
</div>
<?php
$text=$date->details;
//fix links:
$text=preg_replace('/\[(http[^\s]+)\s([^\]]+)\]/', '[${2}](${1})', $text);
$text=preg_replace('/cq\(([0-9]+)\)/', '[concept quiz ${1}](http://physics.hamline.edu/~arundquist/cqs/cqs/${1})', $text);
?>
{{Helpers\replacegoogle($text)}}

<p>{{link_to_route('dates.edit', "edit", [$date->id])}}</p>

@stop