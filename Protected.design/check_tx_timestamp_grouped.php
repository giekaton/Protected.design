<?php

require_once 'setup/_auth.php';

load_global_wpdb();

$tx_hash = $_GET['tx_hash'];

if(empty($tx_hash)) {
    exit();
}

$i = 0;
get_timestamp($tx_hash, $i);

function get_timestamp($tx_hash, $i) {
    sleep(15);
    global $wpdb;

    // Get block nr
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['etherscan_api'] . "/api?module=proxy&action=eth_getTransactionByHash&txhash=" . $tx_hash . "&apikey=" . $GLOBALS['etherscanKey']); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch);
    $outputdecoded = json_decode($output, true);
    $output2 = serialize($outputdecoded);
    $tx_block = $outputdecoded[result][blockNumber];
    
    if (!empty($tx_block)) {
        $wpdb->update('protected_designs', array('tx_block' => $tx_block), array('tx_timestamp_hex' => 'Pending'));

        sleep(1);
        // Get tx timestamp
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $GLOBALS['etherscan_api'] . "/api?module=proxy&action=eth_getBlockByNumber&tag=" . $tx_block . "&boolean=true&apikey" . $GLOBALS['etherscanKey']); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);
        $outputdecoded = json_decode($output, true);
        $output2 = serialize($outputdecoded);
        $tx_timestamp_hex = $outputdecoded[result][timestamp];
        $tx_timestamp = hexdec($tx_timestamp_hex);
        $tx_timestamp = (gmdate("d M Y H:i:s", $tx_timestamp). " GMT");
        $wpdb->update('protected_designs', array('tx_timestamp' => $tx_timestamp, 'tx_timestamp_hex' => $tx_timestamp_hex), array('tx_timestamp_hex' => 'Pending'));
    }
    else {
        $i++;
        echo($i . " ");
        if ($i < 20) {
            get_timestamp($tx_hash, $i);
        }
        else {
            $wpdb->update('protected_designs', array('tx_timestamp' => 'Error'), array('tx_timestamp_hex' => 'Pending'));
        }
    }
}

?>