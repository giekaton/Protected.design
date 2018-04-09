<?php



$home_url = "";



$GLOBALS['etherscanKey'] = "";
$GLOBALS['linux_auth_token'] = "";
$GLOBALS['tx_auth_token'] = "";
$GLOBALS['admin_email'] = "";



// Dev
$GLOBALS['tx_server'] = "";
$GLOBALS['etherscan_api'] = "";

require_once 'braintree-php-3.28.0/lib/Braintree.php';
Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('');
Braintree_Configuration::publicKey('');
Braintree_Configuration::privateKey('');



// // Live
// $GLOBALS['tx_server'] = "";
// $GLOBALS['etherscan_api'] = "";

// require_once 'braintree-php-3.28.0/lib/Braintree.php';
// Braintree_Configuration::environment('production');
// Braintree_Configuration::merchantId('');
// Braintree_Configuration::publicKey('');
// Braintree_Configuration::privateKey('');


// MySQL
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";


function load_global_wpdb() {
    define( 'SHORTINIT', true );
    require( '/public_html/wp-load.php' ); // Path to your wp-load.php
}

?>