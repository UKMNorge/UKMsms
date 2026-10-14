<?php

use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;

require_once('UKM/Autoloader.php');

$sessong = date('Y');
$INFOS['season'] = $sessong;

$gruppert = grupperSmsForbruk(hentSmsForbruk($sessong));
$INFOS['fylker'] = $gruppert['fylker'];
$INFOS['total'] = $gruppert['total'];

/**
 * Alle arrangement med SMS-forbruk i sesongen.
 * Kreditter lagres negativt ved sending; abs() gir forbruket, som i SMS_usage().
 *
 * @return Array
 */
function hentSmsForbruk($season) : array {
	$qry = new Query("SELECT
		`pl`.`pl_id`,
		`pl`.`pl_name`,
		`pl`.`pl_owner_fylke`,
		`pl`.`pl_owner_kommune`,
		SUM(`t_credits`) AS `credits`
		FROM `log_sms_transactions` AS `t`
		JOIN `smartukm_place` AS `pl` ON ( `pl`.`pl_id` = `t`.`pl_id`)
		WHERE `t_action` = 'sendte_sms_for'
		AND `season` = '#season'
		AND `t_system` = 'wordpress'
		GROUP BY `pl`.`pl_id`, `pl`.`pl_name`, `pl`.`pl_owner_fylke`, `pl`.`pl_owner_kommune`
		ORDER BY `pl`.`pl_name` ASC, `pl`.`pl_id` ASC",
		[
			'season' => $season,
		]
	);

	$res = $qry->run();
	$rader = [];
	if (!$res) {
		return $rader;
	}

	while ($r = Query::fetch($res)) {
		$credits = (int) abs((int) $r['credits']);
		if ($credits === 0) {
			continue;
		}
		$rader[] = [
			'pl_id' => (int) $r['pl_id'],
			'pl_name' => (string) $r['pl_name'],
			'pl_owner_fylke' => (int) $r['pl_owner_fylke'],
			'pl_owner_kommune' => (int) $r['pl_owner_kommune'],
			'credits' => $credits,
		];
	}

	return $rader;
}

/**
 * Fylke, deretter kommunene i fylket, deretter arrangement.
 * Rekkefølgen på fylke og kommune følger Fylker::getAll() og getKommuner().
 * Arrangement uten kommune ligger direkte på fylket.
 *
 * @param Array $rader
 * @return Array
 */
function grupperSmsForbruk(array $rader) : array {
	$perKommune = [];
	$perFylke = [];

	foreach ($rader as $rad) {
		if ($rad['pl_owner_kommune'] > 0) {
			$perKommune[$rad['pl_owner_kommune']][] = $rad;
		} else {
			$perFylke[$rad['pl_owner_fylke']][] = $rad;
		}
	}

	$fylker = [];
	$kjenteFylkeIder = [];

	foreach (Fylker::getAll() as $fylke) {
		$fylkeId = (int) $fylke->getId();
		$kjenteFylkeIder[$fylkeId] = true;
		$kommuner = [];

		foreach ($fylke->getKommuner() as $kommune) {
			$kommuneId = (int) $kommune->getId();
			if (empty($perKommune[$kommuneId])) {
				continue;
			}
			$kommuner[] = lagKommuneRad($kommuneId, $kommune->getNavn(), $perKommune[$kommuneId]);
			unset($perKommune[$kommuneId]);
		}

		$arrangementer = [];
		if (!empty($perFylke[$fylkeId])) {
			$arrangementer = $perFylke[$fylkeId];
			unset($perFylke[$fylkeId]);
		}

		$fylker[$fylkeId] = lagFylkeRad($fylkeId, $fylke->getNavn(), $arrangementer, $kommuner);
	}

	foreach ($perKommune as $kommuneId => $arrangementer) {
		$plassering = finnKommunePlassering((int) $kommuneId);
		$fylkeId = $plassering['fylkeId'];
		if (!isset($fylker[$fylkeId])) {
			$fylker[$fylkeId] = lagFylkeRad($fylkeId, $plassering['fylkeNavn'], [], []);
		}
		$fylker[$fylkeId]['kommuner'][] = lagKommuneRad(
			(int) $kommuneId,
			$plassering['kommuneNavn'],
			$arrangementer
		);
	}

	foreach ($perFylke as $fylkeId => $arrangementer) {
		$plassering = finnFylkePlassering((int) $fylkeId);
		$id = $plassering['fylkeId'];
		if (!isset($fylker[$id])) {
			$fylker[$id] = lagFylkeRad($id, $plassering['fylkeNavn'], [], []);
		}
		$fylker[$id]['arrangementer'] = array_merge($fylker[$id]['arrangementer'], $arrangementer);
	}

	$liste = [];
	$ekstra = [];
	$total = 0;

	foreach ($fylker as $fylkeId => $fylke) {
		$fylke = oppdaterFylkeTotal($fylke);
		if ($fylke['total'] === 0) {
			continue;
		}
		$total += $fylke['total'];
		if (isset($kjenteFylkeIder[$fylkeId])) {
			$liste[] = $fylke;
		} else {
			$ekstra[] = $fylke;
		}
	}

	usort($ekstra, function ($a, $b) {
		if ((int) $a['id'] === 0) {
			return 1;
		}
		if ((int) $b['id'] === 0) {
			return -1;
		}
		return strcasecmp($a['name'], $b['name']);
	});

	return [
		'fylker' => array_merge($liste, $ekstra),
		'total' => $total,
	];
}

/**
 * @param Int $kommuneId
 * @return Array
 */
function finnKommunePlassering($kommuneId) : array {
	$ukjent = [
		'fylkeId' => 0,
		'fylkeNavn' => 'Ukjent',
		'kommuneNavn' => 'Ukjent',
	];

	try {
		$kommune = new Kommune((int) $kommuneId);
		if (!$kommune->getId()) {
			return $ukjent;
		}
		$fylke = $kommune->getFylke();
		if (!$fylke) {
			$ukjent['kommuneNavn'] = $kommune->getNavn();
			return $ukjent;
		}
		return [
			'fylkeId' => (int) $fylke->getId(),
			'fylkeNavn' => $fylke->getNavn(),
			'kommuneNavn' => $kommune->getNavn(),
		];
	} catch (Exception $e) {
		return $ukjent;
	}
}

/**
 * @param Int $fylkeId
 * @return Array
 */
function finnFylkePlassering($fylkeId) : array {
	if ((int) $fylkeId <= 0) {
		return [
			'fylkeId' => 0,
			'fylkeNavn' => 'Ukjent',
		];
	}

	try {
		$fylke = Fylker::getById((int) $fylkeId);
		if (!$fylke) {
			return [
				'fylkeId' => 0,
				'fylkeNavn' => 'Ukjent',
			];
		}
		return [
			'fylkeId' => (int) $fylke->getId(),
			'fylkeNavn' => $fylke->getNavn(),
		];
	} catch (Exception $e) {
		return [
			'fylkeId' => 0,
			'fylkeNavn' => 'Ukjent',
		];
	}
}

/**
 * @param Int $id
 * @param String $navn
 * @param Array $arrangementer
 * @return Array
 */
function lagKommuneRad($id, $navn, array $arrangementer) : array {
	return [
		'id' => (int) $id,
		'name' => $navn,
		'total' => summerCredits($arrangementer),
		'arrangementer' => $arrangementer,
	];
}

/**
 * @param Int $id
 * @param String $navn
 * @param Array $arrangementer
 * @param Array $kommuner
 * @return Array
 */
function lagFylkeRad($id, $navn, array $arrangementer, array $kommuner) : array {
	return oppdaterFylkeTotal([
		'id' => (int) $id,
		'name' => $navn,
		'arrangementer' => $arrangementer,
		'kommuner' => $kommuner,
	]);
}

/**
 * @param Array $fylke
 * @return Array
 */
function oppdaterFylkeTotal(array $fylke) : array {
	$total = summerCredits($fylke['arrangementer']);
	foreach ($fylke['kommuner'] as $kommune) {
		$total += (int) $kommune['total'];
	}
	$fylke['total'] = $total;
	return $fylke;
}

/**
 * @param Array $arrangementer
 * @return Int
 */
function summerCredits(array $arrangementer) : int {
	$sum = 0;
	foreach ($arrangementer as $arrangement) {
		$sum += (int) $arrangement['credits'];
	}
	return $sum;
}
