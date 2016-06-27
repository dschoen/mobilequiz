<!DOCTYPE html> 
<html> 
	<head> 
	<title><?php echo formatTitle($title)?></title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1" /> 
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="assets/jquery.mobile/jquery.mobile.custom.min.css" />
        <link rel="stylesheet" href="assets/jquery.mobile/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="assets/jquery.mobile/jquery.mobile.structure.min.css" />
	<link rel="stylesheet" href="assets/css/styles.css" />
	<script src="assets/scripts/jquery.min.js"></script>
	<script src="assets/jquery.mobile/jquery.mobile.min.js"></script>
        
    <!-- Enable LaTeX Support -------------------------------------------------- -->
    <script type="text/javascript">
                
        if ( <?php echo LATEX_TRANSFORMATION ?> ) {
            (function () {
              var head = document.getElementsByTagName("head")[0], script;
              script = document.createElement("script");
              script.type = "text/x-mathjax-config";
              script[(window.opera ? "innerHTML" : "text")] =
                "MathJax.Hub.Config({\n"
                + "tex2jax: { inlineMath:  [['$$$','$$$']],\n"
                + "           displayMath: [['$$','$$']],\n"
                + "     }\n"
                + "});";
              head.appendChild(script);
              script = document.createElement("script");
              script.type = "text/javascript";
              script.src  = "http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML";
              head.appendChild(script);
            })();
        }
    </script>
</head> 

<body> 

<div data-role="page">

	<div data-role="header" data-theme="a">
		<h1><?php echo $title ?></h1>
	</div>

	<div data-role="content">