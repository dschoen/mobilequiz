<?php

/*************************************
 *  Configuration for URL Shorter    *
 *************************************/

// Activate URL-Shortener
define("SHORTENER", true);

// Show URL, shortened or not
define("SHORTENER_SHOW_URL", true);

// Authentication Parameters
define("SHORTENER_USERNAME", "wordpressplugin");
define("SHORTENER_PASSWORD", "1gnqxzh2");

// the query parameters
// output format: 'json', 'xml' or 'simple'
define("SHORTENER_FORMAT", "simple"); 
// the URL of the API file
define("SHORTENER_URL", "http://tiny.uni-mannheim.de/yourls-api.php");


/****************************************************************
 *  Quiz Constants - DO NOT TOUCH - unless you know what you do  *
 ****************************************************************/
define("QUESTION_TYPE_MULTI", "1");     // Multi Choice
define("QUESTION_TYPE_SINGLE", "2");    // Single Choice
define("QUESTION_TYPE_NUM", "3");       // Numeric
define("CHOICE_TYPE_INCORRECT", "0");   // Wrong Choice
define("CHOICE_TYPE_CORRECT", "1");     // Correct Choice
define("CHOICE_TYPE_NEUTRAL", "2");     // Neutral Choice
define("NUMERIC_MAX_NUMBER_OF_BUCKETS", "20"); // max number of Buckets displayed in Numeric Questions

define("ALLOWED_TAGS", "<br><br/><b><i><img><iframe>"); // Valid Tags for the question layout, which can be used by people.
?>
