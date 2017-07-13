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
	
	
	/**
	 * Returns true if round exists and is active:
	 * Round is active if it has no ending date
	 *
	 * @param	int		round_id
	 * @return	boolean
	 */
	public static function isActive($round_id) {
		$round = Round::find(array('round_id'=>$round_id));
		
		// check if round exists
		if (empty($round[0])) {
			return false;
			
		// check if round is active
		} else if (empty($round[0]->end_date)) {
			return true;
			
		} else{
			return false;
		}
	}
	
	
	/**
	 * Returns true if this round was not already submitted by this user or
	 * round is from type passive.
	 * Passive rounds can be submitted unlimited times.
	 *
	 * @param	int		round_id
	 * @return	boolean
	 */
	public static function isSubmitable($round_id){
		$round = Round::find(array('round_id'=>$round_id));
		$type = $round[0]->type;
		if ($type == "passive") {
			return true;
		
		} else if (isset($_COOKIE["round-".$round_id])) {
			return false;
		
		} else {
			return true;
		}		
	}
	
	public static function setSubmitCookie($round_id) {
		$user_string = getGuid();
		$expire=time()+60*60*24*365; // valid for a year
		setcookie("round-".$_POST['round_id'], $user_string, $expire);
		return $user_string;
	}
}

?>