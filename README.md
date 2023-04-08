<p align="center">
    <img title="Flutterwave" height="200" src="https://flutterwave.com/images/logo/full.svg" width="50%"/>
</p>

# Rave PHP SDK

> Class documentation can be found here [https://flutterwave.github.io/Flutterwave-Rave-PHP-SDK/packages/Default.html](https://flutterwave.github.io/Flutterwave-Rave-PHP-SDK/packages/Default.html)

Use this library to integrate your PHP app to Rave.

Edit the `paymentForm.php` and `processPayment.php` files to suit your purpose. Both files are well documented.

Simply redirect to the `paymentForm.php` file on your browser to process a payment.

The vendor folder is committed into the project to allow easy installation for those who do not have composer installed.
It is recommended to update the project dependencies using:

```shell
$ composer require flutterwavedev/flutterwave-php
```

## Sample implementation

In this implementation, we are expecting a form encoded POST request to this script.
The request will contain the following parameters.

- payment_method `Can be card, account, both`
- description `Your transaction description`
- logo `Your logo url`
- title `Your transaction title`
- country `Your transaction country`
- currency `Your transaction currency`
- email `Your customer's email`
- firstname `Your customer's first name`
- lastname `Your customer's last name`
- phonenumber `Your customer's phonenumber`
- pay_button_text `The payment button text you prefer`
- ref `Your transaction reference. It must be unique per transaction.  By default, the Rave class generates a unique transaction reference for each transaction. Pass this parameter only if you uncommented the related section in the script below.`

```php
// Prevent direct access to this class
define("BASEPATH", 1);

include('lib/rave.php');
include('lib/raveEventHandlerInterface.php');

use Flutterwave\Rave;
use Flutterwave\Rave\EventHandlerInterface;

$URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
$getData = $_GET;
$postData = $_POST;
$publicKey = '****YOUR**PUBLIC**KEY****'; // Remember to change this to your live public keys when going live
$secretKey = '****YOUR**SECRET**KEY****'; // Remember to change this to your live secret keys when going live
$env = 'staging'; // Remember to change this to 'live' when you are going live
$prefix = 'MY_APP_NAME'; // Change this to the name of your business or app
$overrideRef = false;

// Uncomment here to enforce the useage of your own ref else a ref will be generated for you automatically
// if($postData['ref']){
//     $prefix = $postData['ref'];
//     $overrideRef = true;
// }

$payment = new Rave($publicKey, $secretKey, $prefix, $env, $overrideRef);


// This is where you set how you want to handle the transaction at different stages
class myEventHandler implements EventHandlerInterface{
    /**
     * This is called when the Rave class is initialized
     * */
    function onInit($initializationData){
        // Save the transaction to your DB.
        echo 'Payment started......'.json_encode($initializationData).'<br />'; //Remember to delete this line
    }
    
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
        echo 'Payment Successful!'.json_encode($transactionData).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
        echo 'Payment Failed!'.json_encode($transactionData).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
        echo 'Payment requeried......'.$transactionReference.'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        // Do something, anything!
        echo 'An error occured while requeying the transaction...'.json_encode($requeryResponse).'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
        echo 'Payment canceled by user......'.$transactionReference.'<br />'; //Remember to delete this line
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
        echo 'Payment timeout......'.$transactionReference.' - '.json_encode($data).'<br />'; //Remember to delete this line
    }
}

if($postData['amount']){
    // Make payment
    $payment
    ->eventHandler(new myEventHandler)
    ->setAmount($postData['amount'])
    ->setPaymentMethod($postData['payment_method']) // value can be card, account or both
    ->setDescription($postData['description'])
    ->setLogo($postData['logo'])
    ->setTitle($postData['title'])
    ->setCountry($postData['country'])
    ->setCurrency($postData['currency'])
    ->setEmail($postData['email'])
    ->setFirstname($postData['firstname'])
    ->setLastname($postData['lastname'])
    ->setPhoneNumber($postData['phonenumber'])
    ->setPayButtonText($postData['pay_button_text'])
    ->setRedirectUrl($URL)
    // ->setMetaData(array('metaname' => 'SomeDataName', 'metavalue' => 'SomeValue')) // can be called multiple times. Uncomment this to add meta datas
    // ->setMetaData(array('metaname' => 'SomeOtherDataName', 'metavalue' => 'SomeOtherValue')) // can be called multiple times. Uncomment this to add meta datas
    ->initialize();
}else{
    if($getData['cancelled'] && $getData['txref']){
        // Handle canceled payments
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['txref'])
        ->paymentCanceled($getData['txref']);
    }elseif($getData['txref']){
        // Handle completed payments
        $payment->logger->notice('Payment completed. Now requerying payment.');
        
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['txref']);
    }else{
        $payment->logger->warn('Stop!!! Please pass the txref parameter!');
        echo 'Stop!!! Please pass the txref parameter!';
    }
}
```

# Support Direct Charges

Save your PUBLIC_KEY, SECRET_KEY, ENV in the .env file
```env

PUBLIC_KEY = "****YOUR**PUBLIC**KEY****"
SECRET_KEY = "****YOUR**SECRET**KEY****"
ENV = "staging or live"

```

## Account Charge Sample implementation

The following implementation shows how to initiate a direct bank charge
```php
require("Flutterwave-Rave-PHP-SDK/lib/AccountPayment.php");
use Flutterwave\Account;

    $array = array(
        "PBFPubKey" =>"****YOUR**PUBLIC**KEY****",
        "accountbank"=> "044",// get the bank code from the bank list endpoint.
        "accountnumber" => "0690000031",
        "currency" => "NGN",
        "payment_type" => "account",
        "country" => "NG",
        "amount" => "10",
        "email" => "eze@gmail.com",
       // passcode => "09101989",//customer Date of birth this is required for Zenith bank account payment.
        "bvn" => "12345678901",
        "phonenumber" => "0902620185",
        "firstname" => "temi",
        "lastname" => "desola",
        "IP" => "355426087298442",
        "txRef" => "MC-".time(), // merchant unique reference
        "device_fingerprint" => "69e6b7f0b72037aa8428b70fbe03986c"

    );
$account = new Account();
$result = $account->accountCharge($array);
print_r($result);
```
## Card Charge Sample implementation

The following implementation shows how to initiate a direct card charge
```php
require("Flutterwave-Rave-PHP-SDK/lib/CardPayment.php");
use Flutterwave\Card;
    $array = array(
        "PBFPubKey" => "****YOUR**PUBLIC**KEY****",
        "cardno" =>"5438898014560229",
        "cvv" => "890",
        "expirymonth"=> "09",
        "expiryyear"=> "19",
        "currency"=> "NGN",
        "country"=> "NG",
        "amount"=> "2000",
        "pin"=>"3310",
        //"payment_plan"=> "980", //use this parameter only when the payment is a subscription, specify the payment plan id
        "email"=> "eze@gmail.com",
        "phonenumber"=> "0902620185",
        "firstname"=> "temi",
        "lastname"=> "desola",
        "IP"=> "355426087298442",
        "txRef"=>"MC-".time(),// your unique merchant reference
        "meta"=>["metaname"=> "flightID", "metavalue"=>"123949494DC"],
        "redirect_url"=>"https://rave-webhook.herokuapp.com/receivepayment",
        "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"
    );
$card = new Card();
$result = $card->cardCharge($array);
print_r($result);
```

## Mobile Money Payments

The following implementation shows how to initiate a mobile money payment
```php
require("Flutterwave-Rave-PHP-SDK/lib/MobileMoney.php");
use Flutterwave\MobileMoney;

$array = array(
    "PBFPubKey" =>"****YOUR**PUBLIC**KEY****",
    "currency"=> "GHS",
    "payment_type" => "mobilemoneygh",
    "country" => "GH",
    "amount" => "10",
    "email" => "eze@gmail.com",
    "phonenumber"=> "054709929220",
    "network"=> "MTN",
    "firstname"=> "eze",
    "lastname"=> "emmanuel",
    "voucher"=> "128373", // only needed for Vodafone users.
    "IP"=> "355426087298442",
    "txRef"=> "MC-123456789",
    "orderRef"=> "MC_123456789",
    "is_mobile_money_gh"=> 1,
    "redirect_url"=> "https://rave-webhook.herokuapp.com/receivepayment",
    "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"

);
    $mobilemoney = new MobileMoney();
    $result = $mobilemoney->mobilemoney($array);
    $print_r($result);
```
## Transfer

The following implementation shows how to initiate a transfer
```php
require("Flutterwave-Rave-PHP-SDK/lib/Transfer.php");
use Flutterwave\Transfer;

$array = array(
  "account_bank" => "044",
  "account_number" => "0690000044",
  "amount" => 500,
  "seckey" => "FLWSECK-67fexxxxxxxxxxxxxxxxxx9922512-X",
  "narration" => "New transfer",
  "currency" => "NGN",
  "reference" => "mk-902837-jk",
  "beneficiary_name" => "Kwame Adew" // only pass this for non NGN 
);
    $payment = new Transfer();
    $result = $payment->singleTransfer($array);
    //$result = $payment->bulkTransfer($array);
    //$result = $payment->listTransfers($array);
    print_r($result);
```

## Create Vitual Cards

The following implementation shows how to create virtual cards on rave
```php
require("Flutterwave-Rave-PHP-SDK/lib/VirtualCards.php");
use Flutterwave\VirtualCard;

$array = array(
    "secret_key"=>"****YOUR**SECRET**KEY****",
	"currency"=> "NGN",
	"amount"=>"200",
	"billing_name"=> "Mohammed Lawal",
	"billing_address"=>"DREAM BOULEVARD",
	"billing_city"=> "ADYEN",
	"billing_state"=>"NEW LANGE",
	"billing_postal_code"=> "293094",
	"billing_country"=> "US"
);
    $virtualCard = new VirtualCard();
    $result = $virtualCard->create($array);
    print_r($result);
```


## BVN Verification Sample implementation

The following implementation shows how to verify a Bank Verification Number
```php
require("Flutterwave-Rave-PHP-SDK/lib/Bvn.php");
use Flutterwave\Bvn;
$bvn = new Bvn();
$result = $bvn->verifyBVN("123456789");
print_r($result);
```

## Create a Payment Plan Sample implementation

The following implementation shows how to create a payment plan on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/PaymentPlan.php");
use Flutterwave\PaymentPlan;

$array = array(
    "amount" => "2000",
     "name"=> "The Premium Plan",
     "interval"=> "monthly",
     "duration"=> "12",
     "seckey" => "****YOUR**SECRET**KEY****"
);

$plan = new PaymentPlan();
$result = $plan->createPlan($array);
print_r($result);
```

## Create a Subaccount Sample implementation

The following implementation shows how to create a subaccount on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/Subaccount.php");
use Flutterwave\Subaccount;

$array = array(
        "account_bank"=>"044",
        "account_number"=> "0690000030",
        "business_name"=> "JK Services",
        "business_email"=> "jke@services.com",
        "business_contact"=> "Seun Alade",
        "business_contact_mobile"=> "090890382",
        "business_mobile"=> "09087930450",
        "meta" => ["metaname"=> "MarketplaceID", "metavalue"=>"ggs-920900"],
        "seckey"=> "****YOUR**SECRET**KEY****"
);

$subaccount = new Subaccount();
$result = $subaccount->subaccount($array);
print_r($result);
```
## Create Transfer Recipient Sample implementation

The following implementation shows how to create a transfer recipient on the rave dashboard
```php
require("Flutterwave-Rave-PHP-SDK/lib/Recipient.php");
use Flutterwave\Recipient;

$array = array(
    "account_number"=>"0690000030",
	"account_bank"=>"044",
	"seckey"=>"****YOUR**SECRET**KEY****"
);

$recipient = new Recipient();
$result = $recipient->recipient($array);
print_r($result);
```

## Create Refund Sample implementation

The following implementation shows how to initiate a refund
```php
require("Flutterwave-Rave-PHP-SDK/lib/Refund.php");
use Flutterwave\Refund;

$array = array(
    "ref"=>"txRef",//pass a transaction reference to initiate refund
	"seckey"=>"****YOUR**SECRET**KEY****"
);

$refund = new Refund();
$result = $refund->refund($array);
print_r($result);
```

## Subscriptions Sample implementation

The following implementation shows how to activate a subscription, fetch a subscription, get all subscription
```php
require("Flutterwave-Rave-PHP-SDK/lib/Subscription.php");
use Flutterwave\Subscription;

$email = "eze@gmail.com";//email address of subscriber
$id = 1112 //Id of subscription plan

$subscription = new Subscription();

$resultFetch = $subscription->fetchASubscription($email);//fetches a subscription
$resultGet = $subscription->getAllSubscription();//gets all existing subscription
$resultActivate = $subscription->activateSubscription($id);// activates a subscription plan

//returns the result 
print_r($result);
```
## Bill Sample implementation

The following implementation shows how to pay for any kind of bill from Airtime to DSTv payments to Tolls.
Please view the rave documentation section on Bill payment for different types of bill services you can pass into the ```payBill``` method as an```$array```.
visit: https://developer.flutterwave.com/reference#bill-payments
```php
require("Flutterwave-Rave-PHP-SDK/lib/Bill.php");
use Flutterwave\Bill;

$array = array(
"secret_key" => "YOUR SECRET KEY",
  "service" => "fly_buy",
  "service_method" => "post",
  "service_version"=> "v1",
  "service_channel" => "rave",
  "service_payload" => array(
    "Country" => "NG",
    "CustomerId" => "+23490803840303",
    "Reference" => "9300049404444",
    "Amount" => 500,
    "RecurringType" => 0,
    "IsAirtime" => true,
    "BillerName" => "AIRTIME"
    )
);

$airtime = new Bill();
$result = $airtime->payBill($array);
print_r($result);
```

## Ebill Sample implementation

The following implementation shows how to create a electronic receipt.

```php
require("Flutterwave-Rave-PHP-SDK/lib/Ebill.php");
use Flutterwave\Ebill;

$array = array(
"SECKEY" => "YOUR SECRET KEY",
  "narration" => "",
  "numberofunits" => 1,
  "currency"=> "NGN",
  "amount" => 60000,
  "phonenumber" => "09067985861",
  "email" => "user@gmail.com",
  "txRef" => "",//must be unique
  "IP" => "09067985861",
  "country" => "NG",
  "phonenumber" => "09067985861",
  "custom_business_name" => "RaveShoppin" 
);

$receipt = new Ebill();
$result = $receipt->order($array);
print_r($result);
```

## VirtualAccount Sample implementation

The following implementation shows how to create a virtual Account.
Please view the documentation for more options that can be added in the payload
https://developer.flutterwave.com/reference#create-a-virtual-account-number

```php
require("Flutterwave-Rave-PHP-SDK/lib/VirtualAccount.php");
use Flutterwave\VirtualAccount;

$array = array(
"email" => "",
  "seckey" => "YOUR SECRET KEY",
  "narraction" => "John Doe", 
);

$account = new VirtualAccount();
$result = $account->virtualAccount($array);
print_r($result);
```
## Preauth Sample implementation

Card preauthorisation allows a merchant preauthorise a specific amount to be paid by a customer. Once preauthorised successfully, a hold is put on the amount specified, and left for the merchant to capture that amount at a later time or date. Merchants after preauthorising can perform the following actions:void,refund, capture.
Please note that for each action there is a different payload to pass as an array.

```php
require("Flutterwave-Rave-PHP-SDK/lib/Preauth.php");
use Flutterwave\Preauth;

$array = array(
"PBFPubKey"=> "FLWPUBK-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-X",
  "cardno"=> "5438898014560229",
  "charge_type"=> "preauth",
  "cvv"=> "812",
  "expirymonth"=> "08",
  "expiryyear"=> "20",
  "currency"=> "NGN",
  "country"=> "NG",
  "amount"=> "100",
  "email"=> "user@example.com",
  "phonenumber"=> "08056552980",
  "firstname"=> "user",
  "lastname"=> "example",
  "IP"=> "40.198.14",
  "txRef"=> "MC-12344358",//must be unique
  "redirect_url"=> "https://rave-web.herokuapp.com/receivepayment",
  "device_fingerprint"=> "69e6b7f0b72037aa8428b70fbe03986c"
);

$payment = new Preauth();
$result = $payment->accountCharge($array);//to charge to card
//$capturePayment = $payment->captureFunds($array);//note the payload ```$array``` for this would be different. refer to documentation //on data to be passed.
//print_r($capturePayment);
//$refundOrVoid = $payment->refundOrVoid($array);
//print_r($refundOrVoid);
print_r($result);
```
## Tokenized Charge Sample implementation

Once the charge and validation leg is complete for the first charge on the card, you can make use of the token for subsequent charges.

```php
require("Flutterwave-Rave-PHP-SDK/lib/TokenizedCharge.php");
use Flutterwave\TokenizedCharge;

$array = array(
"SECKEY":"FLWSECK-xxxxxxxxxxx-X",
"token":"flw-t1nf-404dff6823ff91ce154f04dd40085b9e-m03k",
"currency":"NGN",
"country":"NG",
"amount":"100",
"email":"user@example.com",
"firstname":"Yemi",
"lastname":"Oyeleke",
"IP":"190.233.222.1",
"narration":"Internet Renewal",
"txRef":"MC_1522966555872",
"meta":""
);

$payment = new TokenizedCharge();
$result = $payment->tokenCharge($array);
//$updateEmailLinkedToToken = $payment->updateEmailTiedToToken($array);//updates email linked to the token
//print_r($updateEmailLinkedToToken);
//$bulkCharge = $payment->bulkCharge($array);//initiate bulk charges
//print_r($bulkCharge);
//$checkBulkStatus = $payment->bulkChargeStatus($array);//checks the status of the bulk charge.
//print_r($checkBulkStatus);
print_r($result);
```

## view Transactions Sample implementation

 list all transactions on your account. You could do a specific query using ```customer_email``` or ```customer_fullname``` to make specifc search. View all successfull or failed transactions for a particular period, month or year.
 Please read the MISCELLANEOUS section of the Api documentation for more option to pass.
 https://developer.flutterwave.com/reference#list-transactions 

```php
require("Flutterwave-Rave-PHP-SDK/lib/Transactions.php");
use Flutterwave\Transactions;

$array = array(
  "seckey"=> "Merchant secret key",
  "from"=>"2018-01-01",
  "to" => "2018-03-30",
  "currency" => "NGN",
  "status" => "successful" 
);

$payment = new Transactions();
$result = $payment->viewTransactions($array);
print_r($result);




You can also find the class documentation in the docs folder. There you will find documentation for the `Rave` class and the `EventHandlerInterface`.

Enjoy... :v:

## ToDo

- Write Unit Test
- Support Tokenized payment
