<?php namespace App\Http\Controllers;

use App\Models\Conversations;
use View;

class InterfaceController extends Controller {

	public function __construct()
	{
		//
	}

	public function showInterface()
	{
		return view('interface');
	}

	public function speak()
	{
		// Store data
		$text_input = $_GET['text_input'];
		$token = $_GET['_'];
		$start = $_GET['start'];
		// Format data
		$insert_user_speak = array(
		    'user' => $text_input,
		    'start' => $start,
		);
		// Insert data
		$insert = Conversations::insert($insert_user_speak);

		// Figure out response
		$computer_response = strtoupper($text_input);

		// Format data
		$insert_computer_speak = array(
		    'computer' => $computer_response,
		    'start' => $start,
		);
		// Insert data
		$insert = Conversations::insert($insert_computer_speak);

	    // Return data
		return $computer_response;
	}

}
