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

	public function speak()
	{
		$text_input = $_GET['text_input'];
		return $text_input;
	}

}
