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
* Application class for Round object.
*
* @author Stephan Schulz <stschulz@mail.uni-mannheim.de>
*
*/
class Round{
	
	/**
	* This static method returns an array
	* with Round objects including the corresponding choices from the database.
	* This is used to check if a round is still active
	*
	* @return	array	Round classes
	* @param	array	round_id or quiz_id
	*
	*/
	public static function find($arr){
		global $db;
		
		// look for a specific round
		if($arr['round_id']){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_rounds WHERE round_id = :round_id");
		}
		// get all rounds that belong to a certain quiz
		else if($arr['quiz_id']){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_rounds WHERE quiz_id = :quiz_id");
		}
		else{
			throw new Exception("Unsupported question property!");
		}
		
		$st->execute($arr);
		
		$result = $st->fetchAll(PDO::FETCH_CLASS, "Round");
		
		return $result;
	}
}

?>