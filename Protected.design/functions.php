<?php

// Define myslq credentials, tx server host, Etherscan API and home url
require_once 'setup/_auth.php';

global $wpdb;


function new_protected_design( $data ) {
    global $wpdb;
    $timestamp = date("Y-m-d H:i:s");
    $status = "Ready for protection";
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

    return $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );

}


function set_message( $data ) {
    global $wpdb;
    
    $message = $data['message'];
    $hash = $data['hash'];
    $preview_url = $data['preview_url'];

    if ($preview_url) {
        $wpdb->get_row( "UPDATE protected_designs SET preview_url = '$preview_url' WHERE hash = '$hash' AND status = 'Ready for protection'" );
    }

    return $wpdb->get_row( "UPDATE protected_designs SET message = '$message' WHERE hash = '$hash' AND status = 'Ready for protection'" );

}


require_once 'includes/cardinity/autoload.php';

use Cardinity\Client;
use Cardinity\Method\Payment;
use Cardinity\Exception;

function cardinity ( $data ) {
    global $wpdb;

    $hashTrunc = $data['hashTrunc'];
    $countryCode = $data['countryCode'];
    $cardNr = $data['cardNr'];
    $cardExpYear = $data['cardExpYear'];
    $cardExpMonth = $data['cardExpMonth'];
    $cardCVC = $data['cardCVC'];
    $cardHolder = $data['cardHolder'];

    $client = Client::create([
        'consumerKey' => $GLOBALS['consumerKey'],
        'consumerSecret' => $GLOBALS['consumerSecret'],
    ]);

    $method = new Payment\Create([
        'amount' => 0.50,
        'currency' => 'USD',
        'settle' => true,
        'description' => '3d-pass',
        'order_id' => $hashTrunc,
        'country' => $countryCode,
        'payment_method' => Payment\Create::CARD,
        'payment_instrument' => [
            'pan' => $cardNr,
            'exp_year' => (int)$cardExpYear,
            'exp_month' => (int)$cardExpMonth,
            'cvc' => $cardCVC,
            'holder' => $cardHolder
        ],
    ]);

    $errors = [];

    try {
        /** @type Cardinity\Method\Payment\Payment */
        $payment = $client->call($method);
    } catch (Cardinity\Exception\InvalidAttributeValue $exception) {
        foreach ($exception->getViolations() as $key => $violation) {
            array_push($errors, $violation->getPropertyPath() . ' ' . $violation->getMessage());
        }
    } catch (Cardinity\Exception\ValidationFailed $exception) {
        foreach ($exception->getErrors() as $key => $error) {
            array_push($errors, $error['message']);
        }
    } catch (Cardinity\Exception\Declined $exception) {
        foreach ($exception->getErrors() as $key => $error) {
            array_push($errors, $error['message']);
        }
    } catch (Cardinity\Exception\NotFound $exception) {
        foreach ($exception->getErrors() as $key => $error) {
            array_push($errors, $error['message']);
        }
    } catch (Exception $exception) {
        $errors = [
            $exception->getMessage(),
            $exception->getPrevious()->getMessage()
        ];
    }

    if ($errors) {
        $wpdb->update('protected_designs', array('paymentresult' => $errors[0]), array('shortlink' => $hashTrunc));

        return $errors;
    }
    else {
        $status = $payment->getStatus();
        
        if ($status == 'approved') {
            $wpdb->update('protected_designs', array( 'status' => 'Pending', 'paid' => '1', 'paymentresult' => $status, 'protection_type' => '1'), array('shortlink' => $hashTrunc));
            
            return $status;
        }
        elseif ($status == 'pending') {
            $auth = $payment->getAuthorizationInformation();
            $payment_id = $payment->getId();

            $pending = [
                'ThreeDForm' => $auth->getUrl(),
                'PaReq' => $auth->getData(),
                'MD' => $payment->getOrderId(),
                'PaymentId' => $payment_id,
            ];

            $wpdb->update('protected_designs', array( 'status' => 'Pending payment', 'paymentresult' => $status, 'payment_id' => $payment_id, 'protection_type' => '1'), array('shortlink' => $hashTrunc));
            return $pending;
        }
    }
}


function submit_tx( $data ) {

    $hash = $data['hash'];
    $shortlink = substr($hash, 0, 12);

    global $wpdb;
    $dbresult = $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );

    $paid = $dbresult->paid;
    $status = $dbresult->status;
    
    if ($paid == '1' && $status != 'Protected') {
        // Inform admin
        mail($GLOBALS['admin_email'], 'PD PAID ' . $shortlink, $shortlink);

        $ch = curl_init($GLOBALS['tx_server'] . ".php?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
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


function submit_tx_2( $data ) {

    $hash = $data['hash'];
    $shortlink = substr($hash, 0, 12);

    global $wpdb;
    $wpdb->update('protected_designs', array('protection_type' => '2', 'status' => 'Scheduled'), array('shortlink' => substr($hash, 0, 12)));

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

        $errorsrow = $wpdb->get_row( "SELECT * FROM protected_designs WHERE shortlink = '$shortlink'" );
        $errors = $errorsrow->errors;
        $error_log = $errorsrow->error_log;
        $apiresult = $errorsrow->apiresult;

        $error_log = $error_log . ", " . $outputdecoded["error"]["message"];
        $apiresult = $apiresult . " XXX " . $output2;

        // If transaction was not broadcasted, then submit transaction again, until there is no error
        if (empty($outputdecoded["result"])) {

            if ($errors < 40) {
                sleep(15);

                $errors = $errors + 1;


                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Pending', 'apiresult' => $apiresult, 'errors' => $errors, 'error_log' => $error_log), array('shortlink' => $shortlink));
                
                // if ($errors % 3 == 0) {
                //     $ch = curl_init($GLOBALS['tx_server'] . "_3.php?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
                //     curl_exec($ch);
                //     curl_close($ch);
                // }

                if ($errors % 2 == 0) {
                    $ch = curl_init($GLOBALS['tx_server'] . "_2.php?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
                    curl_exec($ch);
                    curl_close($ch);
                }

                else if ($errors % 1 == 0) {
                    $ch = curl_init($GLOBALS['tx_server'] . ".php?hash=" . $hash . "&auth=" . $GLOBALS['linux_auth_token']);
                    curl_exec($ch);
                    curl_close($ch);
                }


            }
            else {
                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Error', 'apiresult' => $apiresult), array('shortlink' => $shortlink));
                
            }
        }
        else {
            if (strlen($tx_hash) != 66) {
                $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Error', 'apiresult' => $apiresult, 'otherresult' => 'error_tx_hash'), array('shortlink' => $shortlink));
                return "error_tx_hash";
            }
            
            $wpdb->update('protected_designs', array('tx_hash' => $tx_hash, 'status' => 'Protected', 'apiresult' => $apiresult), array('shortlink' => $shortlink));
            
            file_get_contents(get_stylesheet_directory_uri() . '/check_tx_timestamp' . $GLOBALS['link_end'] . '?tx_hash=' . $tx_hash . '&shortlink=' . $shortlink);
        }
    }
}



function php_hash( $data ) {
    
    $preview_src = $data['preview_src'];
    
    $head = array_change_key_case(get_headers($preview_src, TRUE));
    $filesize = $head['content-length'];

    if (intval($filesize) < 15000000) {
        $sha256_hash = hash_file('sha256', $preview_src);
        return($sha256_hash);
    }
    else {
        return("error_size");
    }
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

    register_rest_route( 'cardinity', '/set', array(
        'methods' => 'GET',
        'callback' => 'cardinity'
    ) );

    register_rest_route( 'set_message', '/set', array(
        'methods' => 'GET',
        'callback' => 'set_message'
    ) );

    register_rest_route( 'submit_tx', '/submit', array(
        'methods' => 'GET',
        'callback' => 'submit_tx'
    ) );

    register_rest_route( 'submit_tx_2', '/submit', array(
        'methods' => 'GET',
        'callback' => 'submit_tx_2'
    ) );

    register_rest_route( 'broadcast_tx', '/tx', array(
        'methods' => 'GET',
        'callback' => 'broadcast_tx'
    ) );
} );



?>