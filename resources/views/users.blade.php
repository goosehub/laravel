@extends('layout')

@section('content')
    <h1>List of Users!</h1>
    @foreach($users as $user)
	    <ol>
	        <li>
		        <a href="user/{{ $user->id }}" class="lead" >{{ $user->name }}</a>
	        </li>
        </ol>
    @endforeach
@endsection