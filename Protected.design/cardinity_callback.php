<?php

require_once 'setup/_auth.php';

load_global_wpdb();
global $wpdb;

echo "Processing payment...";

if ($_POST['MD']) {
    $hash_trunc = $_POST['MD'];
}

if ($_POST['PaRes']) {
    $PaRes = $_POST['PaRes'];
}

$payment_id = $wpdb->get_var("SELECT payment_id FROM protected_designs WHERE shortlink = '$hash_trunc'");

require_once 'includes/cardinity/autoload.php';
use Cardinity\Client;
use Cardinity\Method\Payment;

$client = Client::create([
    'consumerKey' => $GLOBALS['consumerKey'],
    'consumerSecret' => $GLOBALS['consumerSecret'],
]);

$method = new Payment\Finalize($payment_id, $PaRes);


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

// var_dump($payment);

if ($errors) {
    $wpdb->update('protected_designs', array('status' => 'Payment error', 'paymentresult' => $errors[0]), array('shortlink' => $hash_trunc));
}
else {
    $status = $payment->getStatus();

    if ($status == 'approved') {
        $wpdb->update('protected_designs', array( 'status' => 'Payment received', 'paid' => '1', 'paymentresult' => $status, 'protection_type' => '1'), array('shortlink' => $hash_trunc));
    }
    else if ($status == 'declined') {
        $wpdb->update('protected_designs', array( 'status' => 'Payment declined', 'paymentresult' => $status, 'protection_type' => '1'), array('shortlink' => $hash_trunc));
    }
    else {
        $wpdb->update('protected_designs', array( 'status' => 'Payment error', 'paymentresult' => $status, 'protection_type' => '1'), array('shortlink' => $hash_trunc));
    }
}

header("Location: ".$GLOBALS['home_url'].$hash_trunc);
die();

?>