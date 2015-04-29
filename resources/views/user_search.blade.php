@extends('layout')

@section('content')
    <h1>Search Result!</h1>
    @foreach($user_search as $user)
        <a href="user/{{ $user->id }}" class="lead" >{{ $user->name }}</a>
    @endforeach
@endsection