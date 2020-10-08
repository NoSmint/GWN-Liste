<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

	$pass = 'gwn1909';
	$salt = md5($pass);
	$secret = hash("sha256", $salt . $pass . date('YmdHi'));
	
	$vCards = file_get_contents ("https://gwn-liste.de/liste/download.php?token=".$secret);
	
	$vCards = utf8_decode($vCards);
	$vCards = htmlentities($vCards);
	$vCards = str_replace("\r\n", "<br>\r\n", $vCards);
	
	echo $vCards;
	
?>