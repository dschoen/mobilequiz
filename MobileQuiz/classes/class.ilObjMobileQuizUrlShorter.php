<?php
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/configuration.local.php");

class ilObjMobileQuizUrlShorter {
	public function shortURL($url) {
		
		// Init the CURL session
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, SHORTENER_URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
		curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
				'url'      => $url,
				'format'   => SHORTENER_FORMAT,
				'action'   => 'shorturl',
				'username' => SHORTENER_USERNAME,
				'password' => SHORTENER_PASSWORD
		));
		
		// Fetch and return content
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
}

?>