<?php namespace App\Http\Controllers;

use App\Models\User;
use View;

class UserController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{		
	    $users = \App\Models\User::all();

	    return View::make('users')->with('users', $users);
	}
	
	public function showProfile($id)
	{
	    return view('profile', ['user' => User::findOrFail($id)]);
	}
	public function findProfile()
	{
	    $name = $_GET['get_user'];

	    $user_search = User::where('name', '=', $name)->get();

	    return View::make('user_search')->with('user_search', $user_search);
	}

}
