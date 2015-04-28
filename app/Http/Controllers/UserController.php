<?php namespace App\Http\Controllers;

use App\User;
use View;

class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{		
	    $users = User::all();

	    return View::make('users')->with('users', $users);

		// return view('users');
	}
	public function showProfile($id)
	{
	    return view('profile', ['user' => User::findOrFail($id)]);
	}

}
