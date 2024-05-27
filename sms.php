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
    if( get_option('pl_id') ) {
        add_action('admin_menu', 'UKMSMS_menu', 315);
    }
    add_action('wp_ajax_UKMSMS_ajax', 'UKMSMS_ajax');
    
    require_once('functions.sms.php');
	require_once('ajax.sms.php');
	require_once('UKM/sms.class.php');
}
## ADMIN MENU
function UKMSMS_menu() {
	$page = add_menu_page(
		'SMS',
		'SMS',
		'ukm_sms',
		'UKMSMS_gui',
		'UKMSMS_gui',
		'dashicons-smartphone',#'//ico.ukm.no/mobile-menu.png',
		100
	);
	add_action(
		'admin_print_styles-' . $page,
		'UKMSMS_sns'
	);
} 

function UKMSMS_menu_network() {
	$page = add_menu_page(
		'SMS',
		'SMS',
		'editor',
		'UKMSMS_gui',
		'UKMSMS_gui',
		'dashicons-smartphone',#'//ico.ukm.no/mobile-menu.png',
		400
	);

	$subpage6 = add_submenu_page( 'UKMSMS_gui', 'Blokkere', 'Blokkere mottakere', 'superadministrator', 'UKMsmsblokkert', 'UKMsmsblokkert' );
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

	wp_enqueue_style( 'UKMSMS_style', PLUGIN_PATH .'UKMsms/style.sms.css');
	wp_enqueue_script( 'UKMSMS_script', PLUGIN_PATH .'UKMsms/script.sms.js?v=1');
	wp_enqueue_script( 'UKMSMS_textareacounter', PLUGIN_PATH .'UKMsms/textareacounter.sms.js');

	wp_enqueue_style('UKMSMSVueMIDIcons', 'https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');
	wp_enqueue_style('UKMSMSVueStyle', plugin_dir_url(__FILE__) . '/client/dist/assets/build.css');
	wp_enqueue_script('UKMSMSVueJs', plugin_dir_url(__FILE__) . '/client/dist/assets/build.js','','',true);
}

function UKMSMS_gui(){
	if(isset($_POST['SMS_send']))
		require_once('gui_send.sms.php');
	else 
		require_once('gui_skriv.sms.php');	
}

function UKMsmsblokkert() {
	$TWIGdata = [];
	require_once('controller/blokkert.controller.php');
	echo TWIG('blokkert.html.twig', $TWIGdata , dirname(__FILE__));
}
?>