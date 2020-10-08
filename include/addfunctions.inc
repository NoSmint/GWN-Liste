<?php

function import_file_content($filename) {
			
			unset($inhalt);
			$inhalt = "";
			
			if ($file = fopen($filename, "r")) {
				while(!feof($file)) {
					$inhalt .= fgets($file);
				}
				fclose($file);
			}
			
			return $inhalt;
}


function sort_files($order = NULL) {

	global $files;
	$counter = 0;
	foreach ($files as $key => $pattern) {
		$sorter[$key] = str_replace(".vcf","",explode("_", $pattern)[1]);
	}
	if ($order == "zyx" || $order == "desc") {
		array_multisort($sorter,SORT_DESC,$files);
	} else {
		array_multisort($sorter,SORT_ASC,$files);
	}
}


function write_vcard ($filename, $content) {
	
	if ($vcard = fopen("../vCards/".$filename, "w")) {
	
		fwrite($vcard, $content);
		fclose($vcard);
	
	}
	
}

function mail_attachment($filename, $content, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
		$fileatt_type = "text/x-vcard; charset=utf-8";

		$headers = "FROM: ".$from_mail;

		$data = chunk_split(base64_encode($content));

		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";

		$message .= "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 8bit\n\n" .
		$message . "\n\n";

		$message .= "--{$mime_boundary}\n" .
		"Content-Type: {$fileatt_type};\n" .
		" name=\"{$filename}\"\n" .
		"Content-Transfer-Encoding: base64\n\n" .
		$data . "\n\n" .
		"--{$mime_boundary}--\n";

		mail($mailto, $subject, $message, $headers);
}

function cleanString($text) {
	$utf8 = array(
		'/[áàâãªä]/u'   =>   'a',
		'/[ÁÀÂÃÄ]/u'    =>   'A',
		'/[ÍÌÎÏ]/u'     =>   'I',
		'/[íìîï]/u'     =>   'i',
		'/[éèêë]/u'     =>   'e',
		'/[ÉÈÊË]/u'     =>   'E',
		'/[óòôõºö]/u'   =>   'o',
		'/[ÓÒÔÕÖ]/u'    =>   'O',
		'/[úùûü]/u'     =>   'u',
		'/[ÚÙÛÜ]/u'     =>   'U',
		'/ç/'           =>   'c',
		'/Ç/'           =>   'C',
		'/ñ/'           =>   'n',
		'/Ñ/'           =>   'N',
		'/ä/'           =>   'ae',
		'/Ä/'           =>   'Ae',
		'/ö/'           =>   'oe',
		'/Ö/'           =>   'Oe',
		'/ü/'           =>   'ue',
		'/Ü/'           =>   'Ue',
		'/ß/'           =>   'ss',
		'/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
		'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
	);
	return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

?>