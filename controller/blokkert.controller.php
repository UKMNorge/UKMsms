<?php

use UKMNorge\Database\SQL\Delete;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$sqlins = new Insert('sms_block');
	$sqlins->add('number', $_POST['number']);
	$sqlins->run();
	
	$TWIGdata['message'] = new stdClass();
	$TWIGdata['message']->success = true;
	$TWIGdata['message']->message = 'Nummeret er nå blokkert!';
}

if( isset( $_GET['delete'] ) ) {
	$delete = new Delete('sms_block', ['id' => $_GET['delete'] ] );
	$delete->run();
	
	$TWIGdata['message'] = new stdClass();
	$TWIGdata['message']->success = true;
	$TWIGdata['message']->message = 'Nummeret er ikke lenger blokkert!';
}

$sql = new Query("SELECT * FROM `sms_block` ORDER BY `id` DESC");
$res = $sql->run();

while( $row = Query::fetch( $res ) ) {
	$TWIGdata['blokkerte'][] = $row;
}