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

    return $wpdb->get_row( "UPDATE protected_designs SET message = '$message' WHERE hash = '$hash' AND status = 'Waiting for payment'" );

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
        $wpdb->update('protected_designs', array('status' => 'Pending', 'paid' => '1', 'paymentresult' => $result), array('hash' => $hash));
      } else {
        global $wpdb;
        // @todo: test error loging
        $wpdb->update('protected_designs', array('paymentresult' => $result), array('hash' => $hash));
      }

      return($result);
}


function submit_tx( $data ) {

    $hash = $data['hash'];
    $shortlink = substr($hash, 0, 12);
    // echo($GLOBALS['tx_server'] . "?hash=" . $hash);

    global $wpdb;
    $dbresult = $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );

    $paid = $dbresult->paid;
    $status = $dbresult->status;
    
    if ($paid == '1' && $status != 'Protected') {
        // @todo: if status paid = 1, then execute following script
        $ch = curl_init($GLOBALS['tx_server'] . "?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
        curl_exec($ch);

        // @todo: test error loging
        if(curl_error($ch))
        {
            global $wpdb;
            $wpdb->update('protected_designs', array('otherresult' => curl_error($ch)), array('shortlink' => substr($hash, 0, 12)));
            // Alert admin
            mail($GLOBALS['admin_email'], 'PD Error', 'Linux server not accessible');
        }
    }

}


function broadcast_tx( $data ) {
    global $wpdb;

    $tx_auth_token = $data['tx_auth_token'];

    if ($tx_auth_token == $GLOBALS['tx_auth_token']) {
        $tx_hex = $data['tx_hex'];
        $hash = $data['hash'];
        $shortlink = substr($hash, 0, 12);

        $wpdb->update('protected_designs', array('tx_hex' => $tx_hex), array('shortlink' => $shortlink));
        
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

            // $errors = $wpdb->get_var( "SELECT errors FROM $wpdb->protected_designs WHERE shortlink = '$shortlink'" );
            $errorsrow = $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );
            $errors = $errorsrow->errors;

            if ($errors < 30) {
                sleep(10);

                $errors = $errors + 1;

                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Pending', 'apiresult' => $output2, 'errors' => $errors), array('shortlink' => $shortlink));
                
                // file_get_contents($GLOBALS['tx_server'] . "?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
                $ch = curl_init($GLOBALS['tx_server'] . "?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
                curl_exec($ch);
                curl_close($ch);

                echo("\n\nError nr: " . $errors);

            }
            else {
                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Error', 'apiresult' => $output2), array('shortlink' => $shortlink));
                
            }
        }
        else {
            $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Protected', 'apiresult' => $output2), array('shortlink' => $shortlink));
            
            file_get_contents(get_stylesheet_directory_uri() . '/check_tx_timestamp.php?tx_hash=' . $tx_hash . '&shortlink=' . $shortlink);
        }
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