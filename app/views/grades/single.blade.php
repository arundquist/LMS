@extends('layouts.master')
@section('main')

<h1>{{$student->name}}</h1>
<h2>{{$assignment->type->type}}: {{$assignment->comments}}</h2>
<p>{{Markdown::Render($assignment->details)}}</p>
<p>Due date: {{$assignment->duedate}}</p>
@if (count($teammates))
	<p>Teammates:
	@foreach ($teammates AS $teammate)
		{{HTML::mailto($teammate->email,$teammate->name)}}
	@endforeach
	or {{HTML::mailto(implode(',', $teammates->lists('email')), 'email all')}}
	</p>
@endif

<h3>Activity:</h3>
@if (count($scores))
	{{Form::model(reset($scores)[0], ['action'=>['GradesController@postUpdatesingle',$student->id, $assignment->id]])}}
@else
	{{Form::open(['action'=>['GradesController@postUpdatesingle',$student->id, $assignment->id]])}}
@endif
@if ($role=='Faculty')
	<div>
		<div>

		{{Form::text('score',Input::old('score'), array('placeholder'=>'score'))}}
		{{Form::text('scoredescription',Input::old('scoredescription'), array('placeholder'=>'score description'))}}
		</div>
	</div>
@endif
<div>
	<table>
	<tr>
		<td>
		{{Form::textarea('comment','', array('placeholder'=>'New comment'))}}
		</td>
		<td>
		{{Form::text('link',Input::old('link'), array('placeholder'=>'new URL here'))}}<br/>
		{{Form::textarea('description', '', array('placeholder'=>'link description (optional)'))}}
		</td>
	</table>
	@if ($role=='Faculty')
	 {{ Form::label('old','attach to old score') }}
          {{ Form::radio('attach','old',true,array('id'=>'old')) }}
          {{ Form::label('new','attach to new score') }}
          {{ Form::radio('attach','new','',array('id'=>'new')) }}
          Note that if you're submitting something new to be graded, choose "attach to new score"
	@else
		{{Form::hidden('attach','new')}}
	@endif
	{{Form::submit('submit')}}
	{{Form::close()}}

</div>



<table class="table table-striped table-bordered">
@foreach ($scores AS $score)
	<tr><td colspan='3'>Score submitted {{$score->updated_at->diffForHumans()}} ({{$score->updated_at}})
	@if($score->score != 'pending')
		by {{$score->user->userable->name}}
	@endif
		: {{$score->score}} {{$score->description}}</td></tr>
	@if (array_key_exists($score->id, $allactivities))
		@foreach ($allactivities[$score->id] AS $activity)
			@if ($activity['type']=='Comment')
				<tr>
					<td>{{$activity['date']->diffForHumans()}} ({{$activity['date']}})</td>
					<td>{{$activity['data']->user->userable->name}}</td>
					<td>{{Markdown::render($activity['data']->comment)}}</td>
				</tr>
			@elseif ($activity['type']=='Link')
				<tr>
					<td>{{$activity['date']->diffForHumans()}} ({{$activity['date']}})</td>
					<td>{{$activity['data']->user->userable->name}}</td>
					@if ($activity['data']->description == '')
						<td>{{Markdown::render($activity['data']->link)}}</td>
					@else
						<td>{{link_to($activity['data']->link,
						$activity['data']->description)}}</td>
					@endif
				</tr>
			@endif
		@endforeach
	@endif
@endforeach
</table>

<table class='table table-striped'>
<thead>
	<tr>
		<th>Assignment type</th>
		<th>your score</th>
		<th>algorithm used</th>
	</tr>
</thead>

@foreach($course->types AS $type)
	<tr>
		<td>{{$type->type}} ({{$type->id}})</td>
		<td>{{$totals['totals'][$type->id]}}</td>
		<td>{{$type->algorithm}}</td>
	</tr>
@endforeach
</table>
Total score = {{$totals['totals'][-1]}}<br/>
course algorithm: {{$course->algorithm->algorithm}}
</div>
<div>
<h1>
@if ($role=='Faculty')
	{{link_to_route('extras.create','Resources')}}
@else
	resources
@endif
</h1>
<ul class="list-group">
@foreach ($assignment->extras AS $extra)
	<li class="list-group-item">{{Markdown::render($extra->content)}}
@if ($role=='Faculty')
	{{link_to_route('extras.edit','edit', $extra->id)}}
@endif

	</li>
@endforeach
</ul>

@stop
