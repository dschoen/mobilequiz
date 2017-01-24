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

require_once(__DIR__."/../configuration.local.php");

try {
   
    // Get Parameters
    $question_id     = $_POST['question_id'];
    $round_id       = $_POST['round_id'];
    $action         = $_POST['action'];
    $secret         = $_POST['secret'];
    
    if ($secret != "1238dhsh27egkdad8w") {
        die(json_encode("Wrong secret"));
    }
    
    // get updated data
    switch ($action) {
        case "updateChoice":
            $data = getDataChoice($question_id, $round_id);
            break;
        case "updateNumeric":
            $data = getDataNumeric($question_id, $round_id);
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
    $choices = getChoices($question_id);

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
    $choices    = getChoices($question_id);
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

function getChoices($question_id) {
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

function getDB(){
    try {
        $db = new PDO(
            "mysql:host=".FRONTEND_DB_HOST.";dbname=".FRONTEND_DB_NAME.";charset=utf8",
            FRONTEND_DB_USER,
            FRONTEND_DB_PASS
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

?>