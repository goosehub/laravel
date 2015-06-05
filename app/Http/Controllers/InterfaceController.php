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
		// Translate pronouns
		$text_input = str_replace('|I', '.' . $start, $text_input);
		$text_input = str_replace('|i', '.' . $start, $text_input);
		$text_input = str_replace('|me', '.' . $start, $text_input);
		$text_input = str_replace('|my', '.' . $start, $text_input);
		$text_input = str_replace('|My', '.' . $start, $text_input);
		$text_input = str_replace('|You', '.Steve', $text_input);
		$text_input = str_replace('|you', '.Steve', $text_input);

		// 
		// Seperate sentence into parts of speech
		// 

		preg_match_all('/\.[\S]+/', $text_input, $noun);
		preg_match_all('/![\S]+/', $text_input, $do);
		preg_match_all('/=[\S]+/', $text_input, $is);
		preg_match_all('/:[\S]+/', $text_input, $go);
		preg_match_all('/;[\S]+/', $text_input, $make);
		preg_match_all('/\~[\S]+/', $text_input, $have);
		preg_match_all('/\*[\S]+/', $text_input, $adjective);
		preg_match_all('/\`[\S]+/', $text_input, $article);
		preg_match_all('/\+[\S]+/', $text_input, $cheer);
		preg_match_all('/-[\S]+/', $text_input, $jeer);
		preg_match_all('/\+\+[\S]+/', $text_input, $positive);
		preg_match_all('/--[\S]+/', $text_input, $negative);
		preg_match_all('/\?[\S]+/', $text_input, $inquiry);
		preg_match_all('/@[\S]+/', $text_input, $time);
		preg_match_all('/#[\S]+/', $text_input, $space);
		preg_match_all('/\$[\S]+/', $text_input, $relation);

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
			else if (in_array($text, $have[0]) ) { $part = $text_structure[] = $display_word_part = 'have'; }
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
			else { $part = ''; $error = 'Does Not Computer: "' . $text . '" is not delimited'; }

			// 
			// Get word from DB, Insert if not found, increase weight if found
			// 

			if ($part != 'article')
			{
				$current_found = $text_data[] = DB::select('select * from `words` where word = :word and part = :part', ['word' => $text, 'part' => $part]);
			}

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

			$plural = strpos($text, '\s');
			$possessive = strpos($text, '/s');
			$past = strpos($text, '<');
			$present = strpos($text, '^');
			$future = strpos($text, '>');
			$interval = strpos($text, '%');

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

		// Find number of words in sentence
		$number_of_words = count($text_structure);

		// 
		// Get agent action object
		// 

		$agent_key = 0;
		$action_key = 0;
		$action_type = '';
		$object_key = 0;
		$association_key = [];
		$association_type = [];
		for($connection_i=0; $connection_i<$number_of_words; $connection_i++) 
		{
			// If noun, no agent yet, and not a possessive
			if ($text_data[$connection_i][0]->part === 'noun' && $agent_key === 0 && strpos($text_data[$connection_i][0]->part,'/s') === false ) 
			{ 
				$agent_key = $text_data[$connection_i][0]->id; 
			}
			// If a verb of some kind, and no object yet
			else if ( ($text_data[$connection_i][0]->part === 'make' ||
				$text_data[$connection_i][0]->part === 'do' ||
				$text_data[$connection_i][0]->part === 'have' ||
				$text_data[$connection_i][0]->part === 'go' ||
				$text_data[$connection_i][0]->part === 'is' ) && $object_key === 0) 
			{
				$action_key = $text_data[$connection_i][0]->id;
				$action_type = $text_data[$connection_i][0]->part;
			}
			// If noun, and action is found
			else if ($text_data[$connection_i][0]->part === 'noun' && $action_key != 0) 
			{ 
				$object_key = $text_data[$connection_i][0]->id; 
			}
			else
			{
				$association_key[] = $text_data[$connection_i][0]->id;
				$association_type[] = $text_data[$connection_i][0]->part;
			}
		}

		// 
		// Get connection from DB, Insert if not found, increase weight if found
		// 
		if ($agent_key != 0 && $action_key != 0)
		{
			$current_connection = $text_data[] = DB::select('select * from `connections` 
				where agent_key = :agent_key and action_key = :action_key and action_type = :action_type and object_key = :object_key', 
				['agent_key' => $agent_key, 'action_key' => $action_key, 'action_type' => $action_type, 'object_key' => $object_key]);

			if (empty($current_connection) )
			{
				DB::insert('insert into `connections` (agent_key, action_key, action_type, object_key, is_true) 
					values (:agent_key, :action_key, :action_type, :object_key, :is_true)', 
					['agent_key' => $agent_key, 'action_key' => $action_key, 'action_type' => $action_type, 'object_key' => $object_key, 'is_true' => $is_true]);
				$current_connection = DB::select('select * from `connections` 
				where agent_key = :agent_key and action_key = :action_key and action_type = :action_type and object_key = :object_key', 
				['agent_key' => $agent_key, 'action_key' => $action_key, 'action_type' => $action_type, 'object_key' => $object_key]);
			}
			else
			{
				if ($is_true) { DB::update('update `connections` set is_true = is_true + 1 where id = :id', ['id' => $current_connection[0]->id]); }
				else { DB::update('update `connection` set not_true = not_true + 1 where id = :id', ['id' => $current_connection[0]->id]); }
			}			
		}

		// 
		// Associations of connections
		// 

		$number_of_associations = count($association_key);
		if ($association_key != 0 && isset($current_connection) )
		{
			for($association_i=0; $association_i<$number_of_associations; $association_i++) 
			// foreach ($association_key as $assoc_key)
			{
				$current_association = $text_data[] = DB::select('select * from `associations` 
					where connection_key = :connection_key and word_key = :word_key and word_type = :word_type', 
					['connection_key' => $current_connection[0]->id, 'word_key' => $association_key[$association_i], 'word_type' => $association_type[$association_i]]);

				if (empty($current_association) )
				{
					DB::insert('insert into `associations` (connection_key, word_key, word_type, weight) 
						values (:connection_key, :word_key, :word_type, :weight)', 
						['connection_key' => $current_connection[0]->id, 'word_key' => $association_key[$association_i], 
						'word_type' => $association_type[$association_i], 'weight' => 1]);
					$current_association = $text_data[] = DB::select('select * from `associations` 
					where connection_key = :connection_key and word_key = :word_key and word_type = :word_type', 
					['connection_key' => $current_connection[0]->id, 'word_key' => $association_key[$association_i], 'word_type' => $association_type[$association_i]]);
				}
				else
				{
					DB::update('update `associations` set weight = weight + 1 where id = :id', ['id' => $current_association[0]->id]);
				}
			}			
		}

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
		// Find related connections
		// 

		$agent_key = $agent_key === 0 ? 999999999 : $agent_key;
		$action_key = $action_key === 0 ? 999999999 : $action_key;
		$object_key = $object_key === 0 ? 999999999 : $object_key;

		$response_connection_query = DB::select("SELECT * FROM
			`connections` where 
			`agent_key` in (" . $agent_key . ", " . $object_key . ") 
			or 'action_key' = " . $action_key . " and 'action_type' = '" . $action_type . "' 
			or `object_key` in (" . $agent_key . ", " . $object_key . ") 
			ORDER BY `is_true` DESC
			LIMIT 2
		;");
		$response_connection_query = array_reverse($response_connection_query);
		$response_association_query = [];
		$response_agent_key = [];
		$response_action_key = [];
		$response_action_type = [];
		$response_object_key = [];
		$response_conn_is_true = [];
		$response_conn_is_false = [];
		$connection_assocaition_i = 0;
		foreach ($response_connection_query as $r_c_q_i)
		{
			$response_association_query[$connection_assocaition_i][] = DB::select("SELECT * FROM
				`associations` where 
				`connection_key` = " . $r_c_q_i->id . "
				ORDER BY `weight` DESC
			;");
			$connection_assocaition_i++;
		}

		// 
		// Formulate computer response
		// 

		$computer_response = '';

		// Agent
		if ( isset($response_connection_query[0]->agent_key) && $response_connection_query[0]->agent_key != 0 )
		{
			$current_id = $response_connection_query[0]->agent_key;
			$current_part = 'noun';
			$next_word = DB::select("SELECT * FROM `words` where `id` = " . $current_id . " and `part` = '" . $current_part . "';");
			$computer_response .= $next_word[0]->word . ' ';			
		}

		// Action
		if ( isset($response_connection_query[0]->action_key) && $response_connection_query[0]->action_key != 0 )
		{
			$current_id = $response_connection_query[0]->action_key;
			$current_part = $response_connection_query[0]->action_type;
			$next_word = DB::select("SELECT * FROM `words` where `id` = " . $current_id . " and `part` = '" . $current_part . "';");
			$computer_response .= $next_word[0]->word . ' ';
		}

		// Association
		if ( isset($response_association_query[0][0]->word_key) && $response_association_query[0][0]->weight > 2)
		{
			$current_id = $response_association_query[0][0]->word_key;
			$current_part = $response_association_query[0][0]->word_type;
			$next_word = DB::select("SELECT * FROM `words` where `id` = " . $current_id . " and `part` = '" . $current_part . "';");
			$computer_response .= $next_word[0]->word . ' ';
		}

		// Object
		if ( isset($response_connection_query[0]->object_key) && $response_connection_query[0]->object_key != 0 )
		{
			$current_id = $response_connection_query[0]->object_key;
			$current_part = 'noun';
			$next_word = DB::select("SELECT * FROM `words` where `id` = " . $current_id . " and `part` = '" . $current_part . "';");
			$computer_response .= $next_word[0]->word . ' ';
		}

		// Create pronouns
		$computer_response = str_replace('.steve', '.me', $computer_response);
		$computer_response = str_replace('.' . $start, 'you', $computer_response);
		$computer_response = str_replace('.1433', 'user_', $computer_response);

		// 
		// If no response, resort to relationships and contexts
		// 

		if ($computer_response === '')
		{

			// 
			// Prep for query input
			// 

			$in_query_string = '(';
			foreach ($text_data as $text_data_iteration) 
			{
				$word_id_array[] = $text_data_iteration[0]->id;
				$in_query_string .= $text_data_iteration[0]->id . ',';
			}
			$in_query_string = rtrim($in_query_string, ',');
			$in_query_string .= ')';

			// 
			// Do master computer response select query
			// 

			$response_query = DB::select("SELECT * FROM
				(SELECT
				  w_1.word as word_1, w_1.part as part_1, w_1.weight as weight_1, r_1.a_key as a_key_1, r_1.b_key as b_key_1, r_1.id as r_id_1, c.y_key as c_y_1,
				  w_2.word as word_2, w_2.part as part_2, w_2.weight as weight_2
				FROM
				    contexts c
				    LEFT JOIN relationships r_1 ON r_1.id = c.x_key
				    LEFT JOIN words w_1 ON w_1.id = r_1.a_key
				    LEFT JOIN words w_2 ON w_2.id = r_1.b_key
				WHERE
				    w_1.id in " . $in_query_string . "
	            OR
	            	w_2.id in " . $in_query_string . "
	            GROUP BY
	             	w_1.word
				ORDER BY
	 				r_1.is_true
	 			DESC
	            	) first

				UNION

				SELECT * FROM
				(SELECT
				  w_1.word as word_3, w_1.part as part_3, w_1.weight as weight_3,r_1.a_key as a_key_3, r_1.b_key as b_key_3,  r_1.id as r_id_3,  c.y_key as c_y_2,
				  w_2.word as word_4, w_2.part as part_4, w_2.weight as weight_4
				FROM
				    contexts c
				    LEFT JOIN relationships r_1 ON r_1.id = c.y_key
				    LEFT JOIN words w_1 ON w_1.id = r_1.a_key
				    LEFT JOIN words w_2 ON w_2.id = r_1.b_key
				WHERE
				    w_1.id in " . $in_query_string . "
	            OR
	            	w_2.id in " . $in_query_string . "
	            GROUP BY
	             	w_2.word
				ORDER BY
	 				r_1.is_true
	 			DESC
	        	) second
			;");

			// 
			// Formulate computer response
			//

			$computer_response = '';
			// Halt if no response
			if (!isset($response_query[0]->word_1) ) {}
			else
			{
				// $subject = isset($noun[0][0]) ? $noun[0][0] : $text_data[0][0]->word;
				// $computer_response = $subject . ' ' . $response_query[0]->word_1 . ' ' . $response_query[0]->word_2;
				$response_count = count($response_query);
				for ($response_i=0;$response_i<$response_count;$response_i++)
				{
					if ($response_query[$response_i]->weight_1 > 1)
					{
						$computer_response .= $response_query[$response_i]->word_1 . ' ';
					}
					else
					{
						$response_i = 99999999;
					}
				}
			}
		}

		// If nothing is returned, stay silent
		if ($computer_response === '') { $computer_response = '...'; }

		// 
		// Debug
		// 

		// echo PHP_EOL;
		// $computer_response = 'hello world';
		// var_dump($response_query);
		// var_dump($text_data);
		// echo '<span class="middle_text">You said: ';
		// foreach ($text_structure as $part) { echo $part . ' '; }
		// echo '</span><br/>';

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