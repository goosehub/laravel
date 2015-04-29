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

}
