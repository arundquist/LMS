@extends('layouts.master')
@section('main')
 <h1>Login</h1>

    <!-- check for login error flash var -->
    @if (Session::has('flash_error'))
        <div id="flash_error">{{ Session::get('flash_error') }}</div>
    @endif

    {{-- Form::open(['method'=>'post', 'action'=>'UsersController@postLogin']) --}}
    <form action="https://physics.hamline.edu/~arundquist/LMS/user/login" method='post'>
      {{ csrf_field() }}
    <!-- username field -->
    <p>
        {{ Form::label('username', 'Username') }}<br/>
        {{ Form::text('username', Input::old('username')) }}
    </p>

    <!-- password field -->
    <p>
        {{ Form::label('password', 'Password') }}<br/>
        {{ Form::password('password') }}
    </p>

    <!-- submit button -->
    <p>{{ Form::submit('Login') }}</p>

    {{ Form::close() }}


@stop
