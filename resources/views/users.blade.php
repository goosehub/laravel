@extends('layout')

@section('content')
    <h1>List of Users!</h1>
    @foreach($users as $user)
	    <ol>
	        <li>{{ $user->name }}</li>
        </ol>
    @endforeach
@endsection