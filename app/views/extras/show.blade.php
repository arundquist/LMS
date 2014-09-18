@extends('layouts.master')
@section('main')
{{Markdown::render($extra->content)}}


@stop