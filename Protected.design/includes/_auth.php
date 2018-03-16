<?php

$home_url = "";

require_once 'braintree-php-3.28.0/lib/Braintree.php';
Braintree_Configuration::environment('');
Braintree_Configuration::merchantId('');
Braintree_Configuration::publicKey('');
Braintree_Configuration::privateKey('');

$etherscanKey = "";

$GLOBALS['tx_server'] = "";

// MySQL
$servername = "";
$username = "";
$password = "";
$dbname = "";

?>