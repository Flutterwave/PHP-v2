<?php
    // ob_flush();
    // require("Flutterwave-Rave-PHP-SDK/lib/AccountPayment.php");
    // use Flutterwave\Account;

    // $array = array(
    //     "PBFPubKey" =>"FLWPUBK-3e20938934b74d294ddfe4c87e746100-X",
    //     //"accountbank"=> "058",// get the bank code from the bank list endpoint.
    //     "accountbank"=> "011",
    //     //"accountnumber" => "0255597451",
    //     "accountnumber" => "3028667062",
    //     "currency" => "NGN",
    //     "payment_type" => "account",
    //     "country" => "NG",
    //     "amount" => "10",
    //     "redirect_url"=> "https://google.com",
    //     "email" => "emereuwaonueze@gmail.com",
    //     "bvn" => "22389407285",
    //     "phonenumber" => "0902620185",
    //     "firstname" => "eze",
    //     "lastname" => "Emmanuel",
    //     "txRef" => "MC-".time()// merchant unique reference
    // );
    // $account = new Account();
    // $result = $account->accountCharge($array);
    // print_r($result)
    //$result = json_decode($result, true);
    //$result = $account->validateTransaction("12345");
   // $result = $account->verifyTransaction("MC-1550508976");
//    echo $result["data"]["authurl"];

//    if($result["data"]["authurl"]){
    
//        //echo'<html><head><title>Iframe</title></head><body><iframe width="700px" height="900px" src="'.$result["data"]["authurl"].'"></iframe></body></html>';
//        header("Location:".$result["data"]["authurl"]);
//    }else{
//     print_r($result);
//    }

// require("Flutterwave-Rave-PHP-SDK/lib/MobileMoney.php");
// use Flutterwave\MobileMoney;

// $array = array(
//     "PBFPubKey" =>"FLWPUBK-b55586524308f4b77b5523a59ebd66cf-X",
//     "currency"=> "GHS",
//     "payment_type" => "mobilemoneygh",
//     "country" => "GH",
//     "amount" => "10",
//     "email" => "eze@gmail.com",
//     "phonenumber"=> "054709929220",
//     "network"=> "MTN",
//     "firstname"=> "eze",
//     "lastname"=> "emmanuel",
//     "voucher"=> "128373", // only needed for Vodafone users.
//     "IP"=> "355426087298442",
//     "txRef"=> "MC-123456789",
//     "orderRef"=> "MC_123456789",
//     "is_mobile_money_gh"=> 1,
//     "redirect_url"=> "https://rave-webhook.herokuapp.com/receivepayment",
//     "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"

// );
//     $mobilemoney = new MobileMoney();
//     $result = $mobilemoney->mobilemoney($array);
//     //$result = json_decode($result, true);
//     //$result = $account->validateTransaction("12345");
//    // $result = $account->verifyTransaction("MC-1550508976");
//     print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/VirtualCards.php");
// use Flutterwave\VirtualCard;

// $array = array(
//     "secret_key"=>"FLWSECK-c481f575f255c9344dc128b12e5146c8-X",
// 	"currency"=> "NGN",
// 	"amount"=>"200",
// 	"billing_name"=> "Mohammed Lawal",
// 	"billing_address"=>"DREAM BOULEVARD",
// 	"billing_city"=> "ADYEN",
// 	"billing_state"=>"NEW LANGE",
// 	"billing_postal_code"=> "293094",
// 	"billing_country"=> "US"
// );
//     $virtualCard = new VirtualCard();
//     $result = $virtualCard->create($array);
//     print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/VirtualCards.php");
// use Flutterwave\VirtualCard;

// $array = array(
//     "FromDate"=> "2019-02-13",
//     "ToDate"=> "2019-12-21",
//     "PageIndex"=> 0,
//     "PageSize"=> 20,
//     "CardId"=> "20975b22-8219-4b18-92d5-9e19c5890497 ",
//     "secret_key"=>"FLWSECK-c481f575f255c9344dc128b12e5146c8-X",
//     // "id"=> "20975b22-8219-4b18-92d5-9e19c5890497",
//     // "amount"=> "200",
//     // "debit_currency"=> "NGN",
// );
//     $virtualCard = new VirtualCard();
//     $result = $virtualCard->transactions($array);
//     print_r($result);
    

// require("Flutterwave-Rave-PHP-SDK/lib/CardPayment.php");
// use Flutterwave\Card;
// $array = array(
//     "PBFPubKey" => "FLWPUBK-3b89b62623a703ab8fbc72741402c011-X",
//             "cardno" =>"5438898014560229",
//             "cvv" => "564",
//             "expirymonth"=> "10",
//             "expiryyear"=> "20",
//             "currency"=> "NGN",
//             "country"=> "NG",
//             "amount"=> "2000",
//             "pin"=>"3310",
//             "payment_plan"=> "987",
//             "email"=> "ezechukwu1995@gmail.com",
//             "phonenumber"=> "0902620185",
//             "firstname"=> "Eze",
//             "lastname"=> "Emmanuel",
//             "IP"=> "355426087298442",
//             "txRef"=>"MC-".time(),// your unique merchant reference
//             "meta"=>["metaname"=> "Rave", "metavalue"=>"123949494DC"],
//             "redirect_url"=>"https://rave-webhook.herokuapp.com/receivepayment",
//             "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"
// );
// $card = new Card();
//$result = $card->cardCharge($array);


//$array["suggested_auth"] = "PIN";

    // // $array["billingzip"] = "07205";
    // // $array["billingcity"] = "Hillside";
    // // $array["billingaddress"] = "470 Mundet PI";
    // // $array["billingstate"] = "NJ";
    // // $array["billingcountry"] = "US";

//$result = $card->cardCharge($array);
//$result = $card->validateTransaction("12345");
//$result = $card->verifyTransaction("MC-1552495607");
print_r($result);



// require("Flutterwave-Rave-PHP-SDK/lib/Bvn.php");
// use Flutterwave\Bvn;
// $bvn = new Bvn();
// $result = $bvn->verifyBVN("123456789");
// print_r($result);


// require("Flutterwave-Rave-PHP-SDK/lib/PaymentPlan.php");
// use Flutterwave\PaymentPlan;

// $array = array(
//     "amount" => "2000",
//      "name"=> "The Premium Plan",
//      "interval"=> "monthly",
//      "duration"=> "12",
//      "seckey" => "FLWSECK-c789df9e4953611f46cc13126e84f006-X"
// );

// $plan = new PaymentPlan();
// $result = $plan->createPlan($array);
// print_r($result);


// require("Flutterwave-Rave-PHP-SDK/lib/Subaccount.php");
// use Flutterwave\Subaccount;

// $array = array(
//         "account_bank"=>"044",
//         "account_number"=> "0690000030",
//         "business_name"=> "JK Services",
//         "business_email"=> "jke@services.com",
//         "business_contact"=> "Seun Alade",
//         "business_contact_mobile"=> "090890382",
//         "business_mobile"=> "09087930450",
//         "meta" => ["metaname"=> "MarketplaceID", "metavalue"=>"ggs-920900"],
//         "seckey"=> "FLWSECK-c789df9e4953611f46cc13126e84f006-X"
// );

// $subaccount = new Subaccount();
// $result = $subaccount->subaccount($array);
// print_r($result);


// require("Flutterwave-Rave-PHP-SDK/lib/Recipient.php");
// use Flutterwave\Recipient;

// $array = array(
//     "account_number"=>"0690000030",
// 	"account_bank"=>"044",
// 	"seckey"=>"FLWSECK-c789df9e4953611f46cc13126e84f006-X"
// );

// $recipient = new Recipient();
// $result = $recipient->recipient($array);
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/Refund.php");
// use Flutterwave\Refund;

// $array = array(
//     "ref"=>"ACHG-1540381755976",
// 	"seckey"=>"FLWSECK-c789df9e4953611f46cc13126e84f006-X"
// );

// $refund = new Refund();
// $result = $refund->refund($array);
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
// use Flutterwave\Subscription;
// $id = 406925;
// $subscription = new Subscription();
// $result = $subscription->fetchSubscriptionById($id);
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
// use Flutterwave\Subscription;
// $email = "emereuwaonueze@gmail.com";
// $subscription = new Subscription();
// $result = $subscription->fetchSubscriptionByEmail($email);
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
// use Flutterwave\Subscription;
// $id = 406925;
// $subscription = new Subscription();
// $result = $subscription->activateSubscription($id);
// print_r($result);


// require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
// use Flutterwave\Subscription;
// $id = 406925;
// $subscription = new Subscription();
// $result = $subscription->cancelSubscription($id);
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/TransactionVerification.php");
// use Flutterwave\TransactionVerification;

// $verify = new TransactionVerification("FLWPUBK-3b89b62623a703ab8fbc72741402c011-X","FLWSECK-c789df9e4953611f46cc13126e84f006-X","staging");
// $result = $verify->verifyTransaction();
// print_r($result);

// require("Flutterwave-Rave-PHP-SDK/lib/Transfer.php");
// use Flutterwave\Transfer;

// $array = array(
//         "account_bank"=>"044",
//         "account_number"=> "0690000044",
//         "amount"=> "500",
//         "seckey"=>"FLWSECK-c481f575f255c9344dc128b12e5146c8-X",
//         "narration"=> "New transfer",
//         "currency"=>"NGN",
//         "reference"=> "mk-".time()
// );

// $transfer = new Transfer();
// $result = $transfer->singleTransfer($array);
// print_r($result);

