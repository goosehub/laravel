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
		// Format input
		// 

		$text_input = strtolower($text_input);
		$text_input = trim($text_input);
		$error = (strlen($text_input) > 200) ? 'Does Not Computer: Too many characters' : false;
		$text_input = str_replace(',', '', $text_input);
		$text_input = str_replace('.', '', $text_input);
		$text_input = str_replace('?', '', $text_input);
		$text_input = str_replace('!', '', $text_input);

		// 
		// Seperate sentence into words
		// 

		$text_split = explode(' ', $text_input);
		// Debug
		echo 'text_split<br/>';
		var_dump($text_split);

		// 
		// Seperate sentence into parts of speech
		// 

		// nouns
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $nouns);
		// Debug
		echo 'nouns<br/>';
		var_dump($nouns);
		// verbs
		$pattern = '/>\w+/';
		preg_match_all($pattern, $text_input, $verbs);
		// Debug
		echo 'verbs<br/>';
		var_dump($verbs);
		// adjectives
		$pattern = '/:\w+/';
		preg_match_all($pattern, $text_input, $adjectives);
		// Debug
		echo 'adjectives<br/>';
		var_dump($adjectives);
		// conjunctions
		$pattern = '/&\w+/';
		preg_match_all($pattern, $text_input, $conjunctions);
		// Debug
		echo 'conjunctions<br/>';
		var_dump($conjunctions);
		// determiner
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $determiner);
		// Debug
		echo 'determiner<br/>';
		var_dump($determiner);
		// exclamations
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $exclamations);
		// Debug
		echo 'exclamations<br/>';
		var_dump($exclamations);
		// adverbs
		$pattern = '/;\w+/';
		preg_match_all($pattern, $text_input, $adverbs);
		// Debug
		echo 'adverbs<br/>';
		var_dump($adverbs);
		// pronouns
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $pronouns);
		// Debug
		echo 'pronouns<br/>';
		var_dump($pronouns);
		// interjections
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $interjections);
		// Debug
		echo 'interjections<br/>';
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
			else { $error = 'Does Not Computer: "' . $text . '" is not delimited'; }
		}
		// Debug
		echo 'text_structure<br/>';
		var_dump($text_structure);

		// 
		// Form response
		// 

		$computer_response = $original_input;

		// 
		// Error stop point
		// 

		if ($error) { return $error; }

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
