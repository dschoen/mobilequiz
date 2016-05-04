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
* Application class for Choice object.
*
* @author Stephan Schulz <stschulz@mail.uni-mannheim.de>
*
*/
class Choice{
	
	/**
	* This static method returns an array
	* with Choice objects from the database.
	*
	* @return	array	Choice classes
	* @param	array	id or choice_id
	*
	*/
	public static function find($arr){
		global $db;
		
		// look for a specific choice
		if($arr['choice_id']){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_choices WHERE choice_id=:choice_id");
		}
		// get all choices that belong to a certain question
		else if($arr['question_id']){
			$st = $db->prepare("SELECT * FROM rep_robj_xuiz_choices WHERE question_id = :question_id ORDER BY choice_order");
		}
		else{
			throw new Exception("Unsupported choice property!");
		}
		
		$st->execute($arr);
		
		return $st->fetchAll(PDO::FETCH_CLASS, "Choice");
	}
}

?>