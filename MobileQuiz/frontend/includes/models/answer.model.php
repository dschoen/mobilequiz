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
* Application class for Answer object.
*
* @author Stephan Schulz <stschulz@mail.uni-mannheim.de>
*
*/
class Answer{
	
	public $round_id;
	public $choice_id;
	public $value;
	public $user_string;
	
	/**
	* Constructor
	*
	* @access	public
	* @param	int			round_id
	* @param	int			choice_id
	* @param	int			value
	* @param	string		user_string
	* 
	*/
	function __construct($round_id, $choice_id, $value, $user_string) {
		$this->round_id 	= $round_id;
		$this->choice_id 	= $choice_id;
		$this->value 		= $value;
		$this->user_string 	= $user_string;
	}
	
	/**
	* This static method inserts an
	* answer object into the database.
	*
	* @return	bool	success
	* 
	*/
	public static function doCreate($arr){
		global $db;
	
		// look for a specific choice
		if(isset($arr['round_id']) && isset($arr['choice_id'])){
			
			// generate primary key using the sequence table (see getSequence())
			$arr['answer_id'] = ANSWER::getSequence();
			$st = $db->prepare("INSERT INTO rep_robj_xuiz_answers (answer_id, round_id, choice_id, value, user_string) VALUES (:answer_id, :round_id, :choice_id, :value, :user_string)");
		} else{
			throw new Exception("doCreate (answer): Unsupported property!");
		}
	
		return $st->execute($arr);
	}
	
	/**
	* As ILIAS uses pear MDB2 we need to deal with its special sequence table.
	* Whenever we write to the answer table we need to get the next primary key from that sequence table and increase it.
	*
	* @return	int		primary_key
	*/
	public static function getSequence(){
		global $db;
	
		$statement = $db->prepare("SELECT sequence FROM rep_robj_xuiz_answers_seq");
		$statement->execute();
		$result = $statement->fetch();
		
		// if there is no sequence entry yet, initialize the table with 1
		if(empty($result)){
			$st2 = $db->prepare("INSERT INTO rep_robj_xuiz_answers_seq (sequence) VALUES (?)");
			$st2->execute(array(1));
			$id = 0;
		}else{
			$id = $result["sequence"];
		}
		
		// increase sequence by 1
		$st = $db->prepare("UPDATE rep_robj_xuiz_answers_seq SET sequence=? WHERE sequence=?");
		$st->execute(array($id+1,$id));
		
		// return current sequence
		return $id+1;
	}
}

?>