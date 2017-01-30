<?php
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/configuration.php");

class ilObjMobileQuizWizard {
		
	// -----------------------------------------------------------------------------------
	
	/**
	 * 
	 * Loads data to fill the form
	 * 
	 * @param int $question_id
	 * @param template $tpl
	 * @param $model
	 */
	public function loadAnswerAndQuestions($question_id, $tpl, $model) {
		$question = $model->getQuestion($question_id);
                
        // escape curvy brackets, so that ILIAS cannot use them as placeholder
		$question['text'] = ilObjMobileQuizHelper::escapeCurvyBrackets($question['text']);
		$question['solution'] = ilObjMobileQuizHelper::escapeCurvyBrackets($question['solution']);
		$question['furthermore'] = ilObjMobileQuizHelper::escapeCurvyBrackets($question['furthermore']);
                
		$tpl->setVariable("QUESTION_ID", 		$question_id);
		$tpl->setVariable("QUESTION_TEXT", 		$question["text"]);
		$tpl->setVariable("QUESTION_TYPE_2", 	$question["type"]);
		$tpl->setVariable("SOLUTION_TEXT", 		$question["solution"]);
		$tpl->setVariable("FURTHERMORE_TEXT", 	$question["furthermore"]);
		
		switch($question["type"]) {
			case QUESTION_TYPE_MULTI:
				$this->loadAnswerChoice($question_id, $tpl, $model, QUESTION_TYPE_MULTI);
				break;
			case QUESTION_TYPE_SINGLE:
				$this->loadAnswerChoice($question_id, $tpl, $model, QUESTION_TYPE_SINGLE);
				break;
			case QUESTION_TYPE_NUM:
				$this->loadAnswerNumericChoice($question_id, $tpl, $model);
				break;
			case QUESTION_TYPE_TEXT:
				$this->loadAnswerTextChoice($question_id, $tpl, $model);
				break;
		}		
		
	}
	
	// -------------------------------------------------------------------------
	
	public function loadAnswerChoice($question_id, $tpl, $model, $type) {
		// Get the questions' choices from the database
		$choices = $model->getChoices($question_id);
		$result = array();
		$i = "1";
	
		if ($type == QUESTION_TYPE_MULTI) {
			$tpl->setVariable("SELECTED_MULTIPLE",'selected="selected"');
			
		} else if ($type == QUESTION_TYPE_SINGLE) {
			$tpl->setVariable("SELECTED_SINGLE", 'selected="selected"');
		}
		
		// hide numeric and Text block
		$tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_TEXT_BLOCK", 'style="display:none;"');
		
		if(!count($choices) == 0) {
			foreach($choices as $choice){
				
				//set ilTemplate block which defines the context for the variables in the curvy brackets
				$tpl->setCurrentBlock ( "choice_block" );
				
				// escape curvy brackets, so that ILIAS cannot use them as placeholder
				$choice ['text'] = ilObjMobileQuizHelper::escapeCurvyBrackets ( $choice ['text'] );
				
				$tpl->setVariable ( "MUL_SHOW", "" );
				$tpl->setVariable ( "MUL_TEXT", $choice ['text'] );
				$tpl->setVariable ( "MUL_ID", $choice ['choice_id'] );
				$tpl->setVariable ( "MUL_COU", $i );
				$tpl->setVariable ( "MUL_DEL", false );
				$tpl->setVariable ( "MUL_TYPE_C", ($choice ['correct_value'] == 1) ? "checked" : "" );
				$tpl->setVariable ( "MUL_TYPE_N", ($choice ['correct_value'] == 2) ? "checked" : "" );
				$tpl->setVariable ( "MUL_TYPE_I", ($choice ['correct_value'] == 0) ? "checked" : "" );
				$tpl->setVariable ( "ROW_ID", $choice ['choice_order'] );
				
				$tpl->parseCurrentBlock ();
				
				$i ++;
			}
		} else {
			//if there are no choices, hide the existing choice Block
			
			//set ilTemplate block which defines the context for the variables in the curvy brackets
			$tpl->setCurrentBlock ( "choice_block" );
						
			$tpl->setVariable ( "MUL_SHOW", "none" );
			$tpl->setVariable ( "MUL_TEXT", "" );
			$tpl->setVariable ( "MUL_ID", "000" );	// set dummy id to 000
			$tpl->setVariable ( "MUL_COU", "1" );
			$tpl->setVariable ( "MUL_DEL", true );
			$tpl->setVariable ( "MUL_TYPE_C", "" );
			$tpl->setVariable ( "MUL_TYPE_N", "checked");
			$tpl->setVariable ( "MUL_TYPE_I", "" );
			$tpl->setVariable ( "ROW_ID", "1" );
			
			$tpl->parseCurrentBlock ();
		}
	}
	
	// -------------------------------------------------------------------------
	
	public function loadAnswerNumericChoice($question_id, $tpl, $model) {
		// Get the questions' choices from the database
		$choices = $model->getChoices($question_id);
		
		$numeric_values		= (explode(';',$choices[0]['text']));
		$minimum 			= $numeric_values[0];
		$maximum 			= $numeric_values[1];	
		$step 				= $numeric_values[2];
		$correct_number		= $numeric_values[3];
		$tolerance_range	= $numeric_values[4];

		$tpl->setVariable("SELECTED_NUMERIC", 'selected="selected"');
		$tpl->setVariable("CHOICE_ID", $choices[0]['choice_id']);
		$tpl->setVariable("MINIMUM_VAL", $minimum);
		$tpl->setVariable("MAXIMUM_VAL", $maximum);
		$tpl->setVariable("STEP_VAL", $step);
		$tpl->setVariable("CORRECT_VALUE_VAL", $correct_number);
		$tpl->setVariable("TOLERANCE_RANGE_VAL", $tolerance_range);
		
		$tpl->setVariable("HIDE_CHOICE_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_TEXT_BLOCK", 'style="display:none;"');		
	}
	
	// -------------------------------------------------------------------------
	
	public function loadAnswerTextChoice($question_id, $tpl, $model) {
		// Get the questions' choices from the database
		$choices = $model->getChoices($question_id);
	
		$correct_value = $choices[0]['text'];
	
		$tpl->setVariable("SELECTED_TEXT", 'selected="selected"');
		$tpl->setVariable("CHOICE_ID", $choices[0]['choice_id']);
		$tpl->setVariable("TEXT_CORRECT_VALUE", $correct_value);
	
		//hide other blocks
		$tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_CHOICE_BLOCK", 'style="display:none;"');	
	}
	
	// -------------------------------------------------------------------------------------
	
	/**
	* Change the submitted question infos 
	* and call functions to change the choices
	*
	* @param unknown_type $model
	*/
	public function changeQuestionAndAnswers($model) {
		
		// if question_id is empty, create new question, else update existing one
		if (empty($_POST['question_id'])) {
			$question_id = $model->createQuestion($_POST['text'], $_POST['type'], $_POST['solution'], $_POST['furthermore']);
		} else {
			$model->updateQuestion($_POST['question_id'], $_POST['text'], $_POST['type'],$_POST['solution'] ,$_POST['furthermore'] );
			$question_id = $_POST['question_id'];
		}
		
		// update choices
		switch($_POST['type']) {
			case QUESTION_TYPE_MULTI :
				$this->updateChoice($model, $question_id);
				break;
			case QUESTION_TYPE_SINGLE:
				$this->updateChoice($model, $question_id);
				break;
			case QUESTION_TYPE_NUM:
				$this->updateNumeric($model, $question_id);
				break;
			case QUESTION_TYPE_TEXT:
				$this->updateText($model, $question_id);
				break;
		}
	}
	
	// -------------------------------------------------------------------------------------
	
	private function updateChoice($model, $question_id) {
		
		// iterate through the choices
		$i = 1;
		while(isset($_POST['choice_text'][$i])) {
			
			// ADD NEW CHOICE if choice_id is empty
			if($_POST['choice_id'][$i] == "") {
				
				// now check whether the answer was deleted
				if($_POST['choice_deleted'][$i] != true	&& !empty($_POST['choice_text'][$i])) {
					// create choice
					$model->createChoice($question_id, $_POST['choice_type'][$i], $_POST['choice_text'][$i]);
				}
			}
			
			// DELETE CHOICE if choice_id is not empty and deleted = true
			else if (($_POST['choice_deleted'][$i] == true) && ($_POST['choice_id'][$i] != "")) {
				$model->deleteChoice($_POST['choice_id'][$i]);
			}
			
			// UPDATE CHOICE if choice_id ist not empty
			else if ($_POST['choice_id'][$i] != "") {
				$model->updateChoice($_POST['choice_id'][$i],
						$_POST['choice_type'][$i],
						$_POST['choice_text'][$i],
						$_POST['rowID'][$i]);
			} else {
				// do nothing
			}
			// increment counter
			$i++;
		}
	}
	
	// -------------------------------------------------------------------------
	
	private function updateNumeric($model, $question_id) {
		$text = $_POST['choice_numeric_minimum'].";".
			$_POST['choice_numeric_maximum'].";".
			$_POST['choice_numeric_step'].";".
			$_POST['choice_numeric_correct_value'].";".
			$_POST['choice_numeric_tol_range'];
		
		// replace comma (',') by dot ('.')
		$text = str_replace(',','.',$text);
		
		// if a correct number exists, then the quesition is a correct/not correct one
		if($_POST['correct_number']) {
			$correct_value = "1";
		} else {
			$correct_value = "2";
		}
		
		if (!empty($_POST['choice_numeric_id'])) {
			// if choice exists, then update, else create
			$model->updateChoice($_POST['choice_numeric_id'],$correct_value, $text, -1);
		} else {			
			$model->createChoice($question_id, $correct_value, $text);
		}		
	}
	
	// -------------------------------------------------------------------------
	
	private function updateText($model, $question_id) {
		$text = $_POST['choice_text_value'];
	
		// correct value is not empty the question has a correct value
		if($_POST['choice_text_value']) {
			$correct_value = "1";
		} else {
			$correct_value = "2";
		}

		if (!empty($_POST['choice_text_id'])) {
			// if choice exists, then update, else create
			$model->updateChoice($_POST['choice_text_id'],$correct_value, $text, -1);
		} else {
			$model->createChoice($question_id, $correct_value, $text);
		}
	}
	
	// -------------------------------------------------------------------------
}

?>