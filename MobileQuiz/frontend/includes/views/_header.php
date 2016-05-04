<!DOCTYPE html> 
<html> 
	<head> 
	<title><?php echo formatTitle($title)?></title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1" /> 
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="assets/jquery.mobile/jquery.mobile-1.0.1.min.css" />
	<link rel="stylesheet" href="assets/css/styles.css" />
	<script src="assets/scripts/jquery-1.6.4.min.js"></script>
	<script src="assets/jquery.mobile/jquery.mobile-1.0.1.min.js"></script>
</head> 
<body> 

<div data-role="page">

	<div data-role="header" data-theme="b">
	    <a href="./" data-icon="home" data-iconpos="notext" data-transition="fade">Home</a>
		<h1><?php echo $title?></h1>
	</div>

	<div data-role="content">