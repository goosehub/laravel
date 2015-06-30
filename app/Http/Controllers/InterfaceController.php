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
		// Get DB connection
		$pdo = DB::connection('mysql')->getPdo();

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
		// Articles, Perspectives removed because it communicates redundent information
		// 

		$text_input = str_replace('`a ', '', $text_input);
		$text_input = str_replace('`A ', '', $text_input);
		$text_input = str_replace('`an ', '', $text_input);
		$text_input = str_replace('`An ', '', $text_input);
		$text_input = str_replace('`the ', '', $text_input);
		$text_input = str_replace('`The ', '', $text_input);
		$text_input = str_replace('|s', '', $text_input);
		$text_input = str_replace('\s', ' \possesses', $text_input);
		$text_input = str_replace('/s', ' /amount', $text_input);

		// 
		// Possesses
		// 

		$text_input = str_replace('#get ', '\possesses ', $text_input);
		$text_input = str_replace('#got ', '\possesses ', $text_input);
		$text_input = str_replace('#given ', '\possesses ', $text_input);
		$text_input = str_replace('#has ', '\possesses ', $text_input);
		$text_input = str_replace('#had ', '\possesses ', $text_input);
		$text_input = str_replace('#have ', '\possesses ', $text_input);
		$text_input = str_replace('#having ', '\possesses ', $text_input);
		$text_input = str_replace('#own ', '\possesses ', $text_input);
		$text_input = str_replace('#owned ', '\possesses ', $text_input);
		$text_input = str_replace('#posesses ', '\possesses ', $text_input);
		$text_input = str_replace('#receive ', '\possesses ', $text_input);
		$text_input = str_replace('#keep ', '\possesses ', $text_input);
		$text_input = str_replace('#take ', '\possesses ', $text_input);
		$text_input = str_replace('#toke ', '\possesses ', $text_input);

		// 
		// Contractions
		// 

		$text_input = str_replace('\'t ', '--not ', $text_input);
		$text_input = str_replace('n\'t ', '--not ', $text_input);
		$text_input = str_replace('\'d ', '#would ', $text_input);
		$text_input = str_replace('\'s ', '=is ', $text_input);
		$text_input = str_replace('\'ll ', '#will ', $text_input);
		$text_input = str_replace('\'ve ', '\possesses ', $text_input);
		$text_input = str_replace('\'re ', '=are ', $text_input);

		// 
		// Translate pronouns
		// 

		$text_input = str_replace(':i', ';' . $start, $text_input);
		$text_input = str_replace(':I', ';' . $start, $text_input);
		$text_input = str_replace(':me', ';' . $start, $text_input);
		$text_input = str_replace(':Me', ';' . $start, $text_input);
		$text_input = str_replace(':my', ';' . $start, $text_input);
		$text_input = str_replace(':My', ';' . $start, $text_input);
		$text_input = str_replace(':you', ';Steve', $text_input);
		$text_input = str_replace(':You', ';Steve', $text_input);
		$text_input = str_replace(':your', ';Steve', $text_input);
		$text_input = str_replace(':Your', ';Steve', $text_input);
		$text_input = str_replace(':steve', ';Steve', $text_input);

		// 
		// Translate contractions
		// 

		// not yet coded

		// 
		// Seperate sentence into parts of speech
		// 

		preg_match_all('/;[\S]+/', $text_input, $noun);
		preg_match_all('/#[\S]+/', $text_input, $action);
		preg_match_all('/=[\S]+/', $text_input, $equate);
		preg_match_all('/\*[\S]+/', $text_input, $adjective);
		preg_match_all('/\+\+[\S]+/', $text_input, $positive);
		preg_match_all('/--[\S]+/', $text_input, $negative);
		preg_match_all('/\+[a-zA-Z][\S]+/', $text_input, $cheer);
		preg_match_all('/-[a-zA-Z][\S]+/', $text_input, $jeer);
		preg_match_all('/@[\S]+/', $text_input, $preposition);
		preg_match_all('/\?[\S]+/', $text_input, $inquiry);
		preg_match_all('/\\\[\S]+/', $text_input, $possesses);
		preg_match_all('/\/[\S]+/', $text_input, $amount);

		// 
		// Iterate through each word of the sentence
		// 

		// Split into words
		$words = explode(' ', $text_input);

		// Find number of words in sentence
		$number_of_words = count($words);

		// Declare as declarative until found otherwise
		$type_of_sentence = 'declarative';

		// Check to see if sentence is exclamatory
		if (count($cheer) + count($jeer) === 3) { $type_of_sentence = 'exclamatory'; }

		$text_structure = [];
		foreach ($words as $text)
		{
			// 
			// Find structure of sentence
			// 

			// echo in_array($text, $noun);
			if (in_array($text, $noun[0]) ) { $part = $text_structure[] = 'noun'; }
			else if (in_array($text, $action[0]) ) { $part = $text_structure[] = 'action'; }
			else if (in_array($text, $equate[0]) ) { $part = $text_structure[] = 'equate'; }
			else if (in_array($text, $adjective[0]) ) { $part = $text_structure[] = 'adjective'; }
			else if (in_array($text, $cheer[0]) ) { $part = $text_structure[] = 'cheer'; }
			else if (in_array($text, $jeer[0]) ) { $part = $text_structure[] = 'jeer'; }
			else if (in_array($text, $positive[0]) ) { $part = $text_structure[] = 'positive'; }
			else if (in_array($text, $negative[0]) ) { $part = $text_structure[] = 'negative'; }
			else if (in_array($text, $preposition[0]) ) { $part = $text_structure[] = 'preposition'; }
			else if (in_array($text, $inquiry[0]) ) { $part = $text_structure[] = 'inquiry'; }
			else if (in_array($text, $possesses[0]) ) { $part = $text_structure[] = 'possesses'; }
			else if (in_array($text, $amount[0]) ) { $part = $text_structure[] = 'amount'; }
			else { return 'Does Not Computer: "' . $text . '" is not delimited correctly'; }

			// Find is sentence is interrogative
			if (in_array('inquiry', $text_structure) ) { $type_of_sentence = 'interrogative'; }
			// Find is sentence is imperative
			else if ($type_of_sentence != 'interrogative' && $text_structure[0] === 'action') { $type_of_sentence = 'imperative'; }

			// 
			// Find properties of word based on user suffixes
			// 

			// $past = strpos($text, '<');
			// $present = strpos($text, '^');
			// $future = strpos($text, '>');
			// $interval = strpos($text, '%');

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
		if (count($noun) > 1) { $type_of_sentence = 'exclamatory'; }

		// 
		// 
		// Find and store subject verb object relationships
		// 
		// 

		// Only for sentences with information
		if ($type_of_sentence === 'declarative' || $type_of_sentence === 'exclamatory')
		{
			// Set initial defaults
			$svo = [];
			$svo_i = 0;
			$last_id = 0;
			// Set iteration defaults
			$svo[$svo_i]['exclamation'] = false;
			$svo[$svo_i]['preceding'] = $svo[$svo_i]['subject_amount'] = $svo[$svo_i]['object_amount'] = 'unknown';
			$svo[$svo_i]['possesses'] = $svo[$svo_i]['subject'] = $svo[$svo_i]['object'] = $svo[$svo_i]['action'] = $svo[$svo_i]['action_adjective'] = $svo[$svo_i]['infinitive'] =
				$svo[$svo_i]['adverb'] = $svo[$svo_i]['subject_adjective'] = $svo[$svo_i]['object_adjective'] = $svo[$svo_i]['preposition'] = '';
			$svo[$svo_i]['negative'] = $svo[$svo_i]['positive'] = $svo[$svo_i]['cheer'] = $svo[$svo_i]['jeer'] = [];
			$svo[$svo_i]['equate'] = false;
			$svo[$svo_i]['truth'] = true;
			$svo[$svo_i]['sentiment'] = 0;
			for($f_i=0; $f_i<$number_of_words; $f_i++) 
			{
				// Debug
				// echo $text_data[$f_i][0]->word;

				// Used for checking what's next in the loop
				$t_i = $f_i + 1;
				// object if noun and object not set, comparison must be done before subject
				if ($text_data[$f_i][0]->part === 'noun' && $svo[$svo_i]['subject'] != '' ) { 
					$svo[$svo_i]['object'] = $text_data[$f_i][0]->id; 
					// Find if object is plural by jumping ahead, and be sure to do check if isset first
					if (isset($text_data[$t_i][0]->part) && $text_data[$t_i][0]->part === 'amount') { 
						$svo[$svo_i]['object_amount'] = 'many'; 
					}
				}
				// subject if noun and subject isn't set
				if ($text_data[$f_i][0]->part === 'noun' && ! $svo[$svo_i]['subject'] != '' ) { $svo[$svo_i]['subject'] = $text_data[$f_i][0]->id; }
				// possesses true if possesses
				if ($text_data[$f_i][0]->part === 'possesses') { $svo[$svo_i]['possesses'] = true; }
				// action adjective if adjective and verb
				if ($text_data[$f_i][0]->part === 'adjective' && ($svo[$svo_i]['action'] === '' && $svo[$svo_i]['equate'] === '') ) { 
					$svo[$svo_i]['action_adjective'] = $text_data[$f_i][0]->id; 
				}
				// action if action and action isn't set
				if ($text_data[$f_i][0]->part === 'action' && $svo[$svo_i]['action'] === '' ) { 
					$svo[$svo_i]['action'] = $text_data[$f_i][0]->id; 
				}
				// adverb if verb and action is already set
				else if ($text_data[$f_i][0]->part === 'action' && $svo[$svo_i]['adverb'] === '' ) { 
					$svo[$svo_i]['adverb'] = $svo[$svo_i]['action'];
					$svo[$svo_i]['action'] = $text_data[$f_i][0]->id; 
				}
				// infinitive if action and adverb is already set
				else if ($text_data[$f_i][0]->part === 'action' && $svo[$svo_i]['infinitive'] === '' ) { 
					$svo[$svo_i]['infinitive'] = $svo[$svo_i]['adverb'];
					$svo[$svo_i]['adverb'] = $svo[$svo_i]['action'];
					$svo[$svo_i]['action'] = $text_data[$f_i][0]->id; 
				}
				// object adjective if adjective and action is set
				if ($text_data[$f_i][0]->part === 'adjective' && $svo[$svo_i]['action_adjective'] === '' ) { 
					$svo[$svo_i]['object_adjective'] = $text_data[$f_i][0]->id; 
				}
				// preposition if preposition
				if ($text_data[$f_i][0]->part === 'preposition') { $svo[$svo_i]['preposition'] = $text_data[$f_i][0]->id; }
				// equate if equate
				if ($text_data[$f_i][0]->part === 'equate') { $svo[$svo_i]['equate'] = true; }
				// Count cheer
				if ($text_data[$f_i][0]->part === 'cheer') { $svo[$svo_i]['cheer'][] = true; $svo[$svo_i]['exclamation'] = $text_data[$f_i][0]->id; }
				// Count jeer
				if ($text_data[$f_i][0]->part === 'jeer') { $svo[$svo_i]['jeer'][] = true; $svo[$svo_i]['exclamation'] = $text_data[$f_i][0]->id; }
				// Count positive
				if ($text_data[$f_i][0]->part === 'positive') { $svo[$svo_i]['positive'] = true; }
				// Count negative
				if ($text_data[$f_i][0]->part === 'negative') { $svo[$svo_i]['negative'] = true; }
				// If amount, set subject_amount as many
				if ($text_data[$f_i][0]->part === 'amount') { $svo[$svo_i]['subject_amount'] = 'many'; }

				// debug
				// var_dump($svo[$svo_i]);

				// If object found, or this is last loop, Subject verb object relationship complete
				if ($svo[$svo_i]['object'] != '' || $f_i === $number_of_words - 1)
				{
					// Debug
					// echo 'Relationship established. Start again<br>';

					// Find if relationship is truth
					$svo[$svo_i]['truth'] = $svo[$svo_i]['negative'] === true ? false : TRUE;
					// Find sentiment of relationship
					$svo[$svo_i]['sentiment'] = count($svo[$svo_i]['cheer']) - count($svo[$svo_i]['cheer']);

					// 
					// Insert into database
					// 

					DB::insert('insert into `relationships` (preceding, truth, sentiment, exclamation, subject, subject_amount, action_adjective, 
						possesses, adverb, infinitive, action, preposition, object_adjective, equate, object, object_amount) 
						values (:preceding, :truth, :sentiment, :exclamation, :subject, :subject_amount, :action_adjective,
							:possesses, :adverb, :infinitive, :action, :preposition, :object_adjective, :equate, :object, :object_amount)', 
						['preceding' => $last_id, 'truth' => $svo[$svo_i]['truth'], 'sentiment' => $svo[$svo_i]['sentiment'], 
						'exclamation' => $svo[$svo_i]['exclamation'], 'subject' => $svo[$svo_i]['subject'], 'subject_amount' => $svo[$svo_i]['subject_amount'], 
						'action_adjective' => $svo[$svo_i]['action_adjective'], 'possesses' => $svo[$svo_i]['possesses'], 'adverb' => $svo[$svo_i]['adverb'],
						'infinitive' => $svo[$svo_i]['infinitive'], 'action' => $svo[$svo_i]['action'], 'preposition' => $svo[$svo_i]['preposition'], 
						'object_adjective' => $svo[$svo_i]['object_adjective'], 'equate' => $svo[$svo_i]['equate'], 'object' => $svo[$svo_i]['object'], 
						'object_amount' => $svo[$svo_i]['object_amount']]);
					$last_id = $pdo->lastInsertId();

					// increment to the next one
					$svo_i++;
					// Set defaults
					$svo[$svo_i]['exclamation'] = false;
					$svo[$svo_i]['preceding'] = $svo[$svo_i]['subject_amount'] = $svo[$svo_i]['object_amount'] = 'unknown';
					$svo[$svo_i]['negative'] = $svo[$svo_i]['positive'] = $svo[$svo_i]['cheer'] = $svo[$svo_i]['jeer'] = $svo[$svo_i]['possesses'] = 
						$svo[$svo_i]['subject'] = $svo[$svo_i]['object'] = $svo[$svo_i]['action'] = $svo[$svo_i]['action_adjective'] = $svo[$svo_i]['infinitive'] = 
						$svo[$svo_i]['adverb'] = $svo[$svo_i]['subject_adjective'] = $svo[$svo_i]['object_adjective'] = $svo[$svo_i]['preposition'] = '';
					$svo[$svo_i]['equate'] = false;
					$svo[$svo_i]['truth'] = true;
					$svo[$svo_i]['sentiment'] = 0;
					// Reduce for loop pointer so that current object will be next subject
					if ($f_i != $number_of_words - 1) { $f_i--; }

					// Debug
					// $smaller_svo = $svo_i - 1;
					// echo 'End state of relationship';
					// var_dump($svo[$smaller_svo]);
					// echo 'New relationship<br>';

				}
			}
		}


		// 
		// 
		// Response
		// 
		// 

		$computer_response = '';

		// 
		// Declarative response
		// 

		// Check if is an open ended interrogative question, and if so, use the declarative response
		$open_questions = ['?how', '?why', '?what', '?who', '?where', '?when', '?which'];

		if ($type_of_sentence === 'declarative' || $type_of_sentence === 'exclamatory' || in_array($inquiry[0][0], $open_questions) )
		{
			// Construct in string
			$in_query_string = '';
			foreach ($text_data as $text_data_iteration) 
			{
				if ($text_data_iteration[0]->part === 'noun')
				{
					$in_query_string .= $text_data_iteration[0]->id . ',';
				}
			}
			$in_query_string = rtrim($in_query_string, ',');

			if ($in_query_string === '') { return '...'; }

			// Get relevant relationships without preceding
			$relevant = DB::select('SELECT * 
									FROM relationships 
									WHERE preceding = 0
									AND (
										subject IN (' . $in_query_string . ')
										OR object IN (' . $in_query_string . ') 
									)
									ORDER BY RAND()');
			if (! empty($relevant))
			{
				// Get preceding
				$preceding_id = $relevant[0]->id;
				$following = [];
				while ($preceding_id != 0)
				{
					$following[] = DB::select('SELECT * 
											FROM relationships 
											WHERE preceding = :preceding', 
											['preceding' => $preceding_id]);
					$preceding_id = isset($following[0]->id) ? $following[0]->id : 0;
				}

				// Debug
				// var_dump($relevant[0]);
				// var_dump($following);

				function get_word($word)
				{
					if ($word === '1') { return true; }
					else if ($word === '0') { return false; }
					$result = DB::select('SELECT * 
											FROM words 
											WHERE id = :id', 
											['id' => $word]);
					if (empty($word) ) { return false; }
					return $result[0]->word;
				}

				$computer_response .= get_word($relevant[0]->subject) . ' ';
				$computer_response .= get_word($relevant[0]->subject_amount) ? '/s ' : '';
				if (get_word($relevant[0]->equate))
				{
					// Find amount equate is referring to
					if (substr($computer_response, -3) === '/s ') { $computer_response .= '=are '; }
					else { $computer_response .= '=is '; }
				}
				$computer_response .= get_word($relevant[0]->truth) ? '' : '--not ';
				$computer_response .= get_word($relevant[0]->possesses) ? '#has ' : '';
				$computer_response .= get_word($relevant[0]->action_adjective) . ' ';
				$computer_response .= get_word($relevant[0]->adverb) . ' ';
				$computer_response .= get_word($relevant[0]->infinitive) . ' ';
				$computer_response .= get_word($relevant[0]->action) . ' ';
				$computer_response .= get_word($relevant[0]->object_adjective) . ' ';
				$computer_response .= get_word($relevant[0]->preposition) . ' ';
				$computer_response .= get_word($relevant[0]->object) . ' ';
				$computer_response .= get_word($relevant[0]->object_amount) ? '/s' : ' ';

				if (! empty($following[0]) )
				{		
					foreach ($following as $follow)
					{
						// $computer_response .= get_word($follow->subject) . ' ';
						$computer_response .= get_word($follow[0]->subject_amount) ? '/s ' : '';
						if (get_word($follow[0]->equate))
						{
							// Find amount equate is referring to
							if (substr($computer_response, -3) === '/s ') { $computer_response .= '=are '; }
							else { $computer_response .= '=is'; }
						}
						$computer_response .= get_word($follow[0]->truth) ? '' : '--not ';
						$computer_response .= get_word($follow[0]->possesses) ? '\\s ' : '';
						$computer_response .= get_word($follow[0]->action_adjective) . ' ';
						$computer_response .= get_word($follow[0]->adverb) . ' ';
						$computer_response .= get_word($follow[0]->infinitive) . ' ';
						$computer_response .= get_word($follow[0]->action) . ' ';
						$computer_response .= get_word($follow[0]->object_adjective) . ' ';
						$computer_response .= get_word($follow[0]->preposition) . ' ';
						$computer_response .= get_word($follow[0]->object) . ' ';
						$computer_response .= get_word($follow[0]->object_amount) ? '/s' : ' ';
					}	
				}

				// $computer_response = 'Whatever';
			}
		}

		// 
		// Interrogative response which is not open ended
		// 

		else if ($type_of_sentence === 'interrogative')
		{
			$computer_response = ':I #don\'t #know';
		}

		// 
		// Exclamatory response
		// 

		// Handeled by declarative for now
		// else if ($type_of_sentence === 'exclamatory')
		// {
		// 	$computer_response = '#tell :me @about :it';
		// }

		// 
		// Imperative response
		// 

		else if ($type_of_sentence === 'imperative')
		{
			$computer_response = ':I #can\'t !do #that';
		}


		// Create pronouns
		// Self at start of sentence creates grammar rules to be followed
		if (strpos($computer_response, ';steve', 0) )
		{		
			// Replace start self reference with I instead of me
			$computer_response = preg_replace('/^' . preg_quote(';steve', '/') . '/', ':I', $computer_response);
		}
		// Current User
		$computer_response = str_replace(';' . $start, ':you', $computer_response);
		// Past User
		$computer_response = str_replace(';1433', ':user_', $computer_response);
		// Self
		$computer_response = str_replace(':steve', ':me', $computer_response);

		$computer_response = $computer_response === '' ? '...' : $computer_response;

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