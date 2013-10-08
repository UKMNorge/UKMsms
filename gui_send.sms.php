<?php
/// BEREGN MOTTAKERE
// Vanlige mottakere
$mottakere = explode(',', $_POST['mottakere']);
// Det finnes kun rapport-mottakere
if(isset($_POST['ekstra_mottakere'])&&!empty($_POST['ekstra_mottakere'])&&!empty($_POST['mottakere']))
	$mottakere = array_merge($mottakere, explode(',', $_POST['ekstra_mottakere']));
// Det finnes en blanding av rapport- og vanlige mottakere
elseif(isset($_POST['ekstra_mottakere'])&&!empty($_POST['ekstra_mottakere']))
	$mottakere = explode(',', $_POST['ekstra_mottakere']);

// Sikre at det kun finnes unike mottakere (dumt å spamme sveve)
$mottakere = array_unique($mottakere);

$beskjed = strip_tags($_POST['message']."\r\n".$_POST['message_from'], '<br>');
// INITIER SENDING 
$transaction = SMS_send_init($mottakere, $beskjed);

$avsender = $_POST['sender'];
?>

<div class="wrap">
	<div id="icon-edit-pages">
		<img src="<?= UKMN_ico('mobile', 32,false)?>" style="float: left; margin-top: 10px; margin-right: 10px;" width="32" />
	</div>
	<h2>Sender SMS..</h2>
	<div class="updated" style="width: 420px; font-weight: bold;">IKKE NAVIGER BORT FRA DENNE SIDEN FØR ALLE SMS ER SENDT!</div>
	
	<ul class="sendSMS">
<?php
	$i = 0;
	foreach($mottakere as $mottaker) {
		$i++;
		$mottaker = str_replace(' ','',$mottaker);
		$status = SMS_send($transaction, $beskjed, $mottaker, $avsender);
		var_dump($status);
		?>
		<li class="error_<?= $status ? 'true' : 'false' ?>">
			<div class="number"><?= $i ?> av <?= sizeof($mottakere)?></div>
			<div class="mottaker"><?= substr($mottaker,0,3).' '.substr($mottaker, 3,2).' '.substr($mottaker,5) ?></div>
			<div class="status"><?= empty($status['message']) ? 'Sendt!' : $status['message'] ?></div>
			<div class="clear"></div>
		</li>
		<?php
	}
?>
	</ul>
	<div class="clear"></div>
	<div class="clear"></div>
	
	<div class="updated" style="width: 420px; font-weight: bold;">Alle SMS er nå sendt</div>
	<h3>Neste stopp:</h3>
	<a href="?page=<?= $_GET['page'] ?>" class="add-new-h2">Skriv ny SMS</a>
	<br /><br />
	<?= SMS_returnLink('Tilbake til '.urldecode($_GET['UKMSMS_returnname'])); ?>
</div>