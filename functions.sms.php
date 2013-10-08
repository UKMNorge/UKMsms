<?php
function SMS_credits(){
	$plid = get_option('pl_id');
	SMS_init($plid, SMS_wpsender());
	
	$qry = new SQL("SELECT SUM(`t_credits`) AS `credits`
					FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'",
					array('plid'=>$plid));
	return (int) $qry->run('field','credits');
}

function SMS_wpsender(){
	$user = wp_get_current_user();
	return $user->user_login;
}

function SMS_initCredits(){
	return 200;
}

function SMS_init($plid, $wpusr){
	$qry = new SQL("SELECT * FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					AND `t_action` = 'mottok'",
					array('plid'=>$plid));
	$res = $qry->run();
	if(mysql_num_rows($res)==0){
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
	
	if($blog_id == 1){
		$options = '<option value="UKMNorge" data-svar="false" data-name="">UKMNorge</option>';
		$options .='<option value="46415500" data-svar="true" data-name="">46415500 (UKM Norge)</option>';
		$options .='<option value="92837360" data-svar="true" data-name="">92837360 (Marius Mandal)</option>';
		$options .='<option value="47901046" data-svar="true" data-name="">47901046 (Alva Amalie)</option>';
		$options .='<option value="93883875" data-svar="true" data-name="">93883875 (Karoline Amb)</option>';
		return $options;
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
	return $options;
}

function SMS_from(){
	global $blog_id;
	
	if($blog_id == 1){
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

	if(!is_numeric($report)) {
		SMS_refund($trans_id, $recipient, $message, $res['message']);
		return false;
	}
	return true;
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

?>