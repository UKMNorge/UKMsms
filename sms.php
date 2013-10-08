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
	$page = add_menu_page(__('SMS'), __('SMS'), 'editor', 'UKMSMS_gui', 'UKMSMS_gui', 'http://ico.ukm.no/mobile-menu.png',315);
	add_action( 'admin_print_styles-' . $page, 'UKMSMS_sns' );
} 

function UKMSMS_sns(){
	wp_enqueue_style( 'UKMSMS_style', plugin_dir_url( __FILE__ ) .'style.sms.css');
	wp_enqueue_script( 'UKMSMS_script', plugin_dir_url( __FILE__ ) .'script.sms.js');
	wp_enqueue_script( 'UKMSMS_textareacounter', plugin_dir_url( __FILE__ ) .'textareacounter.sms.js');

	
}

function UKMSMS_gui(){
	if(isset($_POST['SMS_send']))
		require_once('gui_send.sms.php');
	else
		require_once('gui_skriv.sms.php');	
}
?>