<?php

require_once 'includes/braintree-php-3.28.0/lib/Braintree.php';

// Define myslq credentials, tx server host, Etherscan API and home url
require_once 'includes/_auth.php';

global $wpdb;


function new_protected_design( $data ) {
    global $wpdb;
    $timestamp = date("Y-m-d H:i:s");
    $status = "Waiting for payment";
    $paid = 0;
    $hash = $data['hash'];
    $shortlink = substr($hash, 0, 12);
    $file_size = $data['file_size'];
    
    $sql = "INSERT IGNORE INTO protected_designs (timestamp, status, paid, shortlink, hash, file_size) VALUES ('$timestamp', '$status', '$paid', '$shortlink', '$hash', '$file_size')";
    $wpdb->query($sql);

}


function get_protected_design( $data ) {
    global $wpdb;
    
    $shortlink = $data['shortlink'];
    
    // /protected.design/wp-json/get_protected_design/get?shortlink=17a689cb55ef

    return $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );

}


function set_message( $data ) {
    global $wpdb;
    
    $message = $data['message'];
    $hash = $data['hash'];

    return $wpdb->get_row( "UPDATE protected_designs SET message = '$message' WHERE hash = '$hash' AND status = 'Waiting for payment' OR message = ''" );

}


function braintree( $data ) {
    $nonceFromTheClient = $data['nonce'];
    $hash = $data['hash'];

    $result = Braintree_Transaction::sale([
        'amount' => '5.00',
        'paymentMethodNonce' => $nonceFromTheClient,
        'options' => [
          'submitForSettlement' => True
        ]
      ]);

      if ($result->success) {
        global $wpdb;
        $wpdb->update('protected_designs', array('status' => 'Pending', 'paymentresult' => $result), array('hash' => $hash));
      } else {
        echo($result);
      }

      return($result);
}


function submit_tx( $data ) {

    $hash = $data['hash'];

    echo($GLOBALS['tx_server'] . "?hash=" . $hash);

    $ch = curl_init($GLOBALS['tx_server'] . "?hash=" . $hash);
    curl_exec($ch);

}


function broadcast_tx( $data ) {
    global $wpdb;

    $tx_auth_token = $data['tx_auth_token'];

    if ($tx_auth_token == $GLOBALS['tx_auth_token']) {
        $tx_hex = $data['tx_hex'];
        $hash_trunc = $data['hash'];
        // Remove message hex from the end of truncated hash
        $hash_trunc = substr($hash_trunc, 0, 40);
        // Create a shortlink from truncated hash
        $shortlink = substr($hash_trunc, 0, 12);

        $wpdb->update('protected_designs', array('tx_hex' => $tx_hex), array('shortlink' => $shortlink));
        
        // echo("https://ropsten.etherscan.io/api?module=proxy&action=eth_sendRawTransaction&hex=" . $tx_hex . "&apikey=" . $etherscanKey);

        // broadcast tx using ethereum api
        // https://api.etherscan.io/api?module=proxy&action=eth_sendRawTransaction&hex=0xf904808000831cfde080&apikey=YourApiKeyToken
        $ch = curl_init(); 
        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://ropsten.etherscan.io/api?module=proxy&action=eth_sendRawTransaction&hex=" . $tx_hex . "&apikey=" . $etherscanKey); 
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

            $errors = $wpdb->get_var( "SELECT errors FROM $wpdb->protected_designs WHERE shortlink = '$shortlink'" );

            if ($errors < 10) {

                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Pending', 'apiresult' => $output2, 'errors' => errors + 1), array('shortlink' => $shortlink));

                $hash = $data['hash'];
            
                $ch = curl_init($GLOBALS['tx_server'] . "?hash=" . $hash);
            
                curl_exec($ch);
                curl_close($ch);

            }
            else {
                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Error', 'apiresult' => $output2, 'errors' => errors + 1), array('shortlink' => $shortlink));
            }
        }
        else {
            $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Protected', 'apiresult' => $output2), array('shortlink' => $shortlink));
        }
    

        // sleep(300);
        // function get_timestamp (tx_hash) {
        // $tx_block_nr = x;
        // $tx_block_timestamp = y; }
        // while(!$tx_block_timestamp) { 
        // sleeep(300) get_timestamp (tx_hash)
        // } wpdb->update;
         
    }
}


function php_hash( $data ) {
    
    $preview_src = $data['preview_src'];
    
    $sha256_hash = hash_file('sha256', $preview_src);
    return($sha256_hash);
}



add_action( 'rest_api_init', function () {
    register_rest_route( 'new_protected_design', '/new', array(
      'methods' => 'GET',
      'callback' => 'new_protected_design'
    ) );

    register_rest_route( 'get_protected_design', '/get', array(
        'methods' => 'GET',
        'callback' => 'get_protected_design'
    ) );

    register_rest_route( 'php_hash', '/get', array(
        'methods' => 'GET',
        'callback' => 'php_hash'
    ) );

    register_rest_route( 'braintree', '/nonce', array(
        'methods' => 'GET',
        'callback' => 'braintree'
    ) );

    register_rest_route( 'set_message', '/set', array(
        'methods' => 'GET',
        'callback' => 'set_message'
    ) );

    register_rest_route( 'submit_tx', '/submit', array(
        'methods' => 'GET',
        'callback' => 'submit_tx'
    ) );

    register_rest_route( 'broadcast_tx', '/tx', array(
        'methods' => 'GET',
        'callback' => 'broadcast_tx'
    ) );
} );



?>