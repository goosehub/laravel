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
		$text_input = str_replace(':', '', $text_input);
		$text_input = str_replace('.', '', $text_input);
		$text_input = str_replace('?', '', $text_input);
		$text_input = str_replace('!', '', $text_input);
		$text_input = str_replace('"', '', $text_input);
		$text_input = str_replace('\'', '', $text_input);

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
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $nouns);
		// Debug
		echo 'nouns<br/>';
		var_dump($nouns);
		// verbs
		$pattern = '/;\w+/';
		preg_match_all($pattern, $text_input, $verbs);
		// Debug
		echo 'verbs<br/>';
		var_dump($verbs);
		// adjectives
		$pattern = '/\*\w+/';
		preg_match_all($pattern, $text_input, $adjectives);
		// Debug
		echo 'adjectives<br/>';
		var_dump($adjectives);
		$pattern = '/`\w+/';
		preg_match_all($pattern, $text_input, $articles);
		// Debug
		echo 'articles<br/>';
		var_dump($articles);
		// positive_exclamations
		$pattern = '/\+\w+/';
		preg_match_all($pattern, $text_input, $positive_exclamations);
		// Debug
		echo 'positive_exclamations<br/>';
		var_dump($positive_exclamations);
		// negative_exclamations
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $negative_exclamations);
		// Debug
		echo 'negative_exclamations<br/>';
		var_dump($negative_exclamations);
		// inquiry
		$pattern = '/~\w+/';
		preg_match_all($pattern, $text_input, $inquiry);
		// Debug
		echo 'inquiry<br/>';
		var_dump($inquiry);
		// time
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $time);
		// Debug
		echo 'time<br/>';
		var_dump($time);
		// space
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $space);
		// Debug
		echo 'space<br/>';
		var_dump($space);
		// relation
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $relation);
		// Debug
		echo 'relation<br/>';
		var_dump($relation);

		// 
		// Find structure of sentence
		// 

		$text_structure = [];
		foreach ($text_split as $text)
		{
			echo in_array($text, $nouns);
			if (in_array($text, $nouns[0]) ) { $text_structure[] = 'nouns'; }
			else if (in_array($text, $verbs[0]) ) { $text_structure[] = 'verbs'; }
			else if (in_array($text, $adjectives[0]) ) { $text_structure[] = 'adjectives'; }
			else if (in_array($text, $articles[0]) ) { $text_structure[] = 'articles'; }
			else if (in_array($text, $positive_exclamations[0]) ) { $text_structure[] = 'positive_exclamations'; }
			else if (in_array($text, $negative_exclamations[0]) ) { $text_structure[] = 'negative_exclamations'; }
			else if (in_array($text, $inquiry[0]) ) { $text_structure[] = 'inquiry'; }
			else if (in_array($text, $time[0]) ) { $text_structure[] = 'time'; }
			else if (in_array($text, $space[0]) ) { $text_structure[] = 'space'; }
			else if (in_array($text, $relation[0]) ) { $text_structure[] = 'relation'; }
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
