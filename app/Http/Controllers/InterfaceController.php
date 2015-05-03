<?php namespace App\Http\Controllers;

use App\Models\Text;
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
		// Format data
		$insert_data = array(
		    'text' => $text_input,
		);
		// Insert data
		$text = Text::insert($insert_data);
		// Get data
	    $response = Text::where('text', '=', $insert_data)->get();
	    // Return data
		return $response;
	}

}
