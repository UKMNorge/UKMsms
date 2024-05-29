<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Wordpress\Nyhet;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\OAuth2\HandleAPICall;


require_once('UKM/Autoloader.php');

function UKMSMS_ajax(){
	switch($_POST['SMSaction']){
		case 'log':
			UKMSMS_ajax_log();
			break;
		case 'nyhetssak':
			getNyhetssaker();
		case 'getNyhetsakerJson':
			getNyhetsakerJson();
			break;
		case 'getInitialData':
			getInitialData();
			break;
		case 'getInnslagMottakere':
			getInnslagMottakere();
			break;
	}
	die();
}

function getInnslagMottakere() {
	$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

	$arrangement = new Arrangement(intval((get_option('pl_id'))));

	$allePersoner = [];
	$arrangement->getInnslag()->getAll();
	foreach($arrangement->getInnslag()->getAll() as $innslag) {
		foreach($innslag->getPersoner()->getAll() as $person) {
			$allePersoner[$innslag->getId()][] = [
				'navn' => $person->getNavn(),
				'mobil' => $person->getMobil(),
				'innslagNavn' => $innslag->getNavn(),
				'innslagType' => $innslag->getType()->getNavn(),
			];
		}
	}

	$handleCall->sendToClient([
		'personer' => $allePersoner,
	]);
}

function getNyhetsakerJson() {
	$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

	$posts = new Posts();
	Wordpress::setPosts($posts);
	
	$retObj = [];
	foreach($posts->getAll() as $post) {
		$retObj[] = [
			'id' => $post->getId(),
			'title' => $post->getTitle(),
			'content' => $post->getContent(),
			'link' => get_permalink($post->getId()),
		];
	}

	$handleCall->sendToClient([
		'posts' => $retObj,
	]);
}

function getNyhetssaker() {
	$posts = new Posts();
	Wordpress::setPosts($posts);
	
	echo json_encode($posts->getAll());
}

function getInitialData() {
	$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);
	$handleCall->sendToClient([
		'SMS_credits' => SMS_credits(),
		'SMS_returnLink' => SMS_returnLink(),
		'SMS_initCredits' => SMS_initCredits(),
		'SMS_avsendere' => SMS_avsendere_array(),
	]);

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