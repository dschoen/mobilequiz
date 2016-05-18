<?php
class ilObjMobileQuizHelper {
	
    /**
     * Cut a string at given lenght and adds "..."
     * 
     * @param String $text
     * @param Integer $length
     * @return String $text
     */
    public function cutText($text, $length=100) {
        return (strlen($text) > $length) ? substr($text,0,$length).'...' : $text;
    }
}

?>