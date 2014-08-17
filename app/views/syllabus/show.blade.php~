@extends('layouts.master')
@section('main')
<h1>{{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
<div class='row'>
<div class='col-md-8'>
<?php
$text=$course->syllabus;
//fix links:
$text=preg_replace('/\[(http[^\s]+)\s([^\]]+)\]/', '[${2}](${1})', $text);
?>
{{Markdown::render($text)}}

</div>
<div class='col-md-4'>

@foreach ($course->dates()->orderBy('date')->get() AS $date)
	@if ($date->date->diffInDays()<8)
		{{link_to_route('dates.show', "<b>{$date->date->diffForHumans()}</b>: {$date->maintopic}", [$date->id])}}<br/>
	@else
		{{link_to_route('dates.show', "{$date->date->toDateString()}: {$date->maintopic}", [$date->id])}}<br/>
	@endif
		@endforeach
</div>
</div>

@stop