@extends('layout')

@section('content')
    Users!
    @foreach($users as $user)
        <p>{{ $user->name }}</p>
    @endforeach
@stop