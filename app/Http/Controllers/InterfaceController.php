<?php namespace App\Http\Controllers;

use App\Models\Conversation;
use View;
use DB;
USE PDO;

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
		// articles
		$pattern = '/\`\w+/';
		preg_match_all($pattern, $text_input, $articles);
		// cheer
		$pattern = '/\+\w+/';
		preg_match_all($pattern, $text_input, $cheers);
		// jeer
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $jeers);
		// positive
		$pattern = '/\+\+\w+/';
		preg_match_all($pattern, $text_input, $positives);
		// negative
		$pattern = '/--\w+/';
		preg_match_all($pattern, $text_input, $negatives);
		// inquiry
		$pattern = '/~\w+/';
		preg_match_all($pattern, $text_input, $inquirys);
		// time
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $times);
		// space
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $spaces);
		// relation
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $relations);

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
				$table = $text_structure[] = $display_word_part = 'noun'; 
			}
			else if (in_array($text, $verbs[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'verb'; 
			}
			else if (in_array($text, $adjectives[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'adjective'; 
			}
			else if (in_array($text, $articles[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'article'; 
			}
			else if (in_array($text, $cheers[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'cheer'; 
			}
			else if (in_array($text, $jeers[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'jeer'; 
			}
			else if (in_array($text, $positives[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'positive'; 
			}
			else if (in_array($text, $negatives[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'negative'; 
			}
			else if (in_array($text, $inquirys[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'inquiry'; 
			}
			else if (in_array($text, $times[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'time'; 
			}
			else if (in_array($text, $spaces[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'space'; 
			}
			else if (in_array($text, $relations[0]) ) { 
				$table = $text_structure[] = $display_word_part = 'relation'; 
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
				$set_last_id = DB::statement('SET @firstid := LAST_INSERT_ID()');
				$insert_id = DB::select('select id from ' . $table . ' where id = @firstid');
			}
			else
			{
				DB::update('update ' . $table . ' set weight = weight + 1 where id = ?', [$current_found[0]->id]);
			}
		}

		// Debug
		echo '<span class="middle_text">You said ';
		foreach ($text_structure as $part) { echo $part . ' '; }
		echo '</span><br/>';

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
		$insert = Conversation::insert($insert_user_speak);
		$insert_computer_speak = array(
		    'computer' => $computer_response,
		    'start' => $start,
		);
		$insert = Conversation::insert($insert_computer_speak);

		//
	    // Return response
		// 

		return $computer_response;
	}

}
