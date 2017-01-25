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

/**
 * This controller handles the quiz.
 *
 */
class QuizController{

    /**
     * This is the main method to display the quiz
     */
    public function handleRequest(){

        // check if the quiz round is still active, if not break
        if(!isRoundActive($_GET['round_id'])){
            render('abuse',array(
                'title'     => 'Quiz is inactive'
            ));
        }

        $quiz = Quiz::find(array('quiz_id'=>$_GET['quiz_id']));

        if(empty($quiz)){
            throw new Exception("There is no such quiz!");
        }

        // Fetch all the questions in this quiz:
        $questions = Question::find(array('quiz_id'=>$_GET['quiz_id']));

        // $questions are arrays with objects
        render('quiz',array(
            'title'		=> 'Quiz: '.$quiz[0]->name,
            'questions'		=> $questions,
        ));
    }

    // -------------------------------------------------------------------------
    
    /**
     * This will insert the answers into the database, if the form was sent with a POST request.
     */
    public function submitAnswers(){
        global $db;
        $sql = $db->prepare("SELECT type FROM rep_robj_xuiz_rounds WHERE round_id = :id" );
        $sql->execute(array(":id" => $_POST['round_id']));
        $row = $sql->fetchAll(PDO::FETCH_CLASS, "Round");

        // check if there is a cookie for this round already, then break
        if(isset($_COOKIE["round".$_POST['round_id']]) && $row[0]->type != "passive"){
            //if(false){
            render('abuse',array(
                'title'			=> 'Quiz already submitted'
            ));

            // check if the quiz round is still active, if not break
        }elseif(!isRoundActive($_POST['round_id']) && $row[0]->type != "passive"){
            render('abuse',array(
                'title'			=> 'Quiz round already closed'
            ));

            // if we get to this, we assume it is the first time that user submits
            // for this round and the quiz ist still active
        }else{
            // store cookie
            $user_string = getGuid();
            $expire=time()+60*60*24*365; // valid for a year
            setcookie("round".$_POST['round_id'], $user_string, $expire);

            // first create answer objects for every choice of all questions of a quiz having 0 as value.
            $answers;
            $questions = Question::find(array('quiz_id'=>$_POST['quiz_id']));
            foreach($questions as $question){
                foreach ($question->choices as $choice){
                    $new_answer = new Answer($_POST['round_id'], $choice->choice_id, 0, $user_string);
                    $answers[] = $new_answer;
                }
            }

            $result = array();
            // If you want to add new question types, you must modify this file here.
            // then change the ones the user selected in the quiz to have $value of 1
            foreach($_POST as $name => $value){
                switch(substr($name, 0, 5)) {
                    case "check":	// multiple choice
                        foreach ($answers as $answer){
                            if ($answer->choice_id == $value){
                                $answer->value = 1;
                            }
                        }
                        break;
                    case "radio":	// single choice
                        foreach ($answers as $answer){
                            if ($answer->choice_id == $value){
                                $answer->value = 1;
                            }
                        }
                        break;
                    case "numer":	// numeric
                        foreach ($answers as $answer){
                            // Here we cannot use the value of the form to identify the choice, because
                            // the value contents the numeric answer. Therefore we use the name of the
                            // form to identity the choice. The formname is 'numeric-1234'.
                            if ($answer->choice_id == substr($name, 8)){
                                $answer->value = $value;
                            }
                        }
                        break;
                }
                $result[$name] = $answer->value;
            }


            // now save all answers to database
            foreach($answers as $answer){
                Answer::doCreate((array)$answer);
            }

            global $db;
            $sql = $db->prepare("SELECT type FROM rep_robj_xuiz_rounds WHERE round_id = :id" );
            $sql->execute(array(":id" => $_POST['round_id']));
            $row = $sql->fetchAll(PDO::FETCH_CLASS, "Round");
            
            $questions = Question::find(array('quiz_id'=>$_POST['quiz_id']));
            render('success',array(
                'title'     	=> 'Quiz submitted',
                'type'      	=> $row[0]->type,
                'questions' 	=> $questions,
                'result'    	=> $answers
            ));
        }

    }

}

?>