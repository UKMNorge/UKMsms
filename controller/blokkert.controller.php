<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$sqlins = new SQLins('sms_block');
	$sqlins->add('number', $_POST['number']);
	$sqlins->run();
	
	$TWIGdata['message'] = new stdClass();
	$TWIGdata['message']->success = true;
	$TWIGdata['message']->message = 'Nummeret er nå blokkert!';
}

$sql = new SQL("SELECT * FROM `sms_block` ORDER BY `id` DESC");
$res = $sql->run();

while( $row = mysql_fetch_assoc( $res ) ) {
	$TWIGdata['blokkerte'][] = $row;
}