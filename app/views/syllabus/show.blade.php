@extends('layouts.master')
@section('head')
{{$head}}
@stop
@section('main')
<h1>{{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
<p>{{link_to_route('syllabus.edit', "edit (faculty login required)", [$course->id])}}</p>
<table class="table table-bordered">

@foreach ($course->faculties AS $faculty)
<tr>

	<td>{{$faculty->name}}</td>
	<td>{{HTML::mailto($faculty->email)}}</td>
	<td>{{$faculty->office}}</td>
	<td>{{HTML::link("tel:+1-{$faculty->phone}", $faculty->phone)}}</td>

</tr>
@endforeach

</table>
<div class='row'>
<div class='col-md-8'>
<?php
$text=$course->syllabus;
//fix links:
$text=preg_replace('/\[(http[^\s]+)\s([^\]]+)\]/', '[${2}](${1})', $text);
?>
{{$body}}

</div>
<div class='col-md-4'>
<ol>
@foreach ($course->dates()->orderBy('date')->get() AS $date)
	<li>
	@if ($date->date->diffInDays()<8)
		{{link_to_route('dates.show', "{$date->date->diffForHumans()}: {$date->maintopic}", [$date->id])}}
	@else
		{{link_to_route('dates.show', "{$date->date}: {$date->maintopic}", [$date->id])}}
	@endif
	</li>
@endforeach
</ol>
</div>
</div>

@stop