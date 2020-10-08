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

</head>

<body>

<div class="bg_img"></div>
<div class="form_wrapper_entry">
	<div class="container_bg"></div>
		<div class="form_container">
		<div class="title_container">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Adressenliste: Erfolgreich eingetragen</h3>
		</div>		
		<div class="clearfix success">
			<label style="text-align: center; margin: 35px 0 15px 0;">Danke <?php echo $ausgabe["first_name"];?><br><br></label>
			Du hast Dich erfolgreich in die Adressliste eingetragen.
			<input type="submit" name="backtohome" value="Zur&uuml;ck zur Startseite" onclick="javascript:window.location.replace('../'); return false;">
		</div>
		<div class="footer_container" style="margin-bottom: 12px;">
			<a class="backlinks" href="../" onclick="javascript:location.href='../'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
		</div>
	</div>
</div>

</body>
</html>