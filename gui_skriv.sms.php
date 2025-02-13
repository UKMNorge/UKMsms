
<div id="SMSVue">
	
</div>


<!-- TEMPORARY -->
<div style="margin-bottom: 500px;"></div>

<?php
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([], ['UKMSMS_recipients', 'UKMSMS_message'], ['GET', 'POST'], false);

$mottakere = $handleCall->getOptionalArgument('UKMSMS_recipients');
$message = $handleCall->getOptionalArgument('UKMSMS_message');


// Remove the square brackets at the start and end of the string
$mottakere = trim($mottakere, "[]");
$recipients = array();


if(strlen($mottakere) > 0) {

	// Split the string into individual recipient entries
	$entries = explode('],[', $mottakere);

	foreach ($entries as $entry) {
		// Remove any remaining square brackets
		$entry = trim($entry, '[]');
		
		// Split the entry into number and name
		list($number, $name) = explode(', ', $entry, 2);
		
		// Trim any whitespace around the number and name
		$number = trim($number);
		$name = trim($name);
		
		// Add the entry as an associative array to the recipients array
		$recipients[] = array(
			'mobil' => $number,
			'name' => $name
		);
	}
}


// Add $mottakere to client side to be used by Vue
echo '<script>';
echo 'var alleMottakere = ' . json_encode($recipients) . ';';
echo 'var smsMessage = "' . $message . '";';
echo '</script>';