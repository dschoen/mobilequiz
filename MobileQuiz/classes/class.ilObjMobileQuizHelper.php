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
                
        include_once './Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/lib/markdown/Markdown.inc.php';
        
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
}

?>