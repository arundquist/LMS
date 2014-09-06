@extends('layouts.master')
@section('main')
<div class='jumbotron'>
	<h1>Welcome!</h1>
	<p>{{HTML::link('http://screencast.com/t/62YLrskVS', 'how to use this')}}</p>
	<table class="table">
		<tr>
			<td>Syllabi for faculty
				<ul class="list-group">
					@foreach ($faculties AS $faculty)
						<li class="list-group-item">
							{{link_to_action(
							'PublicController@getSyllabi', $faculty->name,[$faculty->id])}}
						</li>
					@endforeach
				</ul>
			</td>
			<td>
				Current student or faculty?<br/>
				{{link_to_action('UsersController@getLogin', 'Log in!')}}
			</td>
		</tr>
	</table>


</div>

@stop
