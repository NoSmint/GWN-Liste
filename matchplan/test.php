<?php
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json; charset=utf-8");
//header("Content-Type: text/html; charset=utf-8");
//header('Content-type: text/xml');
//header('Pragma: public');
//header('Cache-control: private');
//header('Expires: -1');

//$homepage = new DOMDocument();
// '<meta http-equiv="Content-Type" content="charset=utf-8" />' . "\r\n" . 

include('FontLib/Autoloader.php');
include('FontLib/Font.php');

$args = explode('/', $_SERVER['REQUEST_URI']);
print_r($args);

//print_r($args);


// *** Vorgaben u. Variablen-Deklaration ***
$abfragen = array();
$matches = array();
$homepage = file_get_contents('http://www.fussball.de/ajax.club.matchplan.loadmore/-/id/00ES8GN8LS00005DVV0AG08LVUPGND5I/max/1000/show-venues/true/datum-von/2020-09-01/datum-bis/2020-09-30');

	$html = new DOMDocument;
	$html->loadHTML('<meta http-equiv="Content-Type" content="charset=utf-8" />' . '<tr class="row-headline visible-small">' . $homepage);
	$xpath = new DOMXpath($html);		
	$elements = $xpath->query('//tr/td[contains(@class, "column-score")]/a/span[contains(@class, "score-left")]/@data-obfuscation');
	$obfuscation = $elements[1]->nodeValue;

	$fonturl = "Fonts/$obfuscation.woff";
	$testfont = file_get_contents('https://www.fussball.de/export.fontface/-/format/woff/id/' . $obfuscation . '/type/font');
	file_put_contents($fonturl, $testfont);
	$font = \FontLib\Font::load($fonturl);
	$obj = $font->getData("cmap", "subtables");
	$glyphs = $obj[0]["glyphIndexArray"];
	echo $obfuscation . "\r\n";
	foreach ($glyphs as $key => $resvalue) {
		echo $resvalue . "\r\n";
		$result["&#$key;"] = ($resvalue == "11" ? "-" : intval($resvalue) - 1);
	}

$homepage = explode('<tr class="row-headline visible-small">', $homepage);
$counter = 0;


// *** QUERY-Abfragen für DOMXPath ***
	// 3 Elements Überschrift Datum | Klasse | Liga
	$abfragen["heading"] = '//tr[contains(@class, "row-headline")]/td[@colspan="6"]';

	// Klasse | Liga
	$abfragen["class"] = '//tr[contains(@class, "row-competition")]/td[contains(@class, "column-team")]/a';

	// Spielart | Kennung
	$abfragen["matchtype"] = '//tr[contains(@class, "row-competition")]/td[3]/a';

	// Team URL (oder Spielfrei)
	$abfragen["teamurl"] = '//tr/td[contains(@class, "column-club")]/a[contains(@class, "club-wrapper")]/@href | //tr/td[contains(@class, "column-club")]/span[contains(@class, "info-text")]';

	// Team Logo (oder Spielfrei)
	$abfragen["teamlogo"] = '//tr/td[contains(@class, "column-club")]/a[contains(@class, "club-wrapper")]/div[contains(@class, "club-logo ")]/span/@data-responsive-image | //tr/td[contains(@class, "column-club")]/span[contains(@class, "info-text")]';

	// Mannschaften (Home / Guest)
	$abfragen["teamname"] = '//tr/td[contains(@class, "column-club")]/a/div[contains(@class, "club-name")] | //tr/td[contains(@class, "column-club")]/span[contains(@class, "info-text")]';

	// Match Link (oder Spielfrei)
	$abfragen["matchlink"] = '//tr/td[contains(@class, "column-score")]/a/@href | //tr/td[contains(@class, "column-club")]/span[contains(@class, "info-text")]';

	// Spielort (Achtung, falls vorhanden)
	$abfragen["venue"] = '//tr[contains(@class, "row-venue")]/td[@colspan="3"]';

	// sccore-left
	$abfragen["score-left"] = '//tr/td[contains(@class, "column-score")]/a/span[contains(@class, "score-left")]';

	// sccore-right
	$abfragen["score-right"] = '//tr/td[contains(@class, "column-score")]/a/span[contains(@class, "score-right")]';

// *** Auswertung der Querys ***

foreach ($homepage as $page) {
	
	// Der erste Eintrag im Array $homepage ist leer, überspringen
	if ($counter > 0) {
			
		$html = new DOMDocument;
		$html->loadHTML('<meta http-equiv="Content-Type" content="charset=utf-8" />' . '<tr class="row-headline visible-small">' . $page);

		$xpath = new DOMXpath($html);		

		foreach ($abfragen as $abfragekey => $abfrage) {

			$elements = $xpath->query($abfrage);
			
			switch($abfragekey) {
				
				case "teamname":
					$matches[$counter]["teamname_home"] = trim($elements[0]->childNodes[0]->nodeValue);
					$matches[$counter]["teamname_guest"] = trim($elements[1]->childNodes[0]->nodeValue);
					break;

				case "teamurl":
					$matches[$counter]["teamurl_home"] = trim($elements[0]->childNodes[0]->nodeValue);
					$matches[$counter]["teamurl_guest"] = trim($elements[1]->childNodes[0]->nodeValue);
					break;

				case "teamlogo":
					$matches[$counter]["teamlogo_home"] = trim(str_replace(array('//www.','format/3'), array('http://www.','format/9'), $elements[0]->childNodes[0]->nodeValue));
					$matches[$counter]["teamlogo_guest"] = trim(str_replace(array('//www.','format/3'), array('http://www.','format/9'), $elements[1]->childNodes[0]->nodeValue));
					break;

				case "score-left":
					$matches[$counter]["score_home"] = mb_convert_encoding(trim($elements[0]->childNodes[0]->nodeValue), "HTML-ENTITIES", "UTF-8");
					$matches[$counter]["score_home"] = ($matches[$counter]["score_home"] == "") ? "o.E." : str_replace(array_keys($result), $result, $matches[$counter]["score_home"]);
					break;

				case "score-right":
					$matches[$counter]["score_guest"] = mb_convert_encoding(trim($elements[0]->childNodes[0]->nodeValue), "HTML-ENTITIES", "UTF-8");
					$matches[$counter]["score_guest"] = ($matches[$counter]["score_guest"] == "") ? "o.E." : str_replace(array_keys($result), $result, $matches[$counter]["score_guest"]);
					break;
					
				default: 
					$matches[$counter][$abfragekey] = trim($elements[0]->childNodes[0]->nodeValue);
					break;
					
			}

		}
		
	}
	
	$counter++;
}

echo json_encode($matches);
//unlink($fonturl);

?>