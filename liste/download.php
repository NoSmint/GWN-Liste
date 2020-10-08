<?php
define('_ROOT_', $_SERVER['DOCUMENT_ROOT']."/GWN-Liste");
require_once(_ROOT_.'/include/encrypt.inc');
require_once(_ROOT_.'/include/addfunctions.inc');
require_once(_ROOT_.'/include/import.inc');

function read_and_delete_first_line($filename) {
  $file = file($filename);
  $output = $file[0];
  unset($file[0]);
  file_put_contents($filename, $file);
  return;
}


$pass = 'gwn1909';
$salt = md5($pass);
$secret_last = hash("sha256", $salt . $pass . (date('YmdHi') - 1));
$secret_cur = hash("sha256", $salt . $pass . date('YmdHi'));
$secret_next = hash("sha256", $salt . $pass . (date('YmdHi') + 1));

	unset($content);
	
	if (isset($_GET["token"]) && $_GET["token"] == $secret_last || $_GET["token"] == $secret_cur || $_GET["token"] == $secret_next) {
		$content = date("d.m.Y - H:i:s") . " [{$_SERVER['REMOTE_ADDR']}] - {$_SERVER['HTTP_USER_AGENT']}\r\n";
	} else {
		$content = date("d.m.Y - H:i:s") . " [{$_SERVER['REMOTE_ADDR']}] - INVALID TOKEN - {$_SERVER['HTTP_USER_AGENT']}\r\n";
	}
	
	
	$file = fopen("history.log", "a");
	if (filesize("history.log") > 65536) read_and_delete_first_line("history.log");
		
	fwrite($file, $content);
	fclose($file);


if (isset($_GET["token"]) && $_GET["token"] == $secret_last || $_GET["token"] == $secret_cur || $_GET["token"] == $secret_next) {

	$outputfile = "download.vcf";
	header("Content-type: text/vcard; charset=utf-8");
	header("Content-Disposition: attachment; filename=" . $outputfile);
	header("Content-Transfer-Encoding: base64");
	header("Pragma: no-cache");
	header("Expires: 0");		


	// Beginn der Konvertierung. Prüfung auf Passworteingabe muss noch zugefügt werden,
	// dient aber nur als zusätzliche Absicherung. Das Passwort wird bereits beim Aufruf
	// der Liste mehrfach überprüft und bei ausgelaufener Session nach 15 Minuten
	// Inaktivität oder Schließen des Browsers erneut abgefragt.

	$filename = trim($_GET["content"]);
	
	
	// Ausschluss verbotener Zeichen. Der Dateiname soll von möglichen Steuerzeichen
	// gereinigt werden, so dass defintiv keine serverseitigen Befehle ausgeführt
	// werden können. Dieser Schritt wird schon bei Anlage der Datei ausgeführt, hier
	// aber wiederholt, weil GET- und POST-Anfragen manipulierbar sind.
	
	$forbidden = array("\\", '"', '/', '$', '_', '?', '!', '#', ",");
	str_replace($forbidden, "", trim($filename));

	
	// Es ist nicht notwendig das Array $files auf mehr als einen Datensatz abzuprüfen. Bei
	// Anlage der Adresse werden bereits vorhandene Dateinamen mit Namensdoppelung erkannt
	// und Aktualisierung statt Neuanlage vorgenommen. Entsprechend kann es zu keinen
	// Doppelungen kommen und der Inhalt des Arrays beträgt immer [0]. Foreach wird nur aus
	// Gründen der Konsistenz verwendet, die Datenabfrage wird zukünfig ggf. in eine Funktion
	// ausgelagert.
	
	$files = glob("../vCards/*.vcf");
	foreach ($files as $filename) {
	
		unset($inhalt_gesamt);
		unset($inhalt_decrypted);
		unset($ausgabe);

		$inhalt_gesamt = import_file_content($filename);
		$inhalt_decrypted = decrypt($inhalt_gesamt, "gwn1909");		// Decryption Passwort
		
		$inhalt_exploded = explode("\r\n", $inhalt_decrypted);
		
		foreach ($inhalt_exploded as $inhalt) {
			if ($inhalt != "") {
			$konvert = explode(":", $inhalt);
			$ausgabe[trim(utf8_decode($konvert[0]))] = trim(utf8_decode($konvert[1]));
			}
		}

		// Konvertierung ist zur Ausgabe als vCard nur in manchen fällen nötig.
		// Um Konsistenz zu wahren und die Ausgabe möglicherweise später in eine
		// Funktion auszulagern, werden alle Variablen konvertiert.

		$position = $ausgabe["TITLE;CHARSET=UTF-8"];			
		$adresse = explode(";",$ausgabe["ADR;TYPE=HOME,POSTAL;CHARSET=UTF-8"]);
		$email = trim(strtolower($ausgabe["EMAIL;TYPE=PREF,INTERNET"]));
		$mobile = $ausgabe["TEL;TYPE=CELL,VOICE"];
		$phone = $ausgabe["TEL;TYPE=HOME,VOICE"];	
		$namen = explode(";",$ausgabe["N;CHARSET=UTF-8"]);
		$team = explode(";",$ausgabe["ORG;CHARSET=UTF-8"]);
				
		// Viele Handys unterstützen keine zwei Parameter für die Organisation.
		// Anders als in der vCard für den Export in Mailprogramme wird die
		// Mannschaft also hier nicht seperat angegeben, sondern als Teil
		// der Position, mit Bullet-Dot getrennt. Das ist optisch die sauberste
		// Lösung.
		
		$vCard = "BEGIN:VCARD\r\n";
		$vCard .= "VERSION:2.1\r\n";																					// 2.1 ist weiter verbreitet, daher nicht 3.0
		$vCard .= "N;CHARSET=UTF-8:" . $ausgabe["N;CHARSET=UTF-8"] . "\r\n";											// Name
		$vCard .= "FN;CHARSET=UTF-8:" . $namen[1] . " " . $namen[0] . "\r\n";											// Anzeigename <Vorname Nachname> für Export
		$vCard .= "ORG;CHARSET=UTF-8:" . $team[0] . "\r\n";																// Organisation: Grün-Weiß Nottuln
		$vCard .= "ADR;HOME;CHARSET=UTF-8:" . $ausgabe["ADR;TYPE=HOME,POSTAL;CHARSET=UTF-8"] . "\r\n";					// Postanschrift
		$vCard .= "TEL;HOME:" . $ausgabe["TEL;TYPE=HOME,VOICE"] . "\r\n";												// Festnetz
		$vCard .= "TEL;CELL;PREF:" . $ausgabe["TEL;TYPE=CELL,VOICE"] . "\r\n"; 											// Mobil ist immer Standardnummer
		$vCard .= "EMAIL;HOME;PREF:" . $ausgabe["EMAIL;TYPE=PREF,INTERNET"] . "\r\n";									// E-Mail Home ist immer Standardadresse
		$vCard .= "TITLE;CHARSET=UTF-8:" . $team[1] . utf8_decode(" · ") . $ausgabe["TITLE;CHARSET=UTF-8"] . "\r\n";	// <Mannschaft · Position>
		$vCard .= "END:VCARD\r\n\r\n";

	
		// Muster: <Mannschaft_Nachname_Vorname.vcf> also z.B. F2_Mustermann_Max.vcf
		// PHP gibt eine Header-Warnung aus, generiert die Datei aber trotzdem problemlos
		
		echo utf8_encode($vCard);
		
	}			
	die();
	
} else {
	echo htmlentities("<INVALID ACCESS TOKEN>");
	die();
}

?>