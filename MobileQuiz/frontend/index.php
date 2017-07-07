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
 * This is the index file of the MobileQuiz frontend.
 * It will route requests to the appropriate controllers
*/

// Change this to the path of the ilDBConnector, if you change the Frontendends position
require_once(__DIR__."/../interface/ilDBConnector.php");

require_once "includes/config.php";
require_once "includes/connect.php";
require_once "includes/helpers.php";
require_once "includes/models/question.model.php";
require_once "includes/models/choice.model.php";
require_once "includes/models/quiz.model.php";
require_once "includes/models/answer.model.php";
require_once "includes/models/round.model.php";
require_once "includes/controllers/home.controller.php";
require_once "includes/controllers/quiz.controller.php";
require_once "assets/markdown/Markdown.inc.php";

try {

	if($_GET['quiz_id'] && $_GET['round_id']){
		$c = new QuizController();
		$c->handleRequest();
	}
	else if(isset($_POST['submit']) && !empty($_POST['submit'])) {
		$c = new QuizController();
		$action = $_POST['submit'];
		switch($action) {
			case 'submitAnswers' : $c->submitAnswers();break;
			// case 'doSomethingElse' : doSomethingElse();break;
			// ...etc...
		}
	}
	// fall back to home controller. Here could be something listed like public quizzes
	// if that is ever wanted.
	else if(empty($_GET)){
		$c = new HomeController();
		$c->handleRequest();
	}
	else throw new Exception('Sorry, there is no such page.');

}
catch(Exception $e) {
	// Display the error page using the "render()" helper function:
	render('error',array('message'=>$e->getMessage()));
}

?>