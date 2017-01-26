<?php
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/configuration.php");

class ilObjMobileQuizWizard {
	
	/**
	 * Checks for valid tags. And deletes not allowed <html> tags and every <php> tag.
	 * 
	 * @param unknown_type $validate
	 * @return $validate
	 */
	public function inputSecurity($validate=null) {
		// get allowed Tags
		$allowded_tags = ALLOWED_TAGS;
		if ($validate == null) {
			foreach ($_REQUEST as $key => $val) {
				if (is_string($val)) {
					$_REQUEST[$key] = strip_tags($val,$allowded_tags);
				} else if (is_array($val)) {
					$_REQUEST[$key] = $this->inputSecurity($val);
				}
			}
			foreach ($_GET as $key => $val) {
				if (is_string($val)) {
					$_GET[$key] = strip_tags($val,$allowded_tags);
				} else if (is_array($val)) {
					$_GET[$key] = $this->inputSecurity($val);
				}
			}
			foreach ($_POST as $key => $val) {
				if (is_string($val)) {
					$_POST[$key] = strip_tags($val,$allowded_tags);
				} else if (is_array($val)) {
					$_POST[$key] = $this->inputSecurity($val);
				}
			}
		} else {
			foreach ($validate as $key => $val) {
				if (is_string($val)) {
					$validate[$key] = strip_tags($val,$allowded_tags);
				} else if (is_array($val)) {
					$validate[$key] = $this->inputSecurity($val);
				}
				return $validate;
			}
		}
	}
	
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
				$this->loadAnswerMultipleChoice($question_id, $tpl, $model);
				break;
			case QUESTION_TYPE_SINGLE:
				$this->loadAnswerSingleChoice($question_id, $tpl, $model);
				break;
			case QUESTION_TYPE_NUM:
				$this->loadAnswerNumericChoice($question_id, $tpl, $model);
				break;
		}		
		
	}
	
	// -----------------------------------------------------------------------------------
	
	public function loadAnswerMultipleChoice($question_id, $tpl, $model) {
		// Get the questions' choices from the database
		$choices = $model->getChoices($question_id);
		$result = array();
		$i = "1";
		
		$tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_SINGLE_CHOICE_BLOCK", 'style="display:none;"');
		$tpl->setVariable("SELECTED_MULTIPLE",'selected="selected"');
		
		if(!count($choices) == 0) {
			foreach($choices as $choice){
				$tpl->setCurrentBlock("multiple_choice_block");
                                
                // escape curvy brackets, so that ILIAS cannot use them as placeholder               
				$choice['text'] = ilObjMobileQuizHelper::escapeCurvyBrackets($choice['text']);
                                                                
				$tpl->setVariable("MUL_SHOW", "");
				$tpl->setVariable("MUL_TEXT", $choice['text']);
				$tpl->setVariable("MUL_ID", $choice['choice_id']);
				$tpl->setVariable("MUL_COU", $i);
				$tpl->setVariable("MUL_DEL", false);
				$tpl->setVariable("MUL_TYPE_C", ($choice['correct_value'] == 1) ? "checked" : "");
				$tpl->setVariable("MUL_TYPE_N", ($choice['correct_value'] == 2) ? "checked" : "");
				$tpl->setVariable("MUL_TYPE_I", ($choice['correct_value'] == 0) ? "checked" : "");			
				$tpl->setVariable("ROW_ID", $choice['choice_order']);
				$tpl->parseCurrentBlock();
				
				$i++;
			}
		}
	}
	
	// -------------------------------------------------------------------------
	
	public function loadAnswerSingleChoice($question_id, $tpl, $model) {
		// Get the questions' choices from the database
		$choices = $model->getChoices($question_id);
		$result = array();
		$i = "1";
	
		$tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_MULTIPLE_CHOICE_BLOCK", 'style="display:none;"');
		$tpl->setVariable("SELECTED_SINGLE", 'selected="selected"');
		
		if(!count($choices) == 0) {
			foreach($choices as $choice){
				$tpl->setCurrentBlock ( "single_choice_block" );
				
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
		$tpl->setVariable("HIDE_SINGLE_CHOICE_BLOCK", 'style="display:none;"');
		$tpl->setVariable("HIDE_MULTIPLE_CHOICE_BLOCK", 'style="display:none;"');
		
	}
	
	// -------------------------------------------------------------------------------------
	
	/**
	* Change the submitted question infos 
	* and call functions to change the choices
	*
	* @param unknown_type $model
	*/
	public function changeQuestionAndAnswers($model) {
		//check input for bad syntax
		$this->inputSecurity();
			
		
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
				$this->updateChoiceMulti($model, $question_id);
				break;
			case QUESTION_TYPE_SINGLE:
				$this->updateChoiceSingle($model, $question_id);
				break;
			case QUESTION_TYPE_NUM:
				$this->updateChoiceNumeric($model, $question_id);
				break;
		}
	}
	
	// -------------------------------------------------------------------------------------
	
	private function updateChoiceMulti($model, $question_id) {
		// iterate through the choices
		$i = 1;
		while(isset($_POST['choice_multiple_text'][$i])) {
			// add choice
			if($_POST['choice_multiple_id'][$i] == "") {
				// now check whether the answer was deleted
				if($_POST['choice_multiple_deleted'][$i] != true 
						&& !empty($_POST['choice_multiple_text'][$i])) {
					// create choice
					$model->createChoice($question_id, $_POST['choice_multiple_type'][$i],
											$_POST['choice_multiple_text'][$i]);
				}				
			}
			// delete choice
			else if($_POST['choice_multiple_deleted'][$i] == true) {
				$model->deleteChoice($_POST['choice_multiple_id'][$i]);
			}
			// change choice
			else {
				$model->updateChoice($_POST['choice_multiple_id'][$i], 
										$_POST['choice_multiple_type'][$i],
										$_POST['choice_multiple_text'][$i],
										$_POST['rowID'][$i]);
			}
			// increment counter
			$i++;
		}
	}
	
	// -------------------------------------------------------------------------
	
	private function updateChoiceSingle($model, $question_id) {
		// iterate through the choices
		$i = 1;
		while(isset($_POST['choice_single_text'][$i])) {
			// add choice
			if($_POST['choice_single_id'][$i] == "") {
				// now check whether the answer was deleted
				if($_POST['choice_single_deleted'][$i] != true
				&& !empty($_POST['choice_single_text'][$i])) {
					// create choice
					$model->createChoice($question_id, $_POST['choice_single_type'][$i],
					$_POST['choice_single_text'][$i]);
				}
			}
			// delete choice
			else if($_POST['choice_single_deleted'][$i] == true) {
				$model->deleteChoice($_POST['choice_single_id'][$i]);
			}
			// change choice
			else {
				$model->updateChoice($_POST['choice_single_id'][$i],
				$_POST['choice_single_type'][$i],
				$_POST['choice_single_text'][$i],
				$_POST['rowID'][$i]);
			}
			// increment counter
			$i++;
		}
	}
	
	// -------------------------------------------------------------------------
	
	private function updateChoiceNumeric($model, $question_id) {
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
}

?>