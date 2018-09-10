<?php
require_once('UKM/sql.class.php');
require_once('UKM/monstring.class.php');

$INFOS['season'] = date('Y');
	
if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	update_site_option('UKMmateriell_sms_invoice_threshold', $_POST['UKMmateriell_sms_invoice_threshold'] );
}

$INFOS['sms_invoice_threshold'] = get_site_option('UKMmateriell_sms_invoice_threshold');

// INITIATE EXCEL
	global $objPHPExcel;
	require_once('UKM/inc/excel.inc.php');
	$objPHPExcel = new PHPExcel();
	
	exorientation('portrait');
	
	$objPHPExcel->getProperties()->setCreator('UKM Norges arrangørsystem');
	$objPHPExcel->getProperties()->setLastModifiedBy('UKM Norges arrangørsystem');
	$objPHPExcel->getProperties()->setTitle('Faktura-grunnlag SMS '. date('Y') );
	$objPHPExcel->getProperties()->setKeywords('Faktura-grunnlag SMS '. date('Y') );
	
	## Sett standard-stil
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
	
	####################################################################################
	## OPPRETTER ARK
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->setActiveSheetIndex(0)->getTabColor()->setRGB('A0CF67');
	
	exSheetName('Fakturagrunnlag');
	
	// HEADERS
	$row = 1;
	exCell('A'.$row, 'Fylke');
	exCell('B'.$row, 'Mønstring');
	exCell('C'.$row, 'Kroner');

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

$sql = new SQL($qry, array('forfree' => get_site_option('UKMmateriell_sms_forfree'), 'season'=>date('Y') ) );
$res = $sql->run();

$monstringer = [];
$total = 0;
if( $res ) {
	while( $r = SQL::fetch( $res ) ) {
		$monstring = new monstring_v2( $r['pl_id'] );
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
			exCell('A'.$row, $fylke);
			exCell('B'.$row, $monstring->getNavn());
			exCell('C'.$row, $monstring->getAttr('creditsAsKroner') );
			
			$total += $monstring->getAttr('creditsAsKroner');
		}
	}
}

$INFOS['monstringer'] = $monstringer;
$excel = new StdClass;
$excel->link = exWrite($objPHPExcel,'UKM_SMSgrunnlag_'. date('Y') .'_generert_'.date('Y-m-d_His'));
$excel->created = time();

$INFOS['total'] = $total;
$INFOS['excel'] = $excel;
$INFOS['forfree'] = get_site_option('UKMmateriell_sms_forfree');