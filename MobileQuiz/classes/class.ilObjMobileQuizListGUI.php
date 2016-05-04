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

include_once "./Services/Repository/classes/class.ilObjectPluginListGUI.php";

/**
 * ListGUI implementation for MobileQuiz object plugin. This one
 * handles the presentation in container items (categories, courses, ...)
 * together with the corresponding ...Access class.
 *
 * PLEASE do not create instances of larger classes here. Use the
 * ...Access class to get DB data and keep it small.
 *
 * @author Stephan Schulz <stschulz@mail.uni-mannheim.de>
 */
class ilObjMobileQuizListGUI extends ilObjectPluginListGUI{

	/**
	 * Init type
	 */
	function initType(){
		$this->setType("xuiz");
	}

	/**
	 * Get name of gui class handling the commands
	 */
	function getGuiClass(){
		return "ilObjMobileQuizGUI";
	}

	/**
	 * Get commands
	 */
	function initCommands(){
		return array(
		array(
				"permission" => "read",
				"cmd" => "showCurrentRound",
				"default" => true),
		array(
				"permission" => "write",
				"cmd" => "editProperties",
				"txt" => $this->txt("edit"),
				"default" => false),
		);
	}

	/**
	 * Get item properties
	 *
	 * @return	array		array of property arrays:
	 *						"alert" (boolean) => display as an alert property (usually in red)
	 *						"property" (string) => property name
	 *						"value" (string) => property value
	 */
	function getProperties(){
		global $lng, $ilUser;

		$props = array();
		$this->plugin->includeClass("class.ilObjMobileQuizAccess.php");

		return $props;
	}
}
?>



