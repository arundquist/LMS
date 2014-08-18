@extends('layouts.master')
@section('main')

{{Form::open(['method'=>'post', 'action'=>'UsersController@postChangepassword'])}}
{{Form::text('newpassword',null,['placeholder'=>'new password'])}}<br/>
{{Form::text('newpassword2',null,['placeholder'=>'verify'])}}<br/>
{{Form::submit('submit')}}



@stop