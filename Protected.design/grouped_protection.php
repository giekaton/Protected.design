<?php

require_once 'setup/_auth.php';

load_global_wpdb();



$grouped_pd = $wpdb->get_results(
    "
    SELECT * 
    FROM protected_designs
    WHERE status = 'Scheduled'
    "
);



$wpdb->update(
    'protected_designs',
    array( 'status' => 'Pending grouped protection' ),
    array( 'status' => 'Scheduled' )
);



$i = 0;
foreach ($grouped_pd as $pd) {
    if ($i > 0) {
        $grouped_pd_list .= "\n";
    }
    $grouped_pd_list .= $pd->hash;
    $grouped_pd_list .= $pd->message;
    $i++;
}



$file = $GLOBALS['pd_home'] . 'files/temp.txt';
file_put_contents($file, $grouped_pd_list);



if($i > 0) {
    $sha256_hash = hash_file('sha256', $file);
    copy($file, $GLOBALS['pd_home'] . 'files/' . $sha256_hash . '.txt');

    $ch = curl_init($GLOBALS['tx_server_grouped'] . "?hash=" . $sha256_hash . "&auth=" . $GLOBALS['linux_auth_token']);
    curl_exec($ch);

    if(curl_error($ch))
    {
        // @todo
    }
}



if (!empty($_GET['tx_auth_token'])) {
    $tx_auth_token = $_GET['tx_auth_token'];

    if ($tx_auth_token == $GLOBALS['tx_auth_token']) {
        $tx_hex = $_GET['tx_hex'];
        $hash = $_GET['hash'];

        $wpdb->update('protected_designs', array('tx_hex' => $tx_hex), array('status' => 'Pending grouped protection'));

        $ch = curl_init(); 
        // set url 
        curl_setopt($ch, CURLOPT_URL, $GLOBALS['etherscan_api'] . "/api?module=proxy&action=eth_sendRawTransaction&hex=" . $tx_hex . "&apikey=" . $GLOBALS['etherscanKey']); 
        // return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // $output contains the output string 
        $output = curl_exec($ch); 
        // close curl resource to free up system resources 
        curl_close($ch);
        
        $outputdecoded = json_decode($output, true);
        $output2 = serialize($outputdecoded);
        
        $tx_hash = $outputdecoded["result"];

        // If transaction was not broadcasted, then submit transaction again, until there is no error
        if ($outputdecoded["error"]) {

            // @todo: error handling
            $wpdb->update('protected_designs', array('status' => 'Error', 'apiresult' => $output2), array('status' => 'Pending grouped protection'));
            
        }
        else {
            $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'grouped_hash' => $hash, 'status' => 'Protected', 'apiresult' => $output2, 'tx_timestamp_hex' => 'Pending'), array('status' => 'Pending grouped protection'));
            
            $ch = curl_init($GLOBALS['pd_theme_url'] . 'check_tx_timestamp_grouped' . $GLOBALS['link_end'] . '?tx_hash='.$tx_hash);
            curl_exec($ch);
            curl_close($ch);
        }
    }
}



?>