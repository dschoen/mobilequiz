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

/*************************************
 *  Configuration for URL Shorter    *
 *************************************/

// Activate URL-Shortener
define("SHORTENER", true);

// Show URL, shortened or not
define("SHORTENER_SHOW_URL", true);

// Authentication Parameters
define("SHORTENER_USERNAME", "xxx");
define("SHORTENER_PASSWORD", "xxx");

// the query parameters
// output format: 'json', 'xml' or 'simple'
define("SHORTENER_FORMAT", "simple"); 
// the URL of the API file
define("SHORTENER_URL", "http://tiny.uni-mannheim.de/yourls-api.php");


// +--------------------------------------------------------------------------+
// |   Configuration of the Students Frontend part                            |
// +--------------------------------------------------------------------------+

// Developement Configuration

define("FRONTEND_DB_HOST", 'localhost');
define("FRONTEND_DB_USER", 'root');
define("FRONTEND_DB_PASS", 'root');
define("FRONTEND_DB_NAME", 'ilias');

?>