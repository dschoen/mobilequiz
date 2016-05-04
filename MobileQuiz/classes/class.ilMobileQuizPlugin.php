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

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");

/**
* MobileQuiz repository object plugin
*
* @author Daniel Schoen <mobilequiz@uni-mannheim.de>
* @author Stephan Schulz, Maximilian Wich, Dominik Campanella, Neslihan Tasci
* @version $Id$
*
*/
class ilMobileQuizPlugin extends ilRepositoryObjectPlugin{
	function getPluginName(){
		return "MobileQuiz";
	}	
}

?>
