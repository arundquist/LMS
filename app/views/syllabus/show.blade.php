@extends('layouts.master')
@section('head')
{{$head}}
@stop
@section('main')
<h1>{{$course->classname}} ({{$course->semester}} {{$course->year}})</h1>
<p>{{link_to_route('syllabus.edit', "edit (faculty login required)", [$course->id])}}</p>
<table>
<tr>
@foreach ($course->faculties AS $faculty)
<td>
<ul class="list-group">
	<li class="list-group-item">{{$faculty->name}}</li>
	<li class="list-group-item">email: {{HTML::mailto($faculty->email)}}</li>
	<li class="list-group-item">office: {{$faculty->office}}</li>
	<li class="list-group-item">phone: {{HTML::link("tel:+1-{$faculty->phone}", $faculty->phone)}}</li>
</ul>
</td>
@endforeach
</tr>
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

@foreach ($course->dates()->orderBy('date')->get() AS $date)
	@if ($date->date->diffInDays()<8)
		{{link_to_route('dates.show', "{$date->date->diffForHumans()}: {$date->maintopic}", [$date->id])}}<br/>
	@else
		{{link_to_route('dates.show', "{$date->date->toDateString()}: {$date->maintopic}", [$date->id])}}<br/>
	@endif
		@endforeach
</div>
</div>

@stop