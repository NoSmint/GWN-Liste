<?php
session_start();
define('_ROOT_', $_SERVER['DOCUMENT_ROOT']."/GWN-Liste");
define('_URL_', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');
define('_BACKLINK_', _URL_ . (!empty($_GET["source"]) ? '?source='.$_GET["source"] : ''));
define('_SOURCE_', (!empty($_GET[source]) ? '?source=' . $_GET[source] : ''));
require_once(_ROOT_.'/include/password.inc');
require_once(_ROOT_.'/include/encrypt.inc');
require_once(_ROOT_.'/include/addfunctions.inc');
require_once(_ROOT_.'/include/import.inc');
require_once(_ROOT_.'/include/config.inc');

$abgelaufen = false;

	if (isset($_SESSION['password']) || isset($_POST["password"])) {
		if (isset($_SESSION['password']) && $_SESSION['password'] != _PASSWORD_) {
			unset($_SESSION['password']);
			unset($_SESSION['LAST_ACTIVITY']);
			session_unset();
		}
		if (isset($_SESSION['password']) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
			$abgelaufen = true;
			unset($_SESSION['password']);
			unset($_SESSION['LAST_ACTIVITY']);
			session_unset();
		}
		if (isset($_POST['password']) && md5(strtoupper($_POST['password'])) == _PASSWORD_) {
			$abgelaufen = false;
			$_SESSION['password'] = md5(strtoupper($_POST['password']));
			$_SESSION['LAST_ACTIVITY'] = time();
			header("Location: ./" . _SOURCE_);
			die();
		}
	}

?>
<!DOCTYPE HTML>
<html>
<head>

	<meta charset="utf-8">

	<!-- Favicons für unterschiedliche Systeme (von realfavicongenerator.net) -->
	<link rel="apple-touch-icon" sizes="180x180" href="../favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="../favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../favicons/favicon-16x16.png">
	<link rel="icon" sizes="192x192" href="../favicons/android-chrome-192x192.png">
	<link rel="manifest" href="../manifest.json">
	<link rel="mask-icon" href="../favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="../favicons/favicon.ico">
	<meta name="apple-mobile-web-app-title" content="GWN-Liste">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="application-name" content="GWN-Liste">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-config" content="../favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

	<!-- Andere Metadaten -->
	<meta name="description" content="Adressverzeichnis f&uuml;r Vereinsmitglieder des <?php echo $config["Base"]["VereinLong"];?>">
	<meta name="keywords" content="Adressliste, Adressverzeichnis, <?php echo $config["Base"]["VereinShort"];?>, <?php echo $config["Base"]["Verein"];?>, <?php echo $config["Base"]["VereinLong"];?>, Mitglieder, Trainer, Betreuer">
	<meta name="author" content="Nils Uhde">
	<meta name="copyright" content="Nils Uhde, <?php echo $config["Base"]["VereinLong"];?>">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="cache-control" content="no-cache">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>GWN &middot; Adressenliste</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/solid.css" integrity="sha384-v2Tw72dyUXeU3y4aM2Y0tBJQkGfplr39mxZqlTBDUZAb9BGoC40+rdFCG0m10lXk" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/fontawesome.css" integrity="sha384-q3jl8XQu1OpdLgGFvNRnPdj5VIlCvgsDQTQB6owSOHWlAurxul7f+JpUOVdAiJ5P" crossorigin="anonymous">
	<link href="../css/gwn.css" rel="stylesheet" type="text/css">

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

	if (isset($_SESSION['password']) && $_SESSION['password'] == _PASSWORD_) {
		$_SESSION['LAST_ACTIVITY'] = time();
?>
<body class="Hover">

	<div class="mobile_navigation">
	<div class="bg"></div>
		<div class="gwn"><a id="gwn" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><img src="../images/gwn.svg" width="246px" height="197px"></a></div>
		<div class="mobile_element"><a href="#" onclick="javascript:return false;"><i class="fas fa-bars"></i></a></div>
		<div class="mobile_element"><a class="sortbutton" id="sortbutton" href="#" onclick="javascript:ToggleSort(); return false;"><i class="fas fa-sort-amount-down"></i></a></div>		
		<div class="sortdropdown" id="sortdropdown">
			<a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=team" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=team'); return false;"><i class="fas fa-futbol"></i> Nach Mannschaft</a>
			<a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=pos" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=pos'); return false;"><i class="fas fa-users"></i> Nach Position</a>
			<a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=abc" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=abc'); return false;"><i class="fas fa-sort-alpha-down"></i> Alphabetisch<br><span>(aufsteigend)</span></a>
			<a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=zyx" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=zyx'); return false;"><i class="fas fa-sort-alpha-up"></i> Alphabetisch<br><span>(absteigend)</span></a>
		</div>
		<div class="mobile_element"><a href="#" onclick="javascript:ToggleSearch(); return false;"><i class="fas fa-search"></i></a></div>
	</div>

	
<div id="myBtn"><a href="#" onclick="topFunction(); return false;"><i aria-hidden="true" class="fas fa-chevron-circle-up"></i></a></div>

<div class="bg_img"></div>
<div class="form_wrapper_list">
	<div class="container_bg"></div>
	<div class="form_container form_container_list">
		<div class="title_container liste">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Adressenliste</h3>
		</div>
		<div class="headerlinks"><i class="fas fa-sort"></i> <a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=team" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=team'); return false;">Nach Mannschaft</a> &bullet; <a href="<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=pos" onClick="javascript:location.replace('<?php echo (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?');?>sort=pos');  return false;">Nach Funktion</a> &bullet; 			
<?php

	if (isset($_GET["sort"]) && $_GET["sort"] == "abc") { 
		echo "<a href=\"" . (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?') . "sort=zyx\" onClick=\"javascript:location.replace('" . (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?') . "sort=zyx'); return false;\">Z-A</a></div>"."\r\n";
	} else {
		echo "<a href=\"" . (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?') . "sort=abc\" onClick=\"javascript:location.replace('" . (!empty(_SOURCE_) ? _SOURCE_ . "&" : '?') . "sort=abc'); return false;\">A-Z</a></div>"."\r\n";
	}
?>		<div class="search"><i class="fas fa-search"></i> <a href="#" onclick="javascript:ToggleSearch(); return false;">Suche</a>&nbsp;<span class="searchdesktop">&nbsp;(Strg + F)</span></div>
		
		<div id="suchfeld" class="suchfeld">
			<div class="clearfix">
				<div class="input_field"> <span><i aria-hidden="true" class="fas fa-search"></i></span>
					<input type="text" id="search" name="search" placeholder="Suchen" onkeyup="javascript:DoSearch()">
				<div class="searchclear"><a href="#" onclick="javascript:ToggleSearch(); return false;">✖</a></div>
				</div>	
			</div>
		</div>
		<div class="clearfix">
			<div class="noData">Keine Daten zum Anzeigen<br>Bitte Sucheingabe &uuml;berpr&uuml;fen</div>
		</div>
<?php

	if (isset($_GET["sort"]) && $_GET["sort"] == "abc") {

		import_vcard("ABC");

	} elseif (isset($_GET["sort"]) && $_GET["sort"] == "zyx") {

		import_vcard("ZYX");

	} elseif (isset($_GET["sort"]) && $_GET["sort"] == "pos") {

		import_vcard("POS5");
		import_vcard("POS1");
		import_vcard("POS2");
		import_vcard("POS3");
		import_vcard("POS4");

	} else {

		foreach ($config["Teams"] as $klasse => $teams) {					
			foreach ($teams as $teamname => $teamdesc) {
				import_vcard($teamname);
			}
		}
	}
?>

		<div class="footer_container liste">
			<a class="backlinks" id="backtohome" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
		</div>
	</div>
</div>

<script src="../js/liste.js"></script>

</body>
<?php
	
	} else {
		
?>
<body>

<div class="bg_img"></div>
<div class="form_wrapper_entry">
	<div class="container_bg"></div>
		<div class="form_container">
		<div class="title_container">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Adressenliste: Passwortabfrage</h3>
		</div>
		<form action="./<?php echo _SOURCE_;?>" method="post">
		<div class="row clearfix">
<?php
			if (isset($_POST['password']) && md5(strtoupper($_POST['password'])) != _PASSWORD_) {
				echo '			<label style="margin-bottom: 25px;">Du hast ein falsches Passwort eingegeben<br>Versuche es noch einmal</label>'."\r\n";
			} elseif (isset($abgelaufen) && $abgelaufen == 1) {
				echo '			<label style="margin-bottom: 25px;">Deine aktive Sitzung ist abgelaufen<br>Aus Sicherheitsgr&uuml;nden musst Du deine Zugangsdaten erneut eingeben</label>'."\r\n";
			} else {
				echo '			<label style="margin-bottom: 25px;">Bitte gib jetzt deine Zugangsdaten ein:</label>'."\r\n";
			}
?>
			<div class="col_half">
				<div class="input_field"> <span><i aria-hidden="true" class="fas fa-user"></i></span>
					<input type="text" name="username" value="gwn-user" required>
				</div>
			</div>
			<div class="col_half">
				<div class="input_field"> <span><i aria-hidden="true" class="fas fa-lock"></i></span>
					<input type="password" name="password" placeholder="Passwort" required autofocus>
				</div>
			</div>
		</div>
		<input type="submit" value="Passwort abschicken">
		</form>
		<div class="footer_container">
			<strong>Die Liste ist nicht &ouml;ffentlich! Du ben&ouml;tigst ein Passwort um fortzufahren.</strong><br>Koordinatoren k&ouml;nnen unter <strong><a href="mailto:info@gwn-liste.de?subject=Anfrage:%20Zugangsdaten%20zur%20GWN-Adressliste" style="text-align: right;" onclick="javascript:location.href='mailto:info@gwn-liste.de?subject=Anfrage:%20Zugangsdaten%20zur%20GWN-Adressliste; return false;"><i class="fas fa-envelope"></i> info@gwn-liste.de</a></strong> einen Zugang anfordern.<br>
			Nach erfolgreicher Pr&uuml;fung erh&auml;lst Du Deine Zugangsdaten per E-Mail.
		</div>
		<div class="footer_container" style="margin-bottom: 12px;">
			<a class="backlinks" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
			<a class="backlinks" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
		</div>
	</div>
</div>

</body>
<?php
	}
?>
</html>