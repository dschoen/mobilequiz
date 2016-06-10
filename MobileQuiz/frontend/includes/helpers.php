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

/* These are helper functions */

/**
* This is the main rendering method
* 
* @param	string	template
* @param	array	vars
*/
function render($template,$vars = array()){
	
	// This function takes the name of a template and
	// a list of variables, and renders it.
	
	// This will create variables from the array:
	extract($vars);
	
	// It can also take an array of objects
	// instead of a template name.
	if(is_array($template)){
		
		// If an array was passed, it will loop
		// through it, and include a partial view
		foreach($template as $key=>$k){
			
			// This will create a local variable
			// with the name of the object's class
			
			$cl = strtolower(get_class($k));
			 $$cl = $k;
            if ( !empty( $vars["templateFile"] ) ) $cl = $vars["templateFile"];
			include "views/_$cl.php";
		}
		
	}
	else {
		include "views/$template.php";
	}
}

/**
* This is the helper function for formatting the title
* 
* @return	string	title
*/
function formatTitle($title = ''){
	if($title){
		$title.= ' | ';
	}
	
	$title .= $GLOBALS['defaultTitle'];
	
	return $title;
}

/**
* Helper function to generate a globally unique identifier (GUID)
* 
* @return	string	GUID
*/
function getGuid(){
	if (function_exists('com_create_guid') === true){
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

/**
* Helper function to find out if a round is still active
* 
* @param	int		round_id
* @return	boolean
*/
function isRoundActive($round_id){
	$round = Round::find(array('round_id'=>$round_id));
	if (empty($round[0]->end_date)){
		return true;
	}
	else{
		return false;
	}
}

// -----------------------------------------------------------------------------

function polishText($text) {

    // remove critical charackters
    $text = htmlspecialchars($text);

    // Create html line breaks
    //$text = str_replace(array("\r","\n"), "<br />", $text);
    $text = nl2br($text);

    // Render Markdown
    $text = Markdown::defaultTransform($text);

    // remove all line breaks
    $text = str_replace(array("\r","\n"), "", $text);

    return $text;                
}

?>
