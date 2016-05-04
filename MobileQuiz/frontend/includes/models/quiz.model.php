<?php
/*
+-----------------------------------------------------------------------------+
| MobileQuiz open source                                                      |
+-----------------------------------------------------------------------------+
| Copyright 2011 Stephan Schulz                                               |
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
* Application class for Quiz object.
*
* @author Stephan Schulz <stschulz@mail.uni-mannheim.de>
*
*/
class Quiz{
	
	/**
	* This static method returns an array
	* with Quiz objects from the database.
	*
	* @return	array	Quiz classes
	* @param	array	quiz_id (optional)
	*
	*/
	public static function find($arr = array()){
		global $db;
		
		if(empty($arr)){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_quizzes");
		}
		else if($arr['quiz_id']){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_quizzes WHERE quiz_id=:quiz_id");
		}
		else{
			throw new Exception("Unsupported quiz property!");
		}
		
		$st->execute($arr);
		
		// Returns an array of Quiz objects:
		return $st->fetchAll(PDO::FETCH_CLASS, "Quiz");
	}
}

?>