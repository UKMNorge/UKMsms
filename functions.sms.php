<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');

function SMS_credits(){
	if( is_network_admin() ) {
		return 100000;
	}
	
	$plid = get_option('pl_id');
	SMS_init($plid, SMS_wpsender());
	
	$qry = new Query("SELECT SUM(`t_credits`) AS `credits`
					FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					AND `t_system` != 'pamelding'
					AND `t_system` != 'pameldingUKMvalidate'",
					array('plid'=>$plid));
	return (int) $qry->run('field','credits');
}

function SMS_usage(){
	$plid = get_option('pl_id');

	$arrangement = new Arrangement($plid);
	$season = $arrangement->getSesong();
	$fylkeId = null;
	$kommuneId = null;
	$where = '';
	if($arrangement->getType() == 'fylke') {
		$fylkeId = $arrangement->getFylke()->getId();
	}
	elseif($arrangement->getType() == 'kommune') {
		$kommuneId = $arrangement->getKommune()->getId();
	}
	if($fylkeId) {
		$where .= " AND `pl_owner_fylke` = '#fylkeId'";
	}
	if($kommuneId) {
		$where .= " AND `pl_owner_kommune` = '#kommuneId'";
	}
	
	$qry = new Query("SELECT
		`pl`.`pl_id`,
		`pl`.`pl_name`,
		`pl`.`pl_owner_fylke`,
		`pl`.`pl_owner_kommune`,
		`f`.`name` AS `fylke_name`,
		`k`.`name` AS `kommune_name`,
		SUM(`t_credits`) AS `credits`
		FROM `log_sms_transactions` AS `t`
		JOIN `smartukm_place` AS `pl` ON ( `pl`.`pl_id` = `t`.`pl_id`)
		LEFT JOIN `smartukm_fylke` AS `f` ON ( `f`.`id` = `pl`.`pl_owner_fylke`)
		LEFT JOIN `smartukm_kommune` AS `k` ON ( `k`.`id` = `pl`.`pl_owner_kommune`)
		WHERE `t_action` = 'sendte_sms_for'
		AND `season` = '#season'
		AND `t_system` = 'wordpress'
		$where
		GROUP BY `pl`.`pl_id`
		ORDER BY `credits` ASC",
		[
			'season' => $season,
			'fylkeId' => $fylkeId,
			'kommuneId' => $kommuneId,
		]
	);

	$res = $qry->run();
	$usage = [];
	while($r = Query::fetch($res)) {
		$usage[] = [
			'pl_id' => $r['pl_id'],
			'pl_name' => $r['pl_name'],
			'fylke_name' => $r['fylke_name'],
			'kommune_name' => $r['kommune_name'],
			'credits' => abs($r['credits']),
		];
	}
	return $usage;
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
	$qry = new Query("SELECT * FROM `log_sms_transactions`
					WHERE `pl_id` = '#plid'
					AND `t_action` = 'mottok'",
					array('plid'=>$plid));
	$res = $qry->run();
	if( Query::numRows( $res ) == 0 ){
		$ins = new Insert('log_sms_transactions');
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
	$norge_options .='<option value="93091329" data-svar="true" data-name="UKM Norge">93091329 (UKM Norge support)</option>';
	$norge_options .='<option value="92837360" data-svar="true" data-name="Marius">92837360 (Marius Mandal)</option>';
	$norge_options .='<option value="90755685" data-svar="true" data-name="Torstein">90755685 (Torstein Siegel)</option>';
	$norge_options .='<option value="93665540" data-svar="true" data-name="Jardar">93665540 (Jardar Nordbø)</option>';
	$norge_options .='<option value="92688810" data-svar="true" data-name="Stine">41123976 (Stine Granly)</option>';

	if($blog_id == 1 || is_network_admin() ){
		return $norge_options;
	}


	$arrangement = new Arrangement(intval(get_option('pl_id')));
	if( $arrangement->getEierType() == 'fylke' ) {
		$options = '<option value="UKMfylke" data-svar="false" data-name="">UKMfylke</option>';
    } else {
        $options = '<option value="UKMlokal" data-svar="false" data-name="">UKMlokal</option>'; 
    }
	foreach($arrangement->getKontaktpersoner()->getAll() as $kontakt ){
        if( empty( $kontakt->getTelefon() ) ) {
            continue;
        }
		$options .= '<option value="'.$kontakt->getTelefon().'" data-svar="true" data-name="'.$kontakt->getFornavn().'">'.$kontakt->getTelefon().' ('.$kontakt->getNavn().')</option>';
	}	
	
	if( is_super_admin() ) {
		return $options . $norge_options;
	}
	return $options;
}

function SMS_avsendere_array(){
	$avsendere = array();
	$avsendere['UKMNorge'] = 'UKMNorge';
	// $avsendere['UKMMedia'] = 'UKMMedia';
	// $avsendere['93091329'] = 'UKM Norge support';
	// $avsendere['46516256'] = 'Kushtrim Aliu';
	// $avsendere['90755685'] = 'Torstein Siegel';
	// $avsendere['93665540'] = 'Jardar Nordbø';

	if(!intval(get_option('pl_id'))) {
		return $avsendere;
	}
	$arrangement = new Arrangement(intval(get_option('pl_id')));
	if( $arrangement->getEierType() == 'fylke' ) {
		$avsendere['UKMfylke'] = 'UKMfylke';
    } else {
		$avsendere['UKMlokal'] = 'UKMlokal';
    }
	
	foreach($arrangement->getKontaktpersoner()->getAll() as $kontakt ){
        if( empty( $kontakt->getTelefon() ) ) {
            continue;
        }
		$avsendere[$kontakt->getTelefon()] = $kontakt->getNavn();
	}	
	
	return $avsendere;
}


function SMS_from(){
	global $blog_id;
	
	if($blog_id == 1 || is_network_admin() ){
		return '- UKM';
	}
	$arrangement = new Arrangement(intval(get_option('pl_id')));
	
	$from = '- UKM '. $arrangement->getNavn();
	
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