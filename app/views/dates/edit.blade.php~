@extends('layouts.master')
@section('head')
<script>
  $('.datepick').each(function(){
    $(this).datepicker({ dateFormat: "yy-mm-dd" });
});
  </script>
@stop
@section('main')
{{Form::model($date, ['method'=>'patch', 'route'=>['dates.update', $date->id]])}}
<div>
	{{Form::label('maintopic', "Main Topic")}}
	{{Form::text('maintopic')}}	
</div>
<div>
	{{Form::label('details', "Details")}}
	{{Form::textarea('details',null,['rows'=>30, 'cols'=>80])}}
</div>
	
<h2>Assigned today:</h2>
	@foreach($date->assignments AS $assignment)
		<p>
			
			{{Form::label("asslist[$assignment->id]['comments']", "short")}}
			{{Form::text("asslist[$assignment->id][comments]", $assignment->comments)}}
		</p>
		<p>
			{{Form::label("asslist[$assignment->id]['details']", "details")}}
			{{Form::textarea("asslist[$assignment->id][details]", $assignment->details)}}      
		</p>
		
		<p>
			{{Form::label("asslist[$assignment->id]['total']", "total")}}
			{{Form::text("asslist[$assignment->id][total]", $assignment->total, ['size'=>2])}}
		
			{{Form::label("asslist[$assignment->id]['type_id']", "type")}}
			{{Form::select("asslist[$assignment->id][type_id]", $types, $assignment->type_id)}}
		
			{{Form::label("asslist[$assignment->id]['newtype']", "or new type")}}
			{{Form::text("asslist[$assignment->id][newtype]", "")}}
		
			{{Form::label("asslist[$assignment->id]['duedate']", "due date")}}
			{{Form::text("asslist[$assignment->id][duedate]", $assignment->duedate, ['class'=>'datepick'])}}
		</p>
		<hr/>
	@endforeach
		<p>
			{{Form::label("asslist[-1]['comments']", "short")}}
			{{Form::text("asslist[-1][comments]", null)}}
		</p>
		<p>
			{{Form::label("asslist[-1]['details']", "details")}}
			{{Form::textarea("asslist[-1][details]", null)}}
		</p>
		
		<p>
			{{Form::label("asslist[-1]['total']", "total")}}
			{{Form::text("asslist[-1][total]", null, ['size'=>2])}}
		
			{{Form::label("asslist[-1]['type_id']", "type")}}
			{{Form::select("asslist[-1][type_id]", $types)}}
		
			{{Form::label("asslist[-1]['newtype']", "or new type")}}
			{{Form::text("asslist[-1][newtype]", "")}}
		
			{{Form::label("asslist[-1]['duedate']", "due date")}}
			{{Form::text("asslist[-1][duedate]", null, ['class'=>'datepick'])}}
		</p>
{{Form::submit('submit')}}
{{Form::close()}}




@stop