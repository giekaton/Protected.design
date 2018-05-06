<?php

require_once 'setup/_auth.php';

load_global_wpdb();
global $wpdb;

sleep(7);

$old_value = $wpdb->get_var("SELECT donations FROM pd_settings WHERE ID = 1");

// Get ETH balance
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $GLOBALS['etherscan_api'] . "/api?module=account&action=balance&address=0xA35007cf66090F4AB11b5C48F64f9122bF6B3FE0&apikey=" . $GLOBALS['etherscanKey']); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);
$outputdecoded = json_decode($output, true);
$new_value = $outputdecoded[result];

$timestamp = date("Y-m-d H:i:s");
$wpdb->update('pd_settings', array('donations' => $new_value, 'timestamp' => $timestamp), array('ID' => '1'));

$difference = $new_value - $old_value;

if ($difference > 1000000000000000) {
    $ch = curl_init($GLOBALS['pd_theme_url'] . 'grouped_protection' . $GLOBALS['link_end']);
    curl_exec($ch);
    curl_close($ch);
}

?>