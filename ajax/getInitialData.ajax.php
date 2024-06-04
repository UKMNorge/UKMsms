<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\SearchArrangorsystemet\SearchContentIndex;
use UKMNorge\SearchArrangorsystemet\Search;
use UKMNorge\Arrangement\Arrangement;

global $current_user;

require_once('UKM/Autoloader.php');

die('aaa');
$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);


$handleCall->sendToClient([
    'results' => [],
]);