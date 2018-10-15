<?php

namespace Payments;
session_start() ;
// session_destroy();
// Prevent direct access to this class
define("BASEPATH", 1);

require('../lib/rave.php');
require('../lib/raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\Rave\EventHandlerInterface;

if($_POST["submit"]){
    $postData = $_POST;
    $publicKey = $postData['publicKey'];
    $secretKey = $postData['secretKey'];
    $env = $postData['env']; // Remember to change this to 'live' when you are going live
    $amount = $postData['amount'];
    $pin = $postData['pin'];
    $cardno = $postData['cardno'];
    $cvv =  $postData['cvv'];
    $expirymonth = $postData['expirymonth'];
    $expiryyear =$postData['expiryyear'];

    $_SESSION['publicKey'] = $publicKey;
    $_SESSION['secretKey'] = $secretKey;
    $_SESSION['env'] = $env;
    $_SESSION['cardno'] = $cardno;
    $_SESSION['pin'] = $pin;
    $_SESSION['amount'] = $amount;
    $_SESSION['cvv'] = $cvv;
    $_SESSION['expirymonth'] = $expirymonth;
    $_SESSION['expiryyear'] = $expiryyear;
}

$prefix = 'RV'; // Change this to the name of your business or app
$overrideRef = false;



// Uncomment here to enforce the useage of your own ref else a ref will be generated for you automatically
if(isset($postData['ref'])){
    $prefix = $postData['ref'];
    $overrideRef = true;
}

$payment = new Rave($_SESSION['publicKey'], $_SESSION['secretKey'], $prefix, $_SESSION['env'], $overrideRef);


class myEventHandler implements EventHandlerInterface{
    /**
     * This is called only when a transaction is successful 
     * @param array
     * */
    function onSuccessful($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
        if($transactionData["data"]["chargecode"] === '00' || $transactionData["data"]["chargecode"] === '0'){
            echo "Transaction Completed";
        }else{
          $this->onFailure($transactionData);
      }
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
       
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        // Do something, anything!
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
       
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
      
    }
}

echo "<pre>";
if($postData['payment_type'] ===  "card"){
        $post_data = array(
            "PBFPubKey" => $_SESSION['publicKey'], 
            "amount" => $_SESSION['amount'], 
            "cardno" => $_SESSION['cardno'],
            "cvv" => $_SESSION['cvv'],
            "pin" => $_SESSION['pin'],
            "email" => 'emereuwaonueze@gmail.com',
            "txRef" => "MC-".time(),
            "expirymonth"=> $_SESSION['expirymonth'],
            "expiryyear" => $_SESSION['expiryyear']
        );
        $payment
        ->eventHandler(new myEventHandler)
        ->setAmount($postData['amount'])
        ->setCardNo($postData['cardno'])
        ->setCVV($postData['cvv']) 
        ->setExpiryMonth($postData['expirymonth'])
        ->setExpiryYear($postData['expiryyear'])
        ->setPaymentMethod($postData['payment_type'])
        ->setEndPoint("flwv3-pug/getpaidx/api/charge")
        ->chargePayment($post_data)
        ->validateTransaction("1234");

}

?>