<?php
$credits = SMS_credits();
$returnLink = SMS_returnLink();

if(isset($_POST['UKMSMS_recipients'])){
	$recipients = explode(',',$_POST['UKMSMS_recipients']);
	array_unique($recipients);
}
?>
<div class="wrap"><div id="icon-edit-pages"><img src="//ico.ukm.no/mobile-32.png" style="float: left; margin-top: 10px; margin-right: 10px;" width="32" /></div>
<h2>Send SMS <?= $returnLink ?></h2>

<?php if(!isset($_POST['UKMSMS_recipients'])||!is_array($recipients)) { ?>
	<div class="updated" id="sendtilflere">
		Hvis du skal sende SMS til flere mottakere, 
        anbefaler vi at du går via 
        <a href="?page=UKMrapporter">rapporter</a>
        for å sende til dine deltakere.
	</div>
<?php } ?>

<form action="?<?=$_SERVER['QUERY_STRING']?>" method="post" id="SMSform">
	<div id="credits">
		<div style="display:none;">Du har <span class="available"><?= $credits ?></span> tekstmeldinger tilgjengelig.</div>
		<?php
		if(!is_network_admin()) {
			echo '<a href="#" id="credits_log">se SMS-logg</a>';
		}
		?>
		<a href="#" id="credits_log_hide" style="display:none;">skjul SMS-logg</a>
		<div id="log"></div>
	</div>
	<div class="description"> Alle SMS belastes UKM Norge, uansett hvilken avsender som velges. Alle får <?= SMS_initCredits()?> tekstmeldinger gratis, og alt forbruk utover dette <u>kan</u> bli fakturert sammen med neste års materiellpakke. 
	<br />1 SMS = kr. 0,40</div>

	<div id="iphone_preview">
		<div id="sender">Avsender</div>
		<div id="message">Melding</div>
		<div id="reply">Svar</div>
	</div>
	
	<div id="sender">
		<div class="title">Avsender</div>
		<select name="sender" id="selected_sender"><?= SMS_avsendere() ?></select>
		<div class="description">Listen viser alle kontaktpersoner for mønstringen som er registrert med mobilnummer i systemet.
		<br /><a href="?page=UKMMonstring">Endre kontaktpersoner</a></div>
		<div id="obs"></div>
	</div>

	<div id="message">
		<div class="title">Melding</div>
		<textarea name="message" id="the_message" maxlength="612"><?= isset($_POST['UKMSMS_message']) ? $_POST['UKMSMS_message'] : '' ?></textarea>
		<input type="hidden" id="message_from_value" name="message_from" value="<?= SMS_from()?>" />
		<input type="hidden" id="message_really_from" name="trahs" value="<?= SMS_from()?>" />
		<div id="message_from"><?= SMS_from()?></div>
		<div id="obs"></div>
		<?php if( is_super_admin() ) { ?>
		<label style="font-weight: normal;">
			<input type="checkbox" id="signature_hide" name="signature_hide" value="true" /> Skjul signatur
		</label>
		<?php } ?>
	</div>

	<div id="nyhetssak">
		<div class="title">Legg ved nyhetssak</div>
		<div class="header-div">
			<select id="selectNyhetssaker" name="nyhetssaker"></select>
			<a id="oppdaterNyhetssak" target="_blank" class="clickable-link">
				<span class="wp-menu-image dashicons-before dashicons-update"></span> 
				Oppdater listen
			</a>
		</div>
		
		<p class="beskrivelse">Når du velger en nyhetssak her, legger vi automatisk til en lenke i slutten av meldingen din.</p>

		<a href="/wp-admin/post-new.php" class="clickable-link" target="_blank">Trykk her for å lage nyhetssak</a>
	</div>
	
	<?php if(isset($_POST['UKMSMS_recipients'])&&is_array($recipients)){?>
	<div id="extra_recipients">
		<div class="title" style="background: #4f94e8;">Forhåndsvalgte mottakere</div>
		<div id="the_extra_recipients"><p>Rapporten du kommer fra har lagt til <?= sizeof($recipients)?> mottaker(e).<br />
								Ønsker du at flere skal motta meldingen, skriver du inn disse i feltet &quot;mottakere&quot;</p></div>
		<input type="hidden" name="ekstra_mottakere" id="ekstra_mottakere" value="<?= implode(',',$recipients)?>" />
		<div class="description"></div>
		<div id="obs"></div>
	</div>
	<?php } ?>

	<div id="recipients">
		<div class="title">
			<?php if(isset($_POST['UKMSMS_recipients'])&&is_array($recipients)) { ?>
			Flere mottakere
			<?php } else { ?>
			Mottakere
			<?php } ?>
		</div>
		<div id="the_recipients"><input type="text" name="mottakere" id="mottakere" /></div>
		<div class="recipients" id="obs"></div>
		<div class="description">Kommaseparert liste over mottakere</div>
	</div>
	
	<div id="logwarning">
	<strong>OBS:</strong> Alle SMS logges og lagres! <a href="#" class="logwarningread" id="logwarningreadmorelink">Les mer</a>
	<div id="logwarning_more" style="display:none;">
		SMS som sendes med dette systemet går fra UKM-Norges konto hos tjenesteleverandøren. <br />
		UKM-Norge er juridisk ansvarlig for innholdet, og vi logger og lagrer derfor alle meldinger slik at <br />
		eventuelle misbruk av systemet til personangrep, spam, eller private formål kan oppdages og <br />
		spores tilbake til avsender. <br />
		Sendingsloggen brukes også som fakturagrunnlag i de tilfeller UKM Norge finner det nødvendig<br />
		 å fakturere for forbruk utover gratiskvoten.
		<br /><a href="#" class="logwarningread">Skjul forklaring</a>
	</div>
	</div>
	
	<input type="submit" id="SMS_send" name="SMS_send" value="Send" />

</div>
</form>
