<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Wordpress\Nyhet;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Feedback\FeedbackArrangor;
use UKMNorge\Feedback\FeedbackResponse;
use UKMNorge\Feedback\Feedbacks;

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
		case 'getSMSLog':
			getSMSLog();
			break;
		case 'sendSMS':
			sendSMS();
			break;
		case 'getInnslagMottakere':
			getInnslagMottakere();
			break;
		case 'saveFeedback':
			saveFeedback();
			break;
		case 'hasUserAnsweredFeedback':
			hasUserAnsweredFeedback();
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

function saveFeedback() {
	$handleCall = new HandleAPICall(['campaignId', 'question', 'answerId'], ['answerText'], ['GET', 'POST'], false);

	$campaignId = $handleCall->getArgument('campaignId');
	$question = $handleCall->getArgument('question');
	$answerId = $handleCall->getArgument('answerId');
	$answerText = $handleCall->getOptionalArgument('answerText');

	$userId = get_current_user_id();

	$answer = !empty($answerText) ? $answerText : $answerId;

	$feedback = new FeedbackArrangor(-1, [], $userId, $campaignId);

	$feedback->leggTilResponse(new FeedbackResponse(-1, $question, $answer));

	$res = $feedback->save();

	$handleCall->sendToClient([
		'result' => $res,
	]);
}

function hasUserAnsweredFeedback() {
	$handleCall = new HandleAPICall(['campaignId'], [], ['GET', 'POST'], false);

	$campaignId = $handleCall->getArgument('campaignId');
	$userId = get_current_user_id();

	$feedbacks = Feedbacks::getAllFeedbacksUserCampaign($userId, $campaignId);


	$handleCall->sendToClient([
		'hasAnswered' => count($feedbacks) > 0,
	]);
}

function sendSMS() {
	$handleCall = new HandleAPICall(['avsender', 'mottakere', 'message'], [], ['GET', 'POST'], false);

	$avsender = $handleCall->getArgument('avsender');
	$mottakere = $handleCall->getArgument('mottakere');
	$message = $handleCall->getArgument('message');

	// Check if avsender is in the list of avsendere
	if( !array_key_exists($avsender, SMS_avsendere_array()) ) {
		$handleCall->sendErrorToClient('Avsenderen er ugyldig!', 400);
		return;
	}
	
	if( !is_array($mottakere) ) {
		$handleCall->sendErrorToClient('Listen av mottakere er ugyldig!', 400);
		return;
	}

	$reports = [];
	foreach($mottakere as $mottaker) {
		// $message = stripslashes(str_replace(array('<br>','<br />'),"\r\n", $message));
		// $pl_id = ($blog_id == 1 || is_network_admin() ) ? 1 : get_option('pl_id');

		// $sms = new SMS('wordpress', get_current_user_id(), $pl_id);
		// $sms->text($message)->to($mottaker)->from($avsender)->ok();
		// $report = $sms->report();

		// Wait 10 seconds
		
		$reports[] = [
			'mottaker' => $mottaker,
			'report' => 'OK',
		];
	}
	
	sleep(1);
	$handleCall->sendToClient([
		'reports' => $reports,
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
		'isArrangement' => get_option('pl_id') ? true : false,
	]);

}

function getSMSLog() {
	$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

	$plID = get_option('pl_id');
	$qry = new Query("SELECT * FROM `log_sms_transactions`
		WHERE `pl_id` = '#plid'
		ORDER BY `t_id` DESC LIMIT 500",
		array('plid' => $plID)
	);
	$res = $qry->run();

	$logs = [];
	while($r = Query::fetch($res)) {
		$logs[] = [
			'time' => $r['t_time'],
			'action' => ucfirst(SMS_human('action', $r['t_action'])) . ' ' . abs($r['t_credits']) . ' SMS-credits',
			'user' => $r['wp_username'],
			'description' => $r['t_comment'],
		];
	}
	
	$handleCall->sendToClient([
		'logs' => $logs,
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