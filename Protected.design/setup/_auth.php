<?php


$GLOBALS['etherscanKey'] = "";
$GLOBALS['linux_auth_token'] = "";
$GLOBALS['tx_auth_token'] = "";
$GLOBALS['admin_email'] = "";
$GLOBALS['link_end'] = "";

$GLOBALS['home_url'] = "https://protected.design/";
$GLOBALS['pd_theme_url'] = "WP-THEME-SERVER-DIRECTORY";
$GLOBALS['pd_home'] = "HOME-SERVER-DIRECTORY";



// Dev
$GLOBALS['tx_server'] = "";
$GLOBALS['tx_server_grouped'] = "";
$GLOBALS['etherscan_api'] = "";

$GLOBALS['consumerKey'] = "";
$GLOBALS['consumerSecret'] = "";


// Live
// $GLOBALS['tx_server'] = "";
// $GLOBALS['tx_server_grouped'] = "";
// $GLOBALS['etherscan_api'] = "";

// $GLOBALS['consumerKey'] = "";
// $GLOBALS['consumerSecret'] = "";



// MySQL Live
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";


// MySQL Dev
// $servername = "localhost";
// $username = "";
// $password = "";
// $dbname = "";



function load_global_wpdb() {
    define( 'SHORTINIT', true );
    require( $GLOBALS['pd_home'] . 'wp-load.php' );
}

?>