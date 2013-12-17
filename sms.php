<?php
/* 
Plugin Name: UKM SMS
Plugin URI: http://www.ukm.no
Description: SMS-system for UKM-arrangører
Author: UKM Norge / M Mandal 
Version: 5.0 
Author URI: http://www.ukm-norge.no
*/

if(is_admin()){
	require_once('functions.sms.php');
	require_once('hooks.sms.php');
	require_once('ajax.sms.php');
	require_once('UKM/sms.class.php');
}
## ADMIN MENU
function UKMSMS_menu() {
	global $menu, $blog_id;
	UKM_add_menu_page('kommunikasjon', __('SMS'), __('SMS'), 'editor', 'UKMSMS_gui', 'UKMSMS_gui', 'http://ico.ukm.no/mobile-menu.png',10);
	UKM_add_scripts_and_styles('UKMSMS_gui', 'UKMSMS_sns' );
} 

function UKMSMS_sns(){
	wp_enqueue_style( 'UKMSMS_style', plugin_dir_url( __FILE__ ) .'style.sms.css');
	wp_enqueue_script( 'UKMSMS_script', plugin_dir_url( __FILE__ ) .'script.sms.js');
	wp_enqueue_script( 'UKMSMS_textareacounter', plugin_dir_url( __FILE__ ) .'textareacounter.sms.js');
}

function UKMSMS_gui(){
	require_once('UKM/sql.class.php');
	if(isset($_POST['SMS_send']))
		require_once('gui_send.sms.php');
	else 
		require_once('gui_skriv.sms.php');	
}
?>