<?php
$INFOS['season'] = get_site_option('season');
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	update_site_option('UKMmateriell_sms_forfree', $_POST['UKMmateriell_sms_forfree'] );
}
$INFOS['sms_forfree'] = get_site_option('UKMmateriell_sms_forfree');
