<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$sqlins = new SQLins('sms_block');
	$sqlins->add('number', $_POST['number']);
	$sqlins->run();
	
	$TWIGdata['message'] = new stdClass();
	$TWIGdata['message']->success = true;
	$TWIGdata['message']->message = 'Nummeret er nå blokkert!';
}

if( isset( $_GET['delete'] ) ) {
	$sqldel = new SQLdel('sms_block', ['id' => $_GET['delete'] ] );
	$sqldel->run();
	
	$TWIGdata['message'] = new stdClass();
	$TWIGdata['message']->success = true;
	$TWIGdata['message']->message = 'Nummeret er ikke lenger blokkert!';
}

$sql = new SQL("SELECT * FROM `sms_block` ORDER BY `id` DESC");
$res = $sql->run();

while( $row = mysql_fetch_assoc( $res ) ) {
	$TWIGdata['blokkerte'][] = $row;
}