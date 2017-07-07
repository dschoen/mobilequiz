<?php
/*
+-----------------------------------------------------------------------------+
| MobileQuiz ILIAS plug-in for audience feedback with mobile devices          |
+-----------------------------------------------------------------------------+
| Copyright 2016 Daniel Schoen                                                |
|                                                                             |
| MobileQuiz is free software: you can redistribute it and/or modify          |
| it under the terms of the GNU General Public License as published by        |
| the Free Software Foundation, either version 3 of the License, or           |
| (at your option) any later version.                                         |
|                                                                             |
|                                                                             |
| MobileQuiz is distributed in the hope that it will be useful,               |
| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
| GNU General Public License for more details.                                |
|                                                                             |
| You should have received a copy of the GNU General Public License           |
| along with MobileQuiz.  If not, see <http://www.gnu.org/licenses/>.         |
+-----------------------------------------------------------------------------+
*/

/*
 * This is a helper file to get the information for a live and dynamic update of 
 * the charts 
*/
require_once 'ilDBConnector.php';

try {
   
    $action         = $_POST['action'];
    $secret         = $_POST['secret'];
    
    if ($secret != "1238dhsh27egkdad8w") {
        die(json_encode("Wrong secret"));
    }
    
    // get updated data
    switch ($action) {
        case "updateChoice":
        	// Get Parameters
        	$question_id    = $_POST['question_id'];
        	$round_id       = $_POST['round_id'];
            $data = getDataChoice($question_id, $round_id);
            break;
        case "updateNumeric":
        	$question_id    = $_POST['question_id'];
        	$round_id       = $_POST['round_id'];
            $data = getDataNumeric($question_id, $round_id);
            break;
        case "updateText":
        	$question_id    = $_POST['question_id'];
        	$round_id       = $_POST['round_id'];
          	$data = getDataText($question_id, $round_id);
           	break;
        case "updateNumberOfParticipants":
        	$round_id       = $_POST['round_id'];
        	$data = countParticipants($round_id);
        	break;
        case "deleteAnswer":
        	$answer_id       = $_POST['answer_id'];
        	$data = deleteAnswer($answer_id);
        	break;
    }       
    
    //return data to requester
    die(json_encode($data));

}
catch(Exception $e) {        
	die(json_encode($e->getMessage()));
}

// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------

function getDataChoice($question_id, $round_id){
    
    $return= array();
    
    // get all choices
    $choices = getChoicesOfQuestion($question_id);

    if(!count($choices) == 0) {
        $return = array();
        foreach($choices as $choice){
            $choice_id = $choice['choice_id'];

            $return[] = countAnswers($round_id, $choice_id);
        }
    }
    
    return $return;
}

// -----------------------------------------------------------------------------

function getDataNumeric($question_id, $round_id){
    
    $return= array();
    
    // get all choices
    $choices    = getChoicesOfQuestion($question_id);
    $choice     = $choices[0];
   
    $numeric_values     = (explode(';',$choices[0]['text']));
    $numeric_min        = $numeric_values[0];
    $numeric_max        = $numeric_values[1];
    $numeric_step       = $numeric_values[2];
    $numeric_correct    = $numeric_values[3];
    $numeric_tolerance  = $numeric_values[4];
    
    $return = array();
 
            
    // create the answer buckets
    for ($i = (float)$numeric_min; $i <= (float)$numeric_max; $i = $i+(float)$numeric_step) {    
        $return[(String)$i] = 0; 
    }

    // summarizing and sorting of the different answers => output: $data
    if(!count($choices) == 0) {
        
        $answers = getAnswers($choice['choice_id'] , $round_id);
        
        foreach ($answers as $answer){
            if ($answer['choice_id'] == $choice['choice_id']){
                // collects all answers and counts them
                $return[((string)$answer['value'])]++;
            }
        }            
    }
        
    // sort all answers        
    ksort($return);
        
    // return only the values
    return array_values($return);
}

// -----------------------------------------------------------------------------

function getDataText($question_id, $round_id){

	$return= array();

	// get the choice
	$choices = getChoicesOfQuestion($question_id);

	if(count($choices) == 0) {
		return $return;	
	}
		
	$choice = $choices[0];
	$choice_id = $choice['choice_id'];
	
	// get all Answers
	$datas = getAnswers($choice_id, $round_id);
		
	// Count Data for Tag Cloud weight
	$data_bucket = array();
	foreach( $datas as $data ) {
		 
		// Skip loop if data is empty
		if (empty($data)) {
			continue;
		}
		
		$word = trim($data['value']);
	
		if (array_key_exists($word, $data_bucket)) {
			$data_bucket[$word] += 1;
		} else {
			$data_bucket[$word] = 1;
		}
	}
	 
	// create data
	foreach( $data_bucket as $data => $weight ) {
		 
		$return[] = array(
				'text' => polishTextTagCloud($data), 
				'weight' => $weight
				);
	}		
	
	return $return;
}

// -----------------------------------------------------------------------------

function getChoicesOfQuestion($question_id) {
    $db = getDB();

    $st = $db->prepare("SELECT * FROM rep_robj_xuiz_choices WHERE question_id = :question_id ORDER BY choice_order");
		
    $st->execute(array(':question_id' => $question_id));
		
    return $st->fetchAll();
}

// -----------------------------------------------------------------------------

function getAnswers($choice_id, $round_id){
    
    $db = getDB();

    $st = $db->prepare("SELECT * FROM rep_robj_xuiz_answers WHERE choice_id = :choice_id AND round_id = :round_id");
		
    $st->execute(array(':choice_id' => $choice_id, ':round_id' => $round_id ));
		
    return $st->fetchAll();
}

// -----------------------------------------------------------------------------

function countAnswers($round_id, $choice_id){

	$db = getDB();

	$st = $db->prepare("Select COUNT(answer_id) as answers"
			." FROM rep_robj_xuiz_answers"
			." WHERE round_id = :round_id"
				." AND choice_id = :choice_id"
	    		." AND value > 0"
	    	.";");

	$st->execute(array(':choice_id' => $choice_id, ':round_id' => $round_id ));
	$rows = $st->fetchAll();
	$result = $rows[0];
	$count = $result["answers"];
	
	return $count;
}

// -----------------------------------------------------------------------------

function countParticipants($round_id){

	$db = getDB();

	$st = $db->prepare("Select COUNT(DISTINCT user_string) as answers"
			." FROM rep_robj_xuiz_answers"
			." WHERE round_id = :round_id"
			.";");

	$st->execute(array(':round_id' => $round_id ));
	$rows = $st->fetchAll();
	$result = $rows[0];
	$count = $result["answers"];

	return $count;
}

// -----------------------------------------------------------------------------

function deleteAnswer($answer_id){

	$db = getDB();
	
	$st = $db->prepare("DELETE FROM rep_robj_xuiz_answers "
			." WHERE answer_id = :answer_id"
			.";");

	$st->execute(array(':answer_id' => $answer_id ));

	return;
}

// -----------------------------------------------------------------------------

function getDB(){
	
	$dbConfig = new ilDBConnector();
	
    try {
        $db = new PDO(
            "mysql:host=".$dbConfig->HOST.";dbname=".$dbConfig->NAME.";charset=utf8",
        		$dbConfig->USER,
        		$dbConfig->PASS
            );

        $db->query("SET NAMES 'utf8'");

        // one of 3 PDO error modes: throw exception instead of being silent or warning
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;

    } catch(PDOException $e) {
        error_log("MobileQuiz frontend could not connect to database:");
        error_log($e->getMessage());

        die("MobileQuiz encountered a database error:".$e->getMessage());
    }
}

// -----------------------------------------------------------------------------
// -------------------------------------------------------------------------

/**
 * Gets a String and transforms it to a web ready version, without Markdown transformation
 *
 * @author dschoen
 * @param String $text
 */
function polishTextTagCloud($text) {

	// remove critical charackters
	$text = htmlspecialchars($text);

	// Create html line breaks
	$text = nl2br($text);

	// remove all original line breaks
	$text = str_replace(array("\r","\n"), "", $text);
	 
	// remove all line breaks
	$text = str_replace(array("<br />"), " ", $text);

	return $text;
}

?>