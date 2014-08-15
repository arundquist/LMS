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
@endforeach
</ul>
<hr/>
Due today:
<ul>
@foreach ($dueassignments AS $da)
	<li>{{$da->type->type}}-{{$da->comments}}: {{$da->details}}</li>
@endforeach

</ul>
</div>
<?php
$text=$date->details;
//fix links:
$text=preg_replace('/\[(http[^\s]+)\s([^\]]+)\]/', '[${2}](${1})', $text);
?>
{{Markdown::render($text)}}

<p>{{link_to_route('dates.edit', "edit", [$date->id])}}</p>

@stop