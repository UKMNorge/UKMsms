<?php
/* 
Plugin Name: UKM SMS
Plugin URI: http://www.ukm.no
Description: SMS-system for UKM-arrangører
Author: UKM Norge / M Mandal 
Version: 5.0 
Author URI: http://www.ukm-norge.no
*/

add_action('network_admin_menu', 'UKMSMS_menu_network');

if(is_admin()){
	require_once('functions.sms.php');
	require_once('hooks.sms.php');
	require_once('ajax.sms.php');
	require_once('UKM/sms.class.php');
}
## ADMIN MENU
function UKMSMS_menu() {
	global $menu, $blog_id;
	UKM_add_menu_page('kommunikasjon', 'SMS', 'SMS', 'ukm_sms', 'UKMSMS_gui', 'UKMSMS_gui', '//ico.ukm.no/mobile-menu.png',10);
	UKM_add_scripts_and_styles('UKMSMS_gui', 'UKMSMS_sns' );
} 

function UKMSMS_menu_network() {
	$page = add_menu_page('SMS', 'SMS', 'editor', 'UKMSMS_gui', 'UKMSMS_gui', '//ico.ukm.no/mobile-menu.png',400);

	$subpage6 = add_submenu_page( 'UKMSMS_gui', 'Fakturering', 'Fakturering', 'superadministrator', 'UKMMsmsfaktura', 'UKMMsmsfaktura' );
	$subpage7 = add_submenu_page( 'UKMSMS_gui', 'Innstillinger', 'Innstillinger', 'superadministrator', 'UKMMsmsinnstillinger', 'UKMMsmsinnstillinger' );

	add_action( 'admin_print_styles-' . $page, 'UKMSMS_sns' );

	add_action( 'admin_print_styles-' . $subpage6, 'UKMSMS_sns' );
	add_action( 'admin_print_styles-' . $subpage7, 'UKMSMS_sns' );
}

function UKMMsmsfaktura() {
	require_once('controller/faktura.controller.php');
	echo TWIG('faktura.html.twig', $INFOS , dirname(__FILE__));
} 

function UKMMsmsinnstillinger() {
	require_once('controller/innstillinger.controller.php');
	echo TWIG('innstillinger.html.twig', $INFOS , dirname(__FILE__));
} 

function UKMSMS_sns(){
	wp_enqueue_script('WPbootstrap3_js');
	wp_enqueue_style('WPbootstrap3_css');

	wp_enqueue_style( 'UKMSMS_style', plugin_dir_url( __FILE__ ) .'style.sms.css');
	wp_enqueue_script( 'UKMSMS_script', plugin_dir_url( __FILE__ ) .'script.sms.js?v=1');
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