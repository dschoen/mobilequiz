<?php
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/classes/class.ilMobileQuizConfigDAO.php");

class ilObjMobileQuizUrlShorter {
	public function shortURL($url) {
		
		$config = new ilMobileQuizConfigDAO();
		
		// Init the CURL session
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $config->getConfigItem("SHORTENER_URL"));
		curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
		curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
				'url'      => $url,
				'format'   => $config->getConfigItem("SHORTENER_FORMAT"),
				'action'   => 'shorturl',
				'username' => $config->getConfigItem("SHORTENER_USERNAME"),
				'password' => $config->getConfigItem("SHORTENER_PASSWORD")
		));
		
		// Fetch and return content
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
}

?>