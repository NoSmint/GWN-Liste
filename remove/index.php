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
	if (isset($_GET["action"]) && $_GET["action"] == "remove") {
		
	echo 'SUCCESS<br><br>Dies ist eine temporäre Seite<br><br>Für tatsächliche Löschung bitte eine E-Mail an info@gwn-liste.de';
	
} else {

?>
<body>

<div class="bg_img"></div>
<div class="form_wrapper_entry">
	<div class="container_bg"></div>
		<div class="form_container">
		<div class="title_container">
			<h2><?php echo $config["Base"]["VereinLong"];?></h2>
			<h3>Adresse aus der Liste entfernen</h3>
		</div>
		<form action="../remove/?action=remove" method="post" accept-charset="utf-8">
		
			<div class="row clearfix">
				<label style="margin-top: 0px;">Meine Kontaktdaten</label>
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

			<div class="clearfix">
				<div class="input_field"> <span><i aria-hidden="true" class="fas fa-envelope"></i></span>
					<input type="email" name="email" placeholder="E-Mail *" required onkeyup="validate('validemail','email')">
					<div id="validemail" class="validity"></div>
				</div>
			</div>
			
			<div class="clearfix" style="margin-top: 0px;">
				<label class="questions">Grund f&uuml;r die L&ouml;schung *</label>
				<div class="custom-select" id="select-reason" style="text-align: center;">
					<div class="input_field"> <span><i aria-hidden="true" class="fas fa-question-circle"></i></span>
						<select id="reason" name="reason" style="text-align-last:center;" onChange="findselected()">
							<option value="">Bitte w&auml;hlen</option>
							<option value="NoReason">Ich möchte keinen Grund nennen</option>							
							<option value="Change">Meine Daten haben sich geändert</option>
							<option value="Wrong">Meine bisherigen Daten sind falsch</option>
							<option value="Move">Ich bin umgezogen</option>
							<option value="Quit">Ich bin nicht mehr für den Verein tätig</option>
							<option value="Other">Sonstiger Grund</option>
						</select>
					</div>
				</div>
			</div>	

			<div id="otherreason" class="clearfix otherreason-hide" style="margin-top: 1.5em;">
				<div class="input_field"> <span><i aria-hidden="true" class="fas fa-envelope"></i></span>
					<input type="text" name="sonstiges" placeholder="Sonstiger Grund">
					<div id="validemail" class="validity"></div>
				</div>
			</div>			
			
			<div id="infobox-change" class="infobox infobox-hide"><span style="float: right; margin-top: -10px; margin-right: -8px; font-size: 2em; color: Tomato;"><i aria-hidden="true" class="fas fa-exclamation-triangle"></i></span>
				<h2>Hinweis</h2>
				F&uuml;r eine Korrektur deiner Daten musst Du den alten Datensatz nicht unbedingt l&ouml;schen. Du kannst einfach <a href="../add" style="font-weight: bold;" onclick="javascript:location.href='../add'; return false;"><i class="fas fa-user-plus"></i> zur Eingabe</a> gehen und deine neuen Daten eingeben. Dein Datensatz wird daraufhin aktualisiert. Name, Vorname und E-Mailadresse müssen dabei den bisherigen Eingaben entsprechen.
			</div>

			<div id="infobox-move" class="infobox infobox-hide"><span style="float: right; margin-top: -10px; margin-right: -8px; font-size: 2em; color: Tomato;"><i aria-hidden="true" class="fas fa-exclamation-triangle"></i></span>
				<h2>Hinweis</h2>
				 Um Deine Daten bei einem Umzug zu aktualisieren, kannst Du einfach <a href="../add" style="font-weight: bold;" onclick="javascript:location.href='../add'; return false;"><i class="fas fa-user-plus"></i> zur Eingabe</a> wechseln, und deine neue Anschrift eintragen. Dein Datensatz wird dann aktualisiert. Deine bisherige E-Mailadresse und dein Name d&uuml;rfen sich dabei nicht &auml;ndern.
			</div>			
			
			<input class="button" type="submit" id="absenden" value="Jetzt Löschung beantragen">
			
		</form>			

		<div class="footer_container">
			* Die Angabe eines Grundes ist freiwillig, hilft uns aber bei der Pflege der Liste.<br>
			Bei Fragen kannst Du Dich jederzeit an <strong><a href="mailto:info@gwn-liste.de?subject=Anfrage:%20Zugangsdaten%20zur%20GWN-Adressliste" style="text-align: right;" onclick="javascript:location.href='mailto:info@gwn-liste.de?subject=Anfrage:%20Zugangsdaten%20zur%20GWN-Adressliste; return false;"><i class="fas fa-envelope"></i> info@gwn-liste.de</a></strong> wenden.
		</div>
		<div class="footer_container" style="margin-bottom: 12px;">
			<a class="backlinks" href="<?php echo _BACKLINK_;?>" onclick="javascript:location.href='<?php echo _BACKLINK_;?>'; return false;"><i class="fas fa-chevron-circle-left"></i> Zur&uuml;ck zur Startseite</a>
		</div>
		
	</div>
</div>

<script src="<?php echo _URL_;?>js/remove.js"></script>

</body>
</html>

<?php
}
?>