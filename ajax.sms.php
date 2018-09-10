<?php
function UKMSMS_ajax(){
	switch($_POST['SMSaction']){
		case 'log':				UKMSMS_ajax_log();
	}
	die();
}

function UKMSMS_ajax_log(){
	$plid = get_option('pl_id');
	$qry = new SQL("SELECT * FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					ORDER BY `t_id` ASC",
					array('plid'=>$plid));
	$res = $qry->run();
	$m = new monstring($plid);
	?>
	<h3>SMS-logg for <?= $m->g('pl_name')?></h3>
	<ul class="log">
		<li class="header">
			<div class="time">Tidspunkt</div>
			<div class="action">Handling</div>
			<div class="user">Aktiv bruker</div>
			<div class="description"></div>
		</li>
	<?php
	while($r = SQL::fetch($res)){?>
		<li class="trans">
			<div class="time"><?= $r['t_time']?><br /><em>Transaksjon: <?= $r['t_id']?></em></div>
			<div class="action"><?= ucfirst(SMS_human('action',$r['t_action'])).' '.abs($r['t_credits'])?> SMS-credits</div>
			<div class="user"><?= $r['wp_username']?></div>
			<div class="description"><?= utf8_encode($r['t_comment'])?></div>
		</li>
	<?php
	}
	?></ul><?php
}