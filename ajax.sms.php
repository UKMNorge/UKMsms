<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Wordpress\Nyhet;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;


require_once('UKM/Autoloader.php');

function UKMSMS_ajax(){
	switch($_POST['SMSaction']){
		case 'log':
			UKMSMS_ajax_log();
			break;
		case 'nyhetssak':
			getNyhetssaker();
			break;
	}
	die();
}

function getNyhetssaker() {
	$posts = new Posts();
	Wordpress::setPosts($posts);
	
	echo json_encode($posts->getAll());
}

function UKMSMS_ajax_log(){
	$plid = get_option('pl_id');
	$qry = new Query("SELECT * FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					ORDER BY `t_id` ASC",
					array('plid'=>$plid));
	$res = $qry->run();
	$arrangement = new Arrangement(intval(($plid)));
	?>
	<h3>SMS-logg for <?= $arrangement->getNavn() ?></h3>
	<ul class="log">
		<li class="header">
			<div class="time">Tidspunkt</div>
			<div class="action">Handling</div>
			<div class="user">Aktiv bruker</div>
			<div class="description"></div>
		</li>
	<?php
	while($r = Query::fetch($res)){?>
		<li class="trans">
			<div class="time"><?= $r['t_time']?><br /><em>Transaksjon: <?= $r['t_id']?></em></div>
			<div class="action"><?= ucfirst(SMS_human('action',$r['t_action'])).' '.abs($r['t_credits'])?> SMS-credits</div>
			<div class="user"><?= $r['wp_username']?></div>
			<div class="description"><?= $r['t_comment']?></div>
		</li>
	<?php
	}
	?></ul><?php
}