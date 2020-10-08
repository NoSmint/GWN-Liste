<?php
define('_ROOT_', $_SERVER['DOCUMENT_ROOT']."/GWN-Liste");
define('_URL_', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');
define('_BACKLINK_', _URL_ . (!empty($_GET["source"]) ? '?source='.$_GET["source"] : ''));
require_once(_ROOT_.'/include/errors.inc');
require_once(_ROOT_.'/include/encrypt.inc');
require_once(_ROOT_.'/include/config.inc');

?>
<!DOCTYPE HTML>
<html>
<head>
<?php
require_once(_ROOT_.'/include/headmeta.inc');
?>
	<script>
		if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('../sw.js', {
				scope: '../.' // <--- THIS BIT IS REQUIRED
			}).then(function(registration) {
				// Registration was successful
				console.log('ServiceWorker registration successful with scope: ', registration.scope);
			}, function(err) {
				// registration failed :(
				console.log('ServiceWorker registration failed: ', err);
			});
		}

		window.addEventListener('beforeinstallprompt', function(e) {
		  console.log('beforeinstallprompt Event fired');
		  e.preventDefault();
		  return false;
		});	
	</script>	
	
</head>

<?php
	if (isset($_GET["action"]) && $_GET["action"] == "new") {
		
			include("../include/addfunctions.inc");

			$forbidden = array("\\", '"', '/', '$', '_', '?', '!', '#', ",");
			$tempmail = $_POST["email"];
			foreach($_POST as $key => $value) {
			$ausgabe[$key] = str_replace($forbidden, "", trim($value));
			}
			$ausgabe["datum"] = date("YmdHi");
			$ausgabe["first_name"] = ucwords(strtolower($ausgabe["first_name"])," -'");
			$ausgabe["last_name"] = ucwords(strtolower($ausgabe["last_name"])," -'");
			$ausgabe["ort"] = ucwords(strtolower($ausgabe["ort"])," -'");
			$ausgabe["street"] = str_replace("str.", "straße", strtolower($ausgabe["street"]));
			$ausgabe["street"] = str_replace("str ", "straße ", strtolower($ausgabe["street"]));
			$ausgabe["street"] = str_replace("str-", "straße-", strtolower($ausgabe["street"]));
			$ausgabe["street"] = ucwords(strtolower($ausgabe["street"])," -'");
			$ausgabe["email"] = strtolower($tempmail);

			$verteiler = "";
			if ($ausgabe["verteiler"] == "Y") {
				$verteiler = "Ja";
			}
			else {
				$verteiler = "Nein";
			}
			
			$publish = "";
			if ($ausgabe["publish"] == "All") {
				$publish = "Alle Kontaktinfos";
			}
			elseif ($ausgabe["publish"] == "NameMail") {
				$publish = "Name + E-Mail";
			}
			elseif ($ausgabe["publish"] == "NameTel") {
				$publish = "Name + Telefon";
			}
			elseif ($ausgabe["publish"] == "None") {
				$publish = "Keine Kontaktinfos";
			}
			else {
				$publish = "Nur Name";
			}
			
			unset($team);
			foreach ($config["Teams"] as $klasse => $teams) {					
				if (array_key_exists($ausgabe["mannschaft"], $teams)) {
					$team = ($klasse == "Misc") ? "" : $config["Teams"][$klasse][$ausgabe["mannschaft"]];
					break;
				}
			};
			
			$position = $ausgabe["position"];
			switch ($position) {
				case 1:
					$position = "Trainer/in";
					break;
				case 2:
					$position = "Co-Trainer/in";
					break;
				case 3:
					$position = "Betreuer/in";
					break;
				case 4:
					$position = "Jugendtrainer/in";
					break;
				case 5:
					$position = $ausgabe["other"];
					break;
			}
			
	$content = "BEGIN:VCARD\r\n";
	$content .= "VERSION:3.0\r\n";
	$content .= "N;CHARSET=UTF-8:". $ausgabe["last_name"] . ";" . $ausgabe["first_name"] . "\r\n";
	$content .= "FN;CHARSET=UTF-8:";
	$content .= ($team) ? $team . " · " . $ausgabe["first_name"] . " " . $ausgabe["last_name"] . "\r\n" : $ausgabe["first_name"] . " " . $ausgabe["last_name"] . "\r\n";
	$content .= "ORG;CHARSET=UTF-8:" . $config["Base"]["Verein"] . ";" . $team . "\r\n";
	$content .= "ADR;TYPE=HOME,POSTAL;CHARSET=UTF-8:;;" . $ausgabe["street"] . ";" . $ausgabe["ort"] . ";;" . $ausgabe["plz"] . ";\r\n";
	$content .= "TEL;TYPE=HOME,VOICE:" . $ausgabe["phone"] . "\r\n";
	$content .= "TEL;TYPE=CELL,VOICE:" . $ausgabe["mobil"] . "\r\n";
	$content .= "EMAIL;TYPE=PREF,INTERNET:" . $ausgabe["email"] . "\r\n";
	$content .= "TITLE;CHARSET=UTF-8:" . $position . "\r\n";
	$content .= "END:VCARD";

	$filetemp = $ausgabe["mannschaft"] . "." . $ausgabe["position"] . "_" . $ausgabe["last_name"] . "_" . $ausgabe["first_name"] . "_" . $ausgabe["datum"] . "-" . $ausgabe["verteiler"] . ".vcf";
	$filetemp = cleanString($filetemp);
	$filetemp = str_replace(" ","_",$filetemp);

	$testforfile = "../vCards/*_" . $ausgabe["last_name"] . "_" . $ausgabe["first_name"] . "_*.vcf";
	$testforfile = cleanString($testforfile);
	$testforfile = str_replace(" ","_",$testforfile);

	$result = glob($testforfile);
	$mailbody = "NEU:<br><br>\r\n";
	$subject = "NEU:";
	if ($result && isset($_POST['update_button'])) {
		$oldname = $result[0];
		$newname = '../vCards/archive/' . substr($result[0], strrpos($result[0],'/') + 1);
		rename($oldname, $newname);
		$mailbody = "UPDATE:<br><br>\r\n";
		$subject = "UPDATE:";
	} elseif ($result) {
		require(_ROOT_.'/include/doublette.inc');
		exit(0);
	}

	// vCard verschlüsseln und schreiben
	
	$content_enc = encrypt($content, "gwn1909");		// Encryption Passwort
	write_vcard($filetemp, $content_enc);
	
	
	// $content für E-Mail vorbereiten, Änderungen vornehmen und abschicken
	
	if ($verteiler == "Ja") {
		$mailbody = "vCard - VERTEILER - " . $mailbody;
	} else {
		$mailbody = "vCard - " . $mailbody;
	}
	if ($verteiler == "Ja") $content = str_replace("END:VCARD", "TEL;TYPE=PAGER:JA\r\nEND:VCARD", $content);
	$content = str_replace("ORG;CHARSET=UTF-8:" . $config["Base"]["Verein"] . ";" . $team . "\r\n", "ORG;CHARSET=UTF-8:" . $config["Base"]["Verein"] . ";" . $ausgabe["mannschaft"] . "\r\n", $content);
	$content = str_replace("END:VCARD", "NICKNAME;CHARSET=UTF-8:" . $ausgabe["mannschaft"] . "\r\nNOTE;CHARSET=UTF-8:Angelegt am: " . date("d.m.Y") . '\nVerteiler: ' . $verteiler . '\nVeröffentlichung: ' . $publish . "\r\nEND:VCARD", $content);
	$mailbody .= utf8_decode(str_replace("\r\n", "<br>\r\n", $content));
	
	// Ich will nicht immer Mails bekommen
	mail_attachment($filetemp, $content, "nils.uhde@web.de", $ausgabe["email"], $ausgabe["first_name"] . " " . $ausgabe["last_name"], $ausgabe["email"], "GWN V-Card: " . $ausgabe["first_name"] . " " . $ausgabe["last_name"], $mailbody);
		
	require(_ROOT_.'/include/success.inc');

} else {

?>
<body>

<div class="bg_img"></div>
<div class="form_wrapper_entry">
	<div class="container_bg"></div>
		<div class="form_container">
		<div class="title_container">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Eingabe zur Adressenliste <?php echo $config["Schedule"]["Saison"];?></h3>
		</div>
		<form action="../add/?action=new" method="post" accept-charset="utf-8">
		
			<div class="row clearfix">
				<label style="margin-top: 0px;">Name</label>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-user"></i></span>
						<input type="text" name="first_name" placeholder="Vorname *" required pattern="^[\p{L}\s'-]{3,99}$" onkeyup="validate('validname','first_name')" autofocus>
						<div id="validname" class="validity"></div>
					</div>
				</div>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-user"></i></span>
						<input type="text" name="last_name" placeholder="Nachname *" required pattern="^[\p{L}\s'-]{3,99}$" onkeyup="validate('validlastname','last_name')">
						<div id="validlastname" class="validity"></div>
					</div>
				</div>
			</div>

			<div class="row clearfix">
				<label>Kontaktdaten</label>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-envelope"></i></span>
						<input type="email" name="email" placeholder="E-Mail *" required onkeyup="validate('validemail','email')">
						<div id="validemail" class="validity"></div>
					</div>
				</div>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-road"></i></span>
						<input type="text" name="street" placeholder="Stra&szlig;e u. Hausnummer" pattern="^[\p{L}\s'-\.&]{3,99}\s+[0-9]+[A-Za-z]?$" onkeyup="validate('validstreet','street')">
						<div id="validstreet" class="validity"></div>
					</div>
				</div>
			</div>
			
			<div class="row clearfix">
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-address-card"></i></span>
						<input type="tel" name="plz" placeholder="PLZ" maxlength="5" pattern="[0-9]{5}" onkeyup="validate('validplz','plz')">
						<div id="validplz" class="validity"></div>
					</div>
				</div>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-address-card"></i></span>
						<input type="text" name="ort" placeholder="Ort" pattern="^[\p{L}\s'-]{3,99}$" onkeyup="validate('validort','ort')">
						<div id="validort" class="validity"></div>
					</div>
				</div>
			</div>
			
			<div class="row clearfix">
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-mobile-alt"></i></span>
						<input type="tel" name="mobil" placeholder="Mobil" maxlength="20" pattern="^0[0-9\s]{9,20}$" onkeyup="validate('validmobile','mobil')">
						<div id="validmobile" class="validity"></div>
					</div>
				</div>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-phone"></i></span>
						<input type="tel" name="phone" placeholder="Festnetz" maxlength="20" pattern="^0[0-9\s]{6,20}$" onkeyup="validate('validphone','phone')">
						<div id="validphone" class="validity"></div>
					</div>
				</div>
			</div>
			
			<div class="row clearfix">
			<label>Position im Verein</label>
				<div class="col_half">
					<div class="custom-select" id="select-pos">
						<div class="input_field"> <span><i aria-hidden="true" class="fas fa-users"></i></span>
							<select id="position" name="position" onChange="findselected()">
								<option value="">Position &middot; Bitte w&auml;hlen</option>
								<optgroup label="Position">
									<option value="1">Trainer/in</option>
									<option value="2">Co-Trainer/in</option>
									<option value="3">Betreuer/in</option>
									<option value="4">Jugendtrainer/in</option>
									<option value="5">Andere</option>
								</optgroup>
							</select>
						</div>
					</div>
				</div>
				<div class="col_half">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-user"></i></span>
						<input type="text" name="other" id="inputother" placeholder="Andere Position" disabled pattern="^[\p{L}\s'-]{3,99}$" onkeyup="validate('validother','other')">
						<div id="validother" class="validity"></div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="custom-select" id="select-team">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-futbol"></i></span>
						<select id="team" name="mannschaft">
							<option value="">Mannschaft &middot; Bitte w&auml;hlen</option>
<?php
								foreach ($config["Teams"] as $klasse => $teams) {
										foreach ($teams as $teamname => $teamdesc) {
											$tab = (chr(9));
											echo '								<option value="' . $teamname . '">';
											echo ($klasse == "Misc" ? "				" . $teamdesc : $klasse . " · " . $teamdesc);
											echo '</option>'."\r\n";
										}
								};
?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="clearfix" style="margin-top: 0px;">
				<label class="questions">Welche Daten d&uuml;rfen auf der <wbr>Vereinshomepage ver&ouml;ffentlich werden?<br>
				<span style="font-weight: normal;">Pro Mannschaft mind. ein Kontakt mit Telefon / E-Mail</span></label>
				<div class="custom-select" id="select-publish" style="text-align: center;">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-upload"></i></span>
						<select id="publish" name="publish" style="text-align-last:center;">
							<option value="">Bitte w&auml;hlen</option>
							<option value="All">Alle Kontaktinfos</option>
							<option value="NameOnly">Nur Name</option>
							<option value="NameTel">Name + Telefon</option>
							<option value="NameMail">Name + E-Mail</option>
							<option value="None">Keine Kontaktinfos</option>
						</select>
					</div>
				</div>
			</div>	

			<div class="clearfix" style="margin-top: 0px;">
				<label class="questions">M&ouml;chtest Du Turniereinladungen <wbr>&uuml;ber den E-Mail-Verteiler erhalten?</label>
				<div class="custom-select" id="select-verteiler" style="text-align: center;">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-at"></i></span>
						<select id="verteiler" name="verteiler" style="text-align-last:center;">
							<option value="">Bitte wählen</option>
							<option value="Y">Ja</option>
							<option value="N">Nein</option>
						</select>
					</div>
				</div>
			</div>	
			
			<input class="button" type="submit" id="absenden" value="Jetzt Eintragen">
			
		</form>
		<div class="footer_container">
			Name und E-Mail-Adresse sind Pflichtfelder.<br><br>
			<strong>Eure Daten sind sicher:</strong><br>Die Liste ist nicht &ouml;ffentlich einsehbar.<br>Eure Daten werden verschl&uuml;sselt gespeichert.<br>Eure Postanschrift wird grunds&auml;tzlich nicht ver&ouml;ffentlicht
		</div>
		<div class="footer_container" style="margin-bottom: 12px;">
			<a class="backlinks" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
		</div>
	</div>
</div>

<script src="<?php echo _URL_;?>js/add.js"></script>

</body>
</html>

<?php
}
?>