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
    
    /**
     * Gets a String and transforms it to a web ready version.
     * 
     * @author dschoen
     * 
     * @param String $text
     */
    public function polishText($text) {
        $text = str_replace(array("\r","\n"), "<br />", $text);
        
        return $text;                
    }
}

?>