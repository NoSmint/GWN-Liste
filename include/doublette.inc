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
	<meta name="description" content="Adressverzeichnis f&uuml;r Vereinsmitglieder des S.V. DJK Gr&uuml;n-Wei&szlig; Nottuln 1919 e.V">
	<meta name="keywords" content="Adressliste, Adressverzeichnis, GWN, Gr&uuml;n-Wei&szlig; Nottuln, Mitglieder, Trainer, Betreuer">
	<meta name="author" content="Nils Uhde">
	<meta name="copyright" content="Nils Uhde, S.V. DJK Gr&uuml;n-Wei&szlig; Nottuln 1919 e.V">
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
			<h2>S.V. DJK Gr&uuml;n-Wei&szlig; <wbr>Nottuln 1919 e.V.</h2>
			<h3>Adressenliste: Doppelter Eintrag</h3>
		</div>
		<form action="../add/?action=new" method="post">
		<input type="hidden" name="first_name" value="<?php echo $_POST["first_name"];?>">
		<input type="hidden" name="last_name" value="<?php echo $_POST["last_name"];?>">
		<input type="hidden" name="email" value="<?php echo $_POST["email"];?>">
		<input type="hidden" name="street" value="<?php echo $_POST["street"];?>">
		<input type="hidden" name="plz" value="<?php echo $_POST["plz"];?>">
		<input type="hidden" name="ort" value="<?php echo $_POST["ort"];?>">
		<input type="hidden" name="mobil" value="<?php echo $_POST["mobil"];?>">
		<input type="hidden" name="phone" value="<?php echo $_POST["phone"];?>">
		<input type="hidden" name="position" value="<?php echo $_POST["position"];?>">
		<?php if (isset($_POST["other"])) {?><input type="hidden" name="other" value="<?php echo $_POST["other"];?>"><?php } ?>
		
		<input type="hidden" name="mannschaft" value="<?php echo $_POST["mannschaft"];?>">
		<input type="hidden" name="verteiler" value="<?php echo $_POST["verteiler"];?>">
		<input type="hidden" name="publish" value="<?php echo $_POST["publish"];?>">
		
		<div class="row clearfix" style="margin-bottom: 30px;">
			<label style="text-align: center; margin: 35px 0 15px 0;">Ein Kontakt mit dem Namen <strong><?php echo htmlentities($ausgabe["first_name"] . " " . $ausgabe["last_name"]);?></strong> existiert bereits.<br><br>
			M&ouml;chtest Du den bestehenden Kontakt mit den neuen Infos aktualisieren?</label>
			<div class="col_half">
				<input type="submit" name="update_button" value="Kontakt aktualisieren">
			</div>
			<div class="col_half">
				<input type="submit" name="abort" value="Abbrechen" onclick="javascript:window.location.replace('../'); return false;">
			</div>
		</div>
		</form>
		<div class="footer_container" style="margin-bottom: 12px;">
			<a class="backlinks" href="../" onclick="javascript:location.href='../'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
			<a class="eintragen" href="../add" style="text-align: right;" onclick="javascript:location.href='../add'; return false;"><i class="fas fa-user-plus"></i> Zur Eingabe</a>
		</div>
	</div>
</div>

</body>
</html>