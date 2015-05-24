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

		// 
		// Seperate sentence into parts of speech
		// 

		$pattern = '/\.\w+/';
		preg_match_all($pattern, $text_input, $noun);
		$pattern = '/!\w+/';
		preg_match_all($pattern, $text_input, $do);
		$pattern = '/=\w+/';
		preg_match_all($pattern, $text_input, $is);
		$pattern = '/:\w+/';
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

		// Split into words
		$words = explode(' ', $text_input);

		$text_structure = [];
		foreach ($words as $text)
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

			// 
			// Find properties of word based on user suffixes
			// 

			// psuedo
			// if ($text contains symbol) { $text has X property }

		}

		// 
		// Gather information and prep sentence
		// 

		// Remove articles (for alpha only)
		$found_articles = array_keys($text_structure, 'article');
		foreach ($found_articles as $found_article) { unset($text_structure[$found_article]); }

		// Determine if positive or negative connontation
		// Currently does not account for double negatives
		$is_true = count(array_keys($text_structure, 'negative')) > 0 ? false : TRUE;
		$not_true = $is_true ? false : TRUE;

		// Find number of words in sentece
		$number_of_words = count($text_structure);

		// 
		// Iterate through words for relationships
		// 

		$context_array = '';
		for($outer_relationship_i=0; $outer_relationship_i<$number_of_words; $outer_relationship_i++) 
		{
			for($inner_relationship_i=0; $inner_relationship_i<$number_of_words; $inner_relationship_i++) 
			{
				if ($inner_relationship_i > $outer_relationship_i) 
				{
					// 
					// Find/Insert Relationships
					// 

					// Get pair being compared
					$a_key = $text_data[$outer_relationship_i][0]->id;
					$b_key = $text_data[$inner_relationship_i][0]->id;
					// Find existing relationship
					$relationship = DB::select('select * from `relationships` where a_key = :a_key and b_key = :b_key', ['a_key' => $a_key, 'b_key' => $b_key]);
					// Create relationship if not found
					if (empty($relationship) )
					{
						DB::insert('insert into `relationships` (a_key, b_key, is_true, not_true) values (:a_key, :b_key, :is_true, :not_true)', 
							['a_key' => $a_key, 'b_key' => $b_key, 'is_true' => $is_true, 'not_true' => $not_true]);
						$relationship = DB::select('select * from `relationships` where a_key = :a_key and b_key = :b_key', ['a_key' => $a_key, 'b_key' => $b_key]);
					}
					// Update truthiness
					else
					{
						if ($is_true) { DB::update('update `relationships` set is_true = is_true + 1 where id = :id', ['id' => $relationship[0]->id]); }
						else { DB::update('update `relationships` set not_true = not_true + 1 where id = :id', ['id' => $relationship[0]->id]); }
					}
					// Context
					$context_array[] = $relationship[0]->id;
				}
			}
		}

		// 
		// Iterate through relationships for context
		// 
		
		$number_of_context = count($context_array);
		for($outer_context_i=0; $outer_context_i<$number_of_context; $outer_context_i++) 
		{
			for($inner_context_i=0; $inner_context_i<$number_of_context; $inner_context_i++) 
			{
				if ($inner_context_i > $outer_context_i) 
				{
					// 
					// Find/Insert Contexts
					// 

					// Get pair being compared
					$x_key = $context_array[$outer_context_i];
					$y_key = $context_array[$inner_context_i];
					// Find existing context
					$context = DB::select('select * from `contexts` where x_key = :x_key and y_key = :y_key', ['x_key' => $x_key, 'y_key' => $y_key]);
					// Create context if not found
					if (empty($context) )
					{
						DB::insert('insert into `contexts` (x_key, y_key, weight) values (:x_key, :y_key, :weight)', 
							['x_key' => $x_key, 'y_key' => $y_key, 'weight' => 1]);
						$context = DB::select('select * from `contexts` where x_key = :x_key and y_key = :y_key', ['x_key' => $x_key, 'y_key' => $y_key]);
					}
					// Update weight
					else
					{
						DB::update('update `contexts` set weight = weight + 1 where id = :id', ['id' => $context[0]->id]);
					}
				}
			}
		}

		// 
		// Debug
		// 

		// var_dump($text_data);
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

		// 
		// Good job
		// 
	}

}
