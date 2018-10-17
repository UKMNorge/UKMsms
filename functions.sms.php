<?php
function SMS_credits(){
	if( is_network_admin() ) {
		return 100000;
	}
	
	$plid = get_option('pl_id');
	SMS_init($plid, SMS_wpsender());
	
	$qry = new SQL("SELECT SUM(`t_credits`) AS `credits`
					FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					AND `t_system` != 'pamelding'
					AND `t_system` != 'pameldingUKMvalidate'",
					array('plid'=>$plid));
	return (int) $qry->run('field','credits');
}

function SMS_wpsender(){
	$user = wp_get_current_user();
	return $user->user_login;
}

function SMS_initCredits(){
	return get_site_option('UKMmateriell_sms_forfree') == false ? 400 : get_site_option('UKMmateriell_sms_forfree');
}

function SMS_init($plid, $wpusr){
	global $blog_id;
	if( $blog_id == 1 ||is_network_admin() ) {
		$plid = 1;
	}
	$qry = new SQL("SELECT * FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					AND `t_action` = 'mottok'",
					array('plid'=>$plid));
	$res = $qry->run();
	if( SQL::numRows( $res ) == 0 ){
		$ins = new SQLins('log_sms_transactions');
		$ins->add('pl_id', $plid);
		$ins->add('wp_username', $wpusr);
		$ins->add('t_action','mottok');
		$ins->add('t_credits',SMS_initCredits());
		$ins->add('t_comment', 'Begynte å bruke SMS-modulen');
		$ins->run();
	}
}

function SMS_human($type, $data){
	switch($type){
		case 'action':
			return str_replace('_',' ', $data);
	}	
}

function SMS_avsendere(){
	global $blog_id;
	
	$norge_options = '<option value="UKMNorge" data-svar="true" data-name="">UKMNorge</option>';
	$norge_options .='<option value="UKMMedia" data-svar="true" data-name="">UKMMedia</option>';
	$norge_options .='<option value="93091329" data-svar="true" data-name="">93091329 (UKM Norge support)</option>';
	$norge_options .='<option value="92837360" data-svar="true" data-name="">92837360 (Marius Mandal)</option>';
	$norge_options .='<option value="93001178" data-svar="true" data-name="">93001178 (Anne M Hybertsen)</option>';
	$norge_options .='<option value="90755685" data-svar="true" data-name="">90755685 (Torstein Siegel)</option>';
	$norge_options .='<option value="93454190" data-svar="true" data-name="">93454190 (Inger Lise Johnsen)</option>';
	$norge_options .='<option value="93665540" data-svar="true" data-name="">93665540 (Jardar Nordbø)</option>';
	$norge_options .='<option value="92688810" data-svar="true" data-name="">41123976 (Stine Granly)</option>';
	$norge_options .='<option value="92688810" data-svar="true" data-name="">45111635 (Martine Jonsrud)</option>';

	if($blog_id == 1 || is_network_admin() ){
		return $norge_options;
	}


	$m = new monstring(get_option('pl_id'));
	$kontakter = $m->kontakter();
	if(get_option('site_type')=='fylke')
		$options = '<option value="UKMfylke" data-svar="false" data-name="">UKMfylke</option>';
	else
		$options = '<option value="UKMlokal" data-svar="false" data-name="">UKMlokal</option>'; 
	foreach($kontakter as $k) {
		$phone = $k->mobil();
		if(!$phone)
			continue;
		$options .= '<option value="'.$phone.'" data-svar="true" data-name="'.$k->g('firstname').'">'.$phone.' ('.$k->g('name').')</option>';
	}	
	
	if( is_super_admin() ) {
		return $options . $norge_options;
	}
	return $options;
}

function SMS_from(){
	global $blog_id;
	
	if($blog_id == 1 || is_network_admin() ){
		return '- UKM';
	}
	$m = new monstring(get_option('pl_id'));
	
	$from = '- UKM '. $m->g('pl_name');
	
	if(strlen($from) > 20)
		return '- UKM';
		
	return $from;
}

function SMS_returnLink($text=false){
	if(isset($_GET['UKMSMS_returnname'])&&isset($_GET['UKMSMS_returnto'])){
	
		if(!$text)
			$text = 'Avbryt, tilbake til '.urldecode($_GET['UKMSMS_returnname']);
	
		$url = urldecode($_GET['UKMSMS_returnto']);
		if(strpos($url, '?')!==0)
			$url = '?'.$url;
			
		return '<a href="'.$url.'" class="add-new-h2">'
				. $text
				. '</a>';
	}
	return '';
}

/*
function SMS_credit_calc($message){
	$credits = strlen($message);
	if($credits <= 160)
		return 1;
	return ceil($credits/154);
}

function SMS_send_init($recipients, $message){
	$message = str_replace(array('<br />','<br>'),"\r\n",$message);
	$credits = SMS_credit_calc($message);
	$credits = $credits * sizeof($recipients);
	
	$t = new SQLins('log_sms_transactions');
	$t->add('pl_id',get_option('pl_id'));
	$t->add('wp_username', SMS_wpsender());
	$t->add('t_action','sendte_sms_for');
	$t->add('t_credits', $credits*-1);
	$t->add('t_comment', nl2br($message));
	$res = $t->run();
	
	$trans_id = $t->insid();
	
	foreach($recipients as $r) {
		$ins = new SQLins('log_sms_transaction_recipients');
		$ins->add('t_id', $trans_id);
		$ins->add('tr_recipient', $r);
		$ins->add('tr_status', 'queued');
		$ins->run();
	}
	return $trans_id;
}

function SMS_send($trans_id, $message, $recipient, $from){
	$upd = new SQLins('log_sms_transaction_recipients', array('tr_recipient'=>$recipient, 't_id'=>$trans_id));
	$upd->add('tr_status', 'sent');
	$upd->run();
	$message = stripslashes(str_replace(array('<br>','<br />'),"\r\n", $message));
	$sms = new SMS('wordpress', get_current_user_id(), get_option('pl_id'));
	$sms->text($message)->to($recipient)->from($from)->ok();
	$report = $sms->report();
	var_dump($report);

	if(!is_numeric($report)) {
		SMS_refund($trans_id, $recipient, $message, $res['message']);
		return array('error' =>true, 'message' => $report);
	}
	return array('error' =>false, 'message' => 'Sendt!');
}

function SMS_refund($trans_id, $recipient, $message, $error){
	$ref = new SQLins('log_sms_transactions');
	$ref->add('pl_id', get_option('pl_id'));
	$ref->add('wp_username', SMS_wpsender());
	$ref->add('t_action','mottok');
	$ref->add('t_credits', SMS_credit_calc($message));
	$ref->add('t_comment', 'Mottaker '.$recipient.' ga feilmelding i transaksjon '.$trans_id.': '.$error);
	$ref->run();
}
*/
