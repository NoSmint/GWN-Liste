<?php
function encrypt($plaintext, $keyvalue) {

	$key = hash("sha256", $keyvalue);
	$cipher = "aes-256-gcm";	

	$ivlen = openssl_cipher_iv_length($cipher);
	$iv = openssl_random_pseudo_bytes($ivlen);

	$ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
	$ciphertext = $tag . $iv . $ciphertext;
	
	$ciphertext_base64 = base64_encode($ciphertext);
	
	return $ciphertext_base64;
	
}

function decrypt($ciphertext_base64, $keyvalue) {

	$key = hash("sha256", $keyvalue);
	$cipher = "aes-256-gcm";	

	$ciphertext_dec = base64_decode($ciphertext_base64);
	
	$ivlen = openssl_cipher_iv_length($cipher);
	$tag = substr($ciphertext_dec, 0, 16);
	$iv = substr($ciphertext_dec, 16, $ivlen);
	$ciphertext = substr($ciphertext_dec, $ivlen + 16);
	
	$plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
	
	return $plaintext;
	
}
?>