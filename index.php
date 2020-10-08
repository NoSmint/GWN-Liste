<?php
define('_ROOT_', $_SERVER['DOCUMENT_ROOT']."/GWN-Liste");
define('_URL_', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');
require_once(_ROOT_.'/include/errors.inc');
$config = parse_ini_file(_ROOT_.'/config.ini', TRUE, INI_SCANNER_TYPED);
?>
<!DOCTYPE HTML>
<html>
<head>
<?php
require_once('include/headmeta.inc');
?>

	<script>
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('sw.js', {
			scope: '.' // <--- THIS BIT IS REQUIRED
		}).then(function(registration) {
			// Registration was successful
			console.log('ServiceWorker registration successful with scope: ', registration.scope);
		}, function(err) {
			// registration failed :(
			console.log('ServiceWorker registration failed: ', err);
		});
	}

	var deferredPrompt;

	window.addEventListener('beforeinstallprompt', function(e) {
		console.log('beforeinstallprompt Event fired');
		e.preventDefault();

		var aths = document.getElementsByClassName("A2HS");
		for (var i=0; i<aths.length; i++) {
			aths[i].style.display = "block";
		}
		document.getElementById("A2HSI").style.display = "none";
		
		// document.getElementById("A2HS").style.display = "block";
		// Stash the event so it can be triggered later.
		deferredPrompt = e;

	return false;
	});

	function rePrompt() {
		if(deferredPrompt !== undefined) {
			// The user has had a positive interaction with our app and Chrome
			// has tried to prompt previously, so let's show the prompt.
			deferredPrompt.prompt();
			
			
			// Follow what the user has done with the prompt.
			deferredPrompt.userChoice.then(function(choiceResult) {

			console.log(choiceResult.outcome);

			if(choiceResult.outcome == 'dismissed') {
				console.log('User cancelled home screen install');
			}
			else {
				console.log('User added to home screen');
			}

			// We no longer need the prompt. Clear it up.
			deferredPrompt = null;
			
			var aths = document.getElementsByClassName("A2HS");
			for (var i=0; i<aths.length; i++) {
				aths[i].style.display = "none";
			}

			});
		}
	}
	
	</script>
	
</head>
<body>

<div class="bg_img"></div>
<div class="form_wrapper_index">
	<div class="container_bg"></div>
	<div class="form_container" style="text-align: center;">
		<div class="title_container" style="margin-bottom: 25px;">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Adressverzeichnis f&uuml;r Vereinsmitglieder</h3>
		</div>
		Was m&ouml;chtest Du tun?<br>
		<div class="clearfix" style="margin-top: 0px;">
			<input class="button" type="submit" value="Meine Daten eintragen" onclick="javascript:window.location.href='/add<?php if (isset($_GET["source"])) echo "?source=".$_GET["source"] ?>'; return false;">
		</div>
		<div class="row clearfix" style="margin-top: 0px;">
			<div class="col_half">
				<input class="button" type="submit" value="Mich austragen" onclick="javascript:window.location.href='/remove<?php if (isset($_GET["source"])) echo "?source=".$_GET["source"] ?>'; return false;">
			</div>	
			<div class="col_half">
				<input class="button" type="submit" value="Mannschaftsübersicht" onclick="javascript:window.location.href='https://fussball-nottuln.de/junioren'; return false;">
			</div>	
		</div>
		<div class="clearfix addToHomescreen">
			<span class="A2HS informer">JETZT NEU!</span>
			<input class="A2HS button" type="submit" value="Als App auf den Startbildschirm legen" onclick="javascript:rePrompt(); return false;">
			<div class="A2HS footer_container">
				<span class="A2HS subtitle">Auf mobilen Ger&auml;ten kannst Du diese Webseite als App installieren und so direkt von deinem Startbildschirm aufrufen</span>
			</div>
			<div class="footer_container" id="A2HSI">
			<?php
			if (!isset($_GET["source"]) || isset($_GET["source"]) && $_GET["source"] != "launcher") echo '			<span class="A2HSI subtitle">Auf mobilen Ger&auml;ten kannst Du diese Webseite als App installieren und so direkt von deinem Startbildschirm aufrufen. Rufe dazu einfach diese Seite mit dem mobilen Google Chrome Browser f&uuml;r Android auf. Funktioniert nicht mit iOS.</span>';
			?>
			</div>
		</div>
		<div class="clearfix">
			<div class="footer_container credit">
				<a href="#">Impressum</a> &middot; <a href="#">Datenschutz</a> &middot; <a href="/liste<?php if (isset($_GET["source"])) echo "?source=".$_GET["source"] ?>" onclick="javascript:window.location.href='/liste<?php if (isset($_GET["source"])) echo "?source=".$_GET["source"] ?>'; return false;">Admin</a>
			</div>
		</div>
	</div>
</div>

</body>
</html>