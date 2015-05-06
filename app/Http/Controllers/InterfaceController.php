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
		// 
		// Store data
		// 

		$error = false;
		$text_input = $original_input = $_GET['text_input'];
		// $text_input = '-nouns >verbs :adjectives &conjunctions @determiners #exclamations ;adverbs =pronouns $interjections';
		$token = $_GET['_'];
		$start = $_GET['start'];

		// 
		// Format data
		// 

		$text_input = strtolower($text_input);
		$text_input = str_replace(',', '', $text_input);
		$text_input = str_replace('.', '', $text_input);
		$text_input = str_replace('?', '', $text_input);
		$text_input = str_replace('!', '', $text_input);

		// 
		// Seperate sentence into words
		// 

		$text_split = explode(' ', $text_input);
		var_dump($text_split);

		// 
		// Seperate sentence into parts of speech
		// 

		// nouns
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $nouns);
		var_dump($nouns);
		// verbs
		$pattern = '/>\w+/';
		preg_match_all($pattern, $text_input, $verbs);
		var_dump($verbs);
		// adjectives
		$pattern = '/:\w+/';
		preg_match_all($pattern, $text_input, $adjectives);
		var_dump($adjectives);
		// conjunctions
		$pattern = '/&\w+/';
		preg_match_all($pattern, $text_input, $conjunctions);
		var_dump($conjunctions);
		// determiner
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $determiner);
		var_dump($determiner);
		// exclamations
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $exclamations);
		var_dump($exclamations);
		// adverbs
		$pattern = '/;\w+/';
		preg_match_all($pattern, $text_input, $adverbs);
		var_dump($adverbs);
		// pronouns
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $pronouns);
		var_dump($pronouns);
		// interjections
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $interjections);
		var_dump($interjections);

		// 
		// Find structure of sentence
		// 

		$text_structure = [];
		foreach ($text_split as $text)
		{
			echo in_array($text, $nouns);
			if (in_array($text, $nouns[0]) ) { $text_structure[] = 'noun'; }
			else if (in_array($text, $verbs[0]) ) { $text_structure[] = 'verb'; }
			else if (in_array($text, $adjectives[0]) ) { $text_structure[] = 'adjective'; }
			else if (in_array($text, $conjunctions[0]) ) { $text_structure[] = 'conjunction'; }
			else if (in_array($text, $determiner[0]) ) { $text_structure[] = 'determiner'; }
			else if (in_array($text, $exclamations[0]) ) { $text_structure[] = 'exclamation'; }
			else if (in_array($text, $adverbs[0]) ) { $text_structure[] = 'adverb'; }
			else if (in_array($text, $pronouns[0]) ) { $text_structure[] = 'pronoun'; }
			else if (in_array($text, $interjections[0]) ) { $text_structure[] = 'interjection'; }
			else { $error = TRUE; }
		}
		var_dump($text_structure);

		// 
		// Form response
		// 

		$computer_response = $original_input;

		// 
		// Enter conversation
		// 

		$insert_user_speak = array(
		    'user' => $original_input,
		    'start' => $start,
		);
		$insert = Conversations::insert($insert_user_speak);
		$insert_computer_speak = array(
		    'computer' => $computer_response,
		    'start' => $start,
		);
		$insert = Conversations::insert($insert_computer_speak);

		//
	    // Return response
		// 
		
		return $computer_response;
	}

}
