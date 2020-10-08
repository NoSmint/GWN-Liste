<?php
		
	function import_vcard($identifier)
	{
		global $labelshown;
		global $config;
		global $files;
		$label = $identifier;
		$id_load = $identifier;
		
		foreach ($config["Teams"] as $klasse => $teams) {
			foreach ($teams as $teamname => $teamdesc) {
				if ($identifier == $teamname) {
					$label = $klasse;
					break;
				}
			}
		}		
		
		if ($label == "Junioren" || $label == "Juniorinnen") {
			$label = (strtoupper(substr($identifier, 0, 1)) == "W") ? substr($identifier, 1, 1) . "-" . $label : substr($identifier, 0, 1) . "-" . $label;;
		}
		
		if ($label == "Misc") {
			$label = "Allgemeine Funktionen";
		}

		switch ($identifier) {
		
			case "ABC":
				$id_load = "*";
				$label = "Alle Kontakte";
				break;
				
			case "ZYX":
				$id_load = "*";
				$label = "Alle Kontakte";
				break;
				
			case "POS1":
				$id_load = "*.1_";
				$label = "Trainer/innen";
				break;
				
			case "POS2":
				$id_load = "*.2_";
				$label = "Co-Trainer/innen";
				break;
				
			case "POS3":
				$id_load = "*.3_";
				$label = "Betreuer/innen";
				break;
				
			case "POS4":
				$id_load = "*.4_";
				$label = "Jugendtrainer/innen";
				break;
				
			case "POS5":
				$id_load = "*.5_";
				$label = "Allgemeine Funktionen";
				break;

		}
		
		$files = glob("../vCards/" . $id_load . "*.vcf");
		
		if ($identifier == "ABC") {
			sort_files("abc");
		}

		if ($identifier == "ZYX") {
			sort_files("zyx");
		}

		foreach ($files as $filename) {
			
			$id_ident = preg_replace('/[0-9]+/', '', $identifier);
			if (substr($identifier, 0, 3) == "POS" || substr($identifier, 0, 3) == "ABC" || substr($identifier, 0, 3) == "ZYX") {
				$id_ident = $identifier;
			}
			if ($labelshown[$label] != 1) {
				echo '		<label class="label" ID="ID_' . $id_ident . '">' . $label . '</label>' . "\r\n";
				$labelshown[$label] = 1;
			}

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
				
			if (isset(explode("_", $filename)[0]) && isset(explode("_", $filename)[1])) $download = explode("_", $filename)[0] . "_" . explode("_", $filename)[1];
			if (isset(explode("_", $filename)[2])) $download .= "_" . explode("_", $filename)[2];
			$download =  str_replace(".vcf","",$download);
			
			$position = $ausgabe["TITLE;CHARSET=UTF-8"];			
			$team = explode(";",$ausgabe["ORG;CHARSET=UTF-8"]);
			$namen = explode(";",$ausgabe["N;CHARSET=UTF-8"]);
			if (isset($ausgabe["ADR;TYPE=HOME,POSTAL;CHARSET=UTF-8"])) $adresse = explode(";",$ausgabe["ADR;TYPE=HOME,POSTAL;CHARSET=UTF-8"]);
			if (isset($ausgabe["EMAIL;TYPE=PREF,INTERNET"])) $email = trim(strtolower($ausgabe["EMAIL;TYPE=PREF,INTERNET"]));
			if (isset($ausgabe["TEL;TYPE=CELL,VOICE"])) $mobile = $ausgabe["TEL;TYPE=CELL,VOICE"];
			if (isset($ausgabe["TEL;TYPE=HOME,VOICE"])) $phone = $ausgabe["TEL;TYPE=HOME,VOICE"];
			
			if (isset($mobile[0]) && $mobile[0] == "0") {
				$mobile_canonical = "+49" . str_replace(" ", "", substr($mobile, 1));
			} else {
				$mobile_canonical = "";
				if (isset($mobile)) $mobile_canonical = $mobile;
			}
			
			if (isset($phone[0]) && $phone[0] == "0") {
				$phone_canonical = "+49" . str_replace(" ", "", substr($phone, 1));
			} else {
				$phone_canonical = "";
				if (isset($phone)) $phone_canonical = $phone;
			}

			if (!isset($adresse[2]) || $adresse[2] == "" && !isset($adresse[3]) || $adresse[3] == "" && !isset($adresse[5]) || $adresse[5] == "" ) {
				$anschrift = "";
			} else {
				$anschrift = trim(htmlentities($adresse[2])) . "<br>" . trim(htmlentities($adresse[5])) . " " . trim(htmlentities($adresse[3]));
			}
			
			if ($identifier == "ABC" || $identifier == "ZYX") echo '		<button class="accordion abc ID_' . $id_ident . '">' . htmlentities(trim($namen[0])) . ", " . htmlentities(trim($namen[1])) . '<span class="subtitle">' . $position;
			else echo '		<button class="accordion ID_' . $id_ident .'">' . htmlentities(trim($namen[1])) . " " . htmlentities(trim($namen[0])) . '<span class="subtitle">' . $position;
			if ($team[1] != "") echo ' &bullet; ' . $team[1];
			echo '</span></button>' . "\r\n";
			echo '		<div class="panel">' . "\r\n";
			echo '			<div class="row clearfix">' . "\r\n";
			echo '				<div class="col_half adresse">' . "\r\n";
			if (!isset($anschrift) || $anschrift == "") {
			echo '					<div class="input_field showcase noinfo"> <span><i aria-hidden="true" class="fas fa-road"></i></span>&ndash; nicht angegeben &ndash;</div>' . "\r\n";
			} else {
			echo '					<div class="input_field showcase road"> <span><i aria-hidden="true" class="fas fa-road"></i></span>' . $anschrift . '</div>' . "\r\n";
			}
			echo '				</div>' . "\r\n";
			echo '				<div class="col_half">' . "\r\n";
			echo '					<div class="input_field showcase email"> <span><i aria-hidden="true" class="fas fa-envelope"></i></span><a href="mailto:' . $email . '">'. $email .'</a></div>' . "\r\n";
			echo '				</div>' . "\r\n";
			echo '			</div>' . "\r\n";
			echo '			<div class="row clearfix">' . "\r\n";
			echo '				<div class="col_half">' . "\r\n";
			if (!isset($mobile) || $mobile == "") {
			echo '					<div class="input_field showcase noinfo"> <span><i aria-hidden="true" class="fas fa-mobile-alt"></i></span>&ndash; nicht angegeben &ndash;</div>' . "\r\n";	
			} else {
			echo '					<div class="input_field showcase"> <span><i aria-hidden="true" class="fas fa-mobile-alt"></i></span><a href="tel:' . $mobile_canonical . '">' . $mobile . '</a></div>' . "\r\n";
			}
			echo '				</div>' . "\r\n";
			echo '				<div class="col_half">' . "\r\n";
			if (!isset($phone) || $phone == "") {
			echo '					<div class="input_field showcase noinfo"> <span><i aria-hidden="true" class="fas fa-phone"></i></span>&ndash; nicht angegeben &ndash;</div>' . "\r\n";
			} else {
			echo '					<div class="input_field showcase"> <span><i aria-hidden="true" class="fas fa-phone"></i></span><a href="tel:' . $phone_canonical . '">' . $phone . '</a></div>' . "\r\n";
			}
			echo '				</div>' . "\r\n";
			echo '			</div>' . "\r\n";
			echo '			<div class="clearfix">' . "\r\n";
			echo "				<input onclick=\"javascript:location.replace('export.php?action=download&content=" . $download . "')\" class=\"button\" type=\"submit\" value=\"Kontakt speichern\">" . "\r\n";
			echo '			</div>' . "\r\n";
			echo '			<div style="padding-bottom: 15px;"></div>' . "\r\n";
			echo '		</div>' . "\r\n";
			echo "\r\n";
		}
	}

?>