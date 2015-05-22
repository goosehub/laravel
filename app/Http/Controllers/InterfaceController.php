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
		$text_input = str_replace('"', '', $text_input);
		$text_input = str_replace('\'', '', $text_input);

		// Split into words
		$text_split = explode(' ', $text_input);

		// 
		// Seperate sentence into parts of speech
		// 

		$pattern = '/\.\w+/';
		preg_match_all($pattern, $text_input, $noun);
		$pattern = '/!\w+/';
		preg_match_all($pattern, $text_input, $do);
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $is);
		$pattern = '/>\w+/';
		preg_match_all($pattern, $text_input, $go);
		$pattern = '/;\w+/';
		preg_match_all($pattern, $text_input, $make);
		$pattern = '/\*\w+/';
		preg_match_all($pattern, $text_input, $adjective);
		$pattern = '/\`\w+/';
		preg_match_all($pattern, $text_input, $article);
		$pattern = '/\+\w+/';
		preg_match_all($pattern, $text_input, $cheer);
		$pattern = '/-\w+/';
		preg_match_all($pattern, $text_input, $jeer);
		$pattern = '/\+\+\w+/';
		preg_match_all($pattern, $text_input, $positive);
		$pattern = '/--\w+/';
		preg_match_all($pattern, $text_input, $negative);
		$pattern = '/\?\w+/';
		preg_match_all($pattern, $text_input, $inquiry);
		$pattern = '/@\w+/';
		preg_match_all($pattern, $text_input, $time);
		$pattern = '/#\w+/';
		preg_match_all($pattern, $text_input, $space);
		$pattern = '/\$\w+/';
		preg_match_all($pattern, $text_input, $relation);

		// 
		// Iterate through each word of the sentence
		// 

		$text_structure = [];
		foreach ($text_split as $text)
		{

			// 
			// Find structure of sentence
			// 

			// echo in_array($text, $noun);
			if (in_array($text, $noun[0]) ) { $part = $text_structure[] = $display_word_part = 'noun'; }
			else if (in_array($text, $do[0]) ) { $part = $text_structure[] = $display_word_part = 'do'; }
			else if (in_array($text, $is[0]) ) { $part = $text_structure[] = $display_word_part = 'is'; }
			else if (in_array($text, $go[0]) ) { $part = $text_structure[] = $display_word_part = 'go'; }
			else if (in_array($text, $make[0]) ) { $part = $text_structure[] = $display_word_part = 'make'; }
			else if (in_array($text, $adjective[0]) ) { $part = $text_structure[] = $display_word_part = 'adjective'; }
			else if (in_array($text, $positive[0]) ) { $part = $text_structure[] = $display_word_part = 'positive'; }
			else if (in_array($text, $negative[0]) ) { $part = $text_structure[] = $display_word_part = 'negative'; }
			else if (in_array($text, $time[0]) ) { $part = $text_structure[] = $display_word_part = 'time'; }
			else if (in_array($text, $space[0]) ) { $part = $text_structure[] = $display_word_part = 'space'; }
			else if (in_array($text, $relation[0]) ) { $part = $text_structure[] = $display_word_part = 'relation'; }
			else if (in_array($text, $inquiry[0]) ) { $part = $text_structure[] = $display_word_part = 'inquiry'; }
			else if (in_array($text, $article[0]) ) { $part = $text_structure[] = $display_word_part = 'article'; }
			else if (in_array($text, $cheer[0]) ) { $part = $text_structure[] = $display_word_part = 'cheer'; }
			else if (in_array($text, $jeer[0]) ) { $part = $text_structure[] = $display_word_part = 'jeer'; }
			else { $error = 'Does Not Computer: "' . $text . '" is not delimited'; }

			// 
			// Get word from DB, Insert if not found, increase weight if found
			// 

			$current_found = $text_data[] = DB::select('select * from `words` where word = :word and part = :part', ['word' => $text, 'part' => $part]);


			if (empty($current_found) )
			{
				array_pop($text_data);
				DB::insert('insert into `words` (word, weight, part) values (:word, :weight, :part)', ['word' => $text, 'weight' => 1, 'part' => $part]);
				$current_found = $text_data[] = DB::select('select * from `words` where word = :word and part = :part', ['word' => $text, 'part' => $part]);
			}
			else
			{
				DB::update('update `words` set weight = weight + 1 where id = :id', ['id' => $current_found[0]->id]);
			}
		}

		// 
		// Understand sentence
		// 

		// Calculate if positive or negative connontation
		// Currently does not account for double negatives
		$truth = count(array_keys($text_structure, 'negative')) > 0 ? false : TRUE;

		// Remove articles
		$found_articles = array_keys($text_structure, 'article');
		foreach ($found_articles as $found_article) { unset($text_structure[$found_article]); }

		// 
		// Insert Relations
		// 

		// $lookup = false;
		// $lookup = DB::select('select * from `lookup` where a_key = :a_key and b_key = :b_key', ['a_key' => $foo, 'b_key' => $foo]);
		// if (empty($current_found) )
		// {
		// 	DB::insert('insert into `lookup` (a_key, a_type, b_key, b_type, true, false) values (:a_key, :a_type, :b_key, :b_type)', 
		// 		['a_key' => $foo, 'a_type' => $foo, 'b_key' => $foo, 'b_type' => $foo]);
		// 	// $lookup_key = DB::getPdo()->lastInsertId();
		// 	$lookup = DB::select('select * from `lookup` where a_key = :a_key and b_key = :b_key', ['a_key' => $foo, 'b_key' => $foo]);
		// }
		// else
		// {
		// 	if ($truth) 
		// 	{
		// 		DB::update('update `lookup` set positive = positive + 1 where id = :id', ['id' => $lookup[0]->id, 'part' => $part]);
		// 	}
		// 	else
		// 	{
		// 		DB::update('update `lookup` set negative = negative + 1 where id = :id', ['id' => $lookup[0]->id, 'part' => $part]);
		// 	}
		// }

		// 
		// Debug
		// 

		var_dump($text_data);
		echo '<span class="middle_text">You said: ';
		foreach ($text_structure as $part) { echo $part . ' '; }
		echo '</span><br/>';


		// 
		// Computer response
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
