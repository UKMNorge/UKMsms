<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
use UKMNorge\File\Excel;


require_once('UKM/Autoloader.php');

$INFOS['season'] = date('Y');
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	update_site_option('UKMmateriell_sms_invoice_threshold', $_POST['UKMmateriell_sms_invoice_threshold'] );
}

$INFOS['sms_invoice_threshold'] = get_site_option('UKMmateriell_sms_invoice_threshold');

// INITIATE EXCEL
	global $objPHPExcel;

	$filNavn = 'Faktura-grunnlag SMS '. date('Y');
	$objPHPExcel = new Excel($filNavn);
	
	// exorientation('portrait');
	
	$objPHPExcel->phpSpreadsheet->getProperties()->setCreator('UKM Norges arrangørsystem');
	$objPHPExcel->phpSpreadsheet->getProperties()->setLastModifiedBy('UKM Norges arrangørsystem');
	$objPHPExcel->phpSpreadsheet->getProperties()->setTitle('Faktura-grunnlag SMS '. date('Y') );
	$objPHPExcel->phpSpreadsheet->getProperties()->setKeywords('Faktura-grunnlag SMS '. date('Y') );
	
	## Sett standard-stil
	$objPHPExcel->phpSpreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
	$objPHPExcel->phpSpreadsheet->getDefaultStyle()->getFont()->setSize(12);
	
	####################################################################################
	## OPPRETTER ARK
	$objPHPExcel->phpSpreadsheet->setActiveSheetIndex(0);
	$objPHPExcel->phpSpreadsheet->setActiveSheetIndex(0)->getTabColor()->setRGB('A0CF67');
	
	// exSheetName('Fakturagrunnlag');
	$objPHPExcel->$sheet_names[] = 'Fakturagrunnlag';
	// HEADERS
	$row = 1;

	$objPHPExcel->celle('A'.$row, 'Fylke');
	$objPHPExcel->celle('B'.$row, 'Mønstring');
	$objPHPExcel->celle('C'.$row, 'Kroner');

	// die();

// SELECT ALL
$qry = "SELECT
			`t`.`t_system`,
			`t`.`wp_username`,
			`t`.`t_action`,
			`pl`.`pl_id`,
		#forfree + SUM(`t_credits`) AS `credits`
		FROM `log_sms_transactions` AS `t`
		JOIN `smartukm_place` AS `pl` ON ( `pl`.`pl_id` = `t`.`pl_id`)
		WHERE `t_action` = 'sendte_sms_for'
		AND `season` = '#season'
		AND `t_system` = 'wordpress'
		GROUP BY `pl`.`pl_id`
		ORDER BY `credits` ASC";

$sql = new Query($qry, array('forfree' => get_site_option('UKMmateriell_sms_forfree'), 'season'=>date('Y') ) );
$res = $sql->run();

$monstringer = [];
$total = 0;
if( $res ) {
	while( $r = Query::fetch( $res ) ) {
		$monstring = new Arrangement(intval(( $r['pl_id'] )));
		$monstring->setAttr('credits', $r['credits'] );
		$monstring->setAttr('creditsAsKroner', $r['credits']*0.4 );
		
		if( $monstring->getAttr('creditsAsKroner') < -1*$INFOS['sms_invoice_threshold'] ) {
			if( 'kommune' == $monstring->getType() ) {
				try {
					$fylke = $monstring->getFylke()->getNavn();
					$monstringName =  $monstring->getFylke() .' - '. $monstring->getNavn();
				} catch (Exception $e ) {
					$fylke = 'Ukjent';
					$monstringName = 'UKJENT fylke - '. $monstring->getNavn();
				}
			} else {
				$fylke = $monstring->getNavn();
				$monstringName = $monstring->getNavn() .' fylkesmønstring';
			}			
			$monstring->setAttr('invoiceName', $monstringName );

			$monstringer[] = $monstring;
			
			$row++;
			$excel->cell('A'.$row, $fylke);
			$excel->cell('B'.$row, $monstring->getNavn());
			$excel->cell('C'.$row, $monstring->getAttr('creditsAsKroner') );
			
			$total += $monstring->getAttr('creditsAsKroner');
		}
	}
}

$INFOS['monstringer'] = $monstringer;
$excel = new StdClass;
$excel->link = $objPHPExcel->writeToFile();
$excel->created = time();

$INFOS['total'] = $total;
$INFOS['excel'] = $excel;
$INFOS['forfree'] = get_site_option('UKMmateriell_sms_forfree');