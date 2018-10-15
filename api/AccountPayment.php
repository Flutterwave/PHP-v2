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

GLOBAL $postData;
$postData =$_POST;
if($_POST["submit"]){
    $publicKey = $postData['publicKey'];
    $secretKey = $postData['secretKey'];
    $env = $postData['env']; // Remember to change this to 'live' when you are going live
    $accountbank =$postData['accountbank'];
    $accountnumber = $postData['accountnumber'];
    $phonenumber = $postData['phonenumber'];
    $amount =  $postData['amount'];
    $country =  $postData['country'];
    $currency =  $postData['currency'];
    //$IP = $postData['IP'];
    $email =$postData['email'];
    $payment_type = $postData['payment_type'];
   
    $_SESSION['publicKey'] = $publicKey;
    $_SESSION['secretKey'] = $secretKey;
    $_SESSION['env'] = $env;
    $_SESSION['accountbank'] = $accountbank;
    $_SESSION['accountnumber'] = $accountnumber;
    $_SESSION['phonenumber'] = $phonenumber;
    $_SESSION['country'] = $country;
    $_SESSION['currency'] = $currency;
    $_SESSION['amount'] = $amount;
   // $_SESSION['IP'] = $IP;
    $_SESSION['email'] = $email;
    $_SESSION['payment_type'] = $payment_type;
    
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
if($postData['payment_type'] === "account"){

    // $post_data = array(
    //     "PBFPubKey" => "FLWPUBK-7adb6177bd71dd43c2efa3f1229e3b7f-X",
    //     "accountbank" => "044",// get the bank code from the bank list endpoint.
    //     "accountnumber"=> "0690000031",
    //     "currency" =>"NGN",
    //     "payment_type"=>"account",
    //     "country"=> "NG",
    //     "amount"=> "10",
    //     "email"=> "emereuwaonueze@gmail.com",
    //     "passcode"=>"09101989",//customer Date of birth this is required for Zenith bank account payment.
    //     "phonenumber"=> "0902620185",
    //     "firstname"=> "temi",
    //     "lastname"=> "desola",
    //     "IP"=> "127.0.0.1",
    //     "txRef"=> "MC-0292920", // merchant unique reference
    //     "device_fingerprint"=>"69e6b7f0b72037aa8428b70fbe03986c"
    // );
    $post_data = array(
        "PBFPubKey" => $_SESSION['publicKey'], 
        "accountbank" => $_SESSION['accountbank'], 
        "accountnumber" => $_SESSION['accountnumber'],
        "payment_type" => $_SESSION['payment_type'],
        "phonenumber" => $_SESSION['phonenumber'],
        "currency"=> $_SESSION['currency'],
        "country" => $_SESSION['country'],
        "amount"=>  $_SESSION['amount'],
        "email" =>  $_SESSION['email'],
        "txRef"=> "MC-".time() // merchant unique reference
        // "firstname"=> "temi",
        // "lastname"=> "desola",
        // "IP"=> "127.0.0.1"
    );
    $payment
    ->eventHandler(new myEventHandler)
    ->setAccount($postData['accountbank'])
    ->setAccountNumber($postData['accountnumber']) 
    ->setAmount($postData['amount'])
    ->setPaymentMethod($postData['payment_type'])
    ->setEndPoint("flwv3-pug/getpaidx/api/charge")
    ->chargePayment($post_data)
    ->validateTransaction("1234");
}

?>