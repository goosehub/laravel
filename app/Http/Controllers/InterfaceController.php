<?php namespace App\Http\Controllers;

class InterfaceController extends Controller {

	public function __construct()
	{
		// $this->middleware('guest');
	}

	public function showInterface()
	{
		return view('interface');
	}

}
