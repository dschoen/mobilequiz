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
    
    // -------------------------------------------------------------------------
    
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
        $text = nl2br($text);

        // Render Markdown
        $text = Markdown::defaultTransform($text);
        
        // remove all original line breaks
        $text = str_replace(array("\r","\n"), "", $text);
        
        return $text;                
    }
    
    // -------------------------------------------------------------------------
    
    /**
     * Returns the global URL of the plugin.
     * Ends with .../MobileQuiz/
     * 
     * @return string
     */
    public function getPluginUrl(){
        
        $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
        $hostname = (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['SERVER_NAME'];
         
        $path = $_SERVER['REQUEST_URI'];
        $pos_to_cut = strrpos($path, '/');
        $path = substr($path,0,$pos_to_cut+1);
        
        // crafting quiz url:
        $tmp = explode('/',$url);
        $server_url = (implode('/',$tmp) . '/');
        
        // please change this if you move your frontend installation out of your ILIAS plugin directory:
        $plugin_path = "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/";

        return $protocol.$hostname.$path.$plugin_path;
    }
}

?>