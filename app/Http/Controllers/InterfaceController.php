<?php namespace App\Http\Controllers;

use App\Models\Conversations;
use View;
use DB;

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
		$token = $_GET['_'];
		$start = $_GET['start'];

		// 
		// Format input
		// 

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

		// 
		// Seperate sentence into parts of speech
		// 

		// nouns
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $nouns);
		// verbs
		$pattern = '/;\w+/';
		preg_match_all($pattern, $text_input, $verbs);
		// adjectives
		$pattern = '/\*\w+/';
		preg_match_all($pattern, $text_input, $adjectives);
		$pattern = '/`\w+/';
		preg_match_all($pattern, $text_input, $articles);
		// positive_exclamations
		$pattern = '/\+\w+/';
		preg_match_all($pattern, $text_input, $positive_exclamations);
		// negative_exclamations
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $negative_exclamations);
		// inquiry
		$pattern = '/~\w+/';
		preg_match_all($pattern, $text_input, $inquiry);
		// time
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $time);
		// space
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $space);
		// relation
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $relation);

		// 
		// 
		// Iterate through each word of the sentence

		$text_structure = [];
		$text_data = [];
		foreach ($text_split as $text)
		{

			// 
			// Find structure of sentence
			// 

			echo in_array($text, $nouns);
			if (in_array($text, $nouns[0]) ) { 
				$text_structure[] = $display_word_part = 'noun';
				$table = 'nouns'; 
			}
			else if (in_array($text, $verbs[0]) ) { 
				$text_structure[] = $display_word_part = 'verb';
				$table = 'verbs'; 
			}
			else if (in_array($text, $adjectives[0]) ) { 
				$text_structure[] = $display_word_part = 'adjective';
				$table = 'adjectives'; 
			}
			else if (in_array($text, $articles[0]) ) { 
				$text_structure[] = $display_word_part = 'article';
				$table = 'articles'; 
			}
			else if (in_array($text, $positive_exclamations[0]) ) { 
				$text_structure[] = $display_word_part = 'positive_exclamation';
				$table = 'positive_exclamations'; 
			}
			else if (in_array($text, $negative_exclamations[0]) ) { 
				$text_structure[] = $display_word_part = 'negative_exclamation';
				$table = 'negative_exclamations'; 
			}
			else if (in_array($text, $inquiry[0]) ) { 
				$text_structure[] = $display_word_part = 'inquiry';
				$table = 'inquiry'; 
			}
			else if (in_array($text, $time[0]) ) { 
				$text_structure[] = $display_word_part = 'time';
				$table = 'time'; 
			}
			else if (in_array($text, $space[0]) ) { 
				$text_structure[] = $display_word_part = 'space';
				$table = 'space'; 
			}
			else if (in_array($text, $relation[0]) ) { 
				$text_structure[] = $display_word_part = 'relate';
				$table = 'relate'; 
			}
			else { 
				$error = 'Does Not Computer: "' . $text . '" is not delimited';
			}

			// 
			// Get word from DB, Insert if not found, increase weight if found
			// 

			$current_found = $text_data[] = DB::select('select * from ' . $table . ' where word = :word', ['word' => $text]);
			if (empty($current_found) )
			{
				DB::insert('insert into ' . $table . ' (word, weight) values (:word, :weight)', ['word' => $text, 'weight' => 1]);
				$test = DB::statement('SET @firstid := LAST_INSERT_ID()');
				$insert_id = DB::select('select id from ' . $table . ' where id = @firstid');
			}
			else
			{
				DB::update('update ' . $table . ' set weight = weight + 1 where id = ?', [$current_found[0]->id]);
			}
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
