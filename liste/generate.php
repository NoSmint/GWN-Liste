<?php

	$pass = 'gwn1909';
	$salt = md5($pass);
	$secret = hash("sha256", $salt . $pass . date('YmdHi'));
	
	echo $secret;
	
?>