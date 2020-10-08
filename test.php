<?php
//header('Content-type: text/xml');
//header('Pragma: public');
//header('Cache-control: private');
//header('Expires: -1');

$homepage = file_get_contents('http://www.fussball.de/ajax.club.matchplan.loadmore/-/id/00ES8GN8LS00005DVV0AG08LVUPGND5I/max/1000/show-venues/true/');
//$homepage = file_get_contents('http://www.fussball.de/ajax.club.matchplan.loadmore/-/id/00ES8GN8N400008VVV0AG08LVUPGND5I/max/1000/show-venues/true/');
//Weiter Testen

$matches = array();
$logos = array();
$i = 0;

foreach(preg_split('/(<tr\s+class\="row\-headline\s+visible\-small">)/', $homepage) as $line){
	
	echo $line;
	$line = str_replace('<div class="club-logo table-image"><span ','',$line);
	$line = str_replace('"></span></div>','',$line);
	$line = preg_replace('/[\[\<{].*?[\]\>}]/' , '', $line);
	$line = str_replace('	','',$line);
	$line = preg_replace("/[\r\n]+/", "\n", $line);
	$line = preg_replace('/\|&nbsp;/','- ',$line);
	echo $line;
	$broken = explode(PHP_EOL, $line);
	
	if ($broken[6] == ":") {
		$matches[home][$i] = trim($broken[5]);
		$matches[guest][$i] = trim($broken[7]);
		$matches[spielart][$i] = trim(explode('|', $broken[3])[0]);
		$matches[kennung][$i] = trim(explode('|', $broken[3])[1]);
		$matches[spielort][$i] = '';
	}
	else {
		$matches[home][$i] = trim($broken[6]);
		$matches[guest][$i] = trim($broken[9]);
		$matches[spielart][$i] = trim(explode('|', $broken[4])[0]);
		$matches[kennung][$i] = trim(explode('|', $broken[4])[1]);
		if ($broken[10] == "Zum Spiel") {
			$matches[spielort][$i] = trim($broken[11]);
		} 
		else {
			$matches[spielort][$i] = trim($broken[12]);
		}
		$matches[logohome][$i] = "http://" . explode('data-responsive-image="//', $broken[5])[1];
		$matches[logoguest][$i] = "http://" . explode('data-responsive-image="//', $broken[8])[1];
	}

	$matches[datum][$i] = trim(explode('|', $broken[1])[0]);
	$matches[spielklasse][$i] = trim(explode('|', $broken[1])[1]);
	$matches[liga][$i++] = trim(explode('|', $broken[1])[2]);

}

var_dump($matches);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
echo '	<Matches>' . "\r\n";

for ($i = 1; $i < count($matches[home]); $i++) {
	echo "		<Match>" . "\r\n";
	echo "			<counter>$i</counter>" . "\r\n";
	echo "			<kennung>" . $matches[kennung][$i] . "</kennung>" . "\r\n";
	echo "			<datum>" . $matches[datum][$i] . "</datum>" . "\r\n";
	echo "			<spielart>" . $matches[spielart][$i] . "</spielart>" . "\r\n";
	echo "			<spielklasse>" . $matches[spielklasse][$i] . "</spielklasse>" . "\r\n";
	echo "			<liga>" . $matches[liga][$i] . "</liga>" . "\r\n";
	echo "			<home>" . $matches[home][$i] . "</home>" . "\r\n";
	echo "			<logohome>" . $matches[logohome][$i] . "</logohome>" . "\r\n";
	echo "			<guest>" . $matches[guest][$i] . "</guest>" . "\r\n";
	echo "			<logoguest>" . $matches[logoguest][$i] . "</logoguest>" . "\r\n";
	echo "			<spielort>" . $matches[spielort][$i] . "</spielort>" . "\r\n";
	echo "		</Match>" . "\r\n";
}

echo '	</Matches>' . "\r\n";

?>