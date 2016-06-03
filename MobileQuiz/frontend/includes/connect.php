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
	This file creates a new MySQL connection using the PDO class.
	The login details are taken from config.php.
*/

try {
    $db = new PDO(
	"mysql:host=".FRONTEND_DB_HOST.";dbname=".FRONTEND_DB_NAME.";charset=utf8",
	FRONTEND_DB_USER,
	FRONTEND_DB_PASS
	);
	
    $db->query("SET NAMES 'utf8'");
    
    // one of 3 PDO error modes: throw exception instead of being silent or warning
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    error_log("MobileQuiz frontend could not connect to database:");
    error_log($e->getMessage());
    
    die("MobileQuiz encountered a database error:".$e->getMessage());
}

?>