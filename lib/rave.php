<?php 
namespace Flutterwave;

// Prevent direct access to this class
//defined('BASEPATH') OR exit('No direct script access allowed'); // Uncomment this link if you need this

require __DIR__.'/../vendor/autoload.php'; // Uncomment this autoloader if you need it


use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Unirest\Request;
use Unirest\Request\Body;
use Dotenv;

$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();
/**
 * Flutterwave's Rave payment gateway PHP SDK
 * @author Olufemi Olanipekun <iolufemi@ymail.com>
 * @author Emereuwaonu Eze <emereuwaonueze@gmail.com>
 * @version 1.0
 **/

class Rave {
    //Api keys
    protected $publicKey;
    protected $secretKey;
    protected $txref;
    protected $integrityHash;
    protected $payButtonText = 'Make Payment';
    protected $redirectUrl;
    protected $meta = array();
    // protected $env;
    protected $transactionPrefix;
   // public $logger;
    protected $handler;
    // protected $stagingUrl = 'https://ravesandboxapi.flutterwave.com';
    protected $liveUrl = 'https://api.ravepay.co';
    protected $baseUrl;
    protected $transactionData;
    protected $overrideTransactionReference;
    protected $requeryCount = 0;

    //Payment information
    protected $account;
    protected $accountno;
    protected $key;
    protected $pin;
    protected $json_options;
    protected $post_data;
    protected $options;
    protected $card_no;
    protected $cvv;
    protected $expiry_month;
    protected $expiry_year;
    protected $amount;
    protected $paymentOptions = Null;
    protected $customDescription;
    protected $customLogo;
    protected $customTitle;
    protected $country;
    protected $currency;
    protected $customerEmail;
    protected $customerFirstname;
    protected $customerLastname;
    protected $customerPhone;

    //EndPoints 
    protected $end_point ;
    protected $authModelUsed;
    protected $flwRef;
    protected $txRef;

    /**
     * Construct
     * @param string $publicKey Your Rave publicKey. Sign up on https://rave.flutterwave.com to get one from your settings page
     * @param string $secretKey Your Rave secretKey. Sign up on https://rave.flutterwave.com to get one from your settings page
     * @param string $prefix This is added to the front of your transaction reference numbers
     * @param string $env This can either be 'staging' or 'live'
     * @param boolean $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     * @return object
     * */
    function __construct($publicKey, $secretKey, $prefix = 'RV', $overrideRefWithPrefix = false){
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        // $this->env = $env;
        $this->transactionPrefix = $overrideRefWithPrefix ? $prefix : $prefix.'_';
        $this->overrideTransactionReference = $overrideRefWithPrefix;
        // create a log channel
        $log = new Logger('flutterwave/rave');
        $this->logger = $log;
        $log->pushHandler(new RotatingFileHandler('rave.log', 90, Logger::DEBUG));

        $this->createReferenceNumber();
        
        // if($this->env === 'staging'){
        //     $this->baseUrl = $this->stagingUrl;
        // }elseif($this->env === 'live'){
        //     $this->baseUrl = $this->liveUrl;
        // }else{
        //     $this->baseUrl = $this->stagingUrl;
        // }

        // set the baseurl
        $this->baseUrl = $this->liveUrl;
        
        $this->logger->notice('Rave Class Initializes....');
        return $this;
    }
    
     /**
     * Generates a checksum value for the information to be sent to the payment gateway
     * @return object
     * */
    function createCheckSum(){
        $this->logger->notice('Generating Checksum....');
        $options = array( 
            "PBFPubKey" => $this->publicKey, 
            "amount" => $this->amount, 
            "customer_email" => $this->customerEmail, 
            "customer_firstname" => $this->customerFirstname, 
            "txref" => $this->txref, 
            "payment_options" => $this->paymentOptions, 
            "customer_lastname" => $this->customerLastname, 
            "country" => $this->country, 
            "currency" => $this->currency, 
            "custom_description" => $this->customDescription, 
            "custom_logo" => $this->customLogo, 
            "custom_title" => $this->customTitle, 
            "customer_phone" => $this->customerPhone,
            "pay_button_text" => $this->payButtonText,
            "redirect_url" => $this->redirectUrl,
            "hosted_payment" => 1
        );
        
        ksort($options);
        
        $this->transactionData = $options;
        
        $hashedPayload = '';
        
        foreach($options as $key => $value){
            $hashedPayload .= $value;
        }
        $completeHash = $hashedPayload.$this->secretKey;
        $hash = hash('sha256', $completeHash);
        
        $this->integrityHash = $hash;
        return $this;
    }

    /**
     * Generates a transaction reference number for the transactions
     * @return object
     * */
    function createReferenceNumber(){
        $this->logger->notice('Generating Reference Number....');
        if($this->overrideTransactionReference){
            $this->txref = $this->transactionPrefix;
        }else{
            $this->txref = uniqid($this->transactionPrefix);
        }
        $this->logger->notice('Generated Reference Number....'.$this->txref);
        return $this;
    }
    
    /**
     * gets the current transaction reference number for the transaction
     * @return string
     * */
    function getReferenceNumber(){
        return $this->txref;
    }
    
    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    function setAmount($amount){
        $this->amount = $amount;
        return $this;
    }

    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    function setAccount($account){
        $this->account = $account;
        return $this;
    }
    /**
     * Sets the transaction amount
     * @param integer $amount Transaction amount
     * @return object
     * */
    function setAccountNumber($accountno){
        $this->accountno = $accountno;
        return $this;
    }

    /**
     * Sets the transaction transaction card number
     * @param integer $card_no Transaction card number
     * @return object
     * */
    function setCardNo($card_no){
        $this->card_no = $card_no;
        return $this;
    }

    /**
     * Sets the transaction transaction CVV
     * @param integer $CVV Transaction CVV
     * @return object
     * */
    function setCVV($cvv){
        $this->cvv = $cvv;
        return $this;
    }
    /**
     * Sets the transaction transaction expiry_month
     * @param integer $expiry_month Transaction expiry_month
     * @return object
     * */
    function setExpiryMonth($expiry_month){
        $this->expiry_month= $expiry_month;
        return $this;
    }

    /**
     * Sets the transaction transaction expiry_year
     * @param integer $expiry_year Transaction expiry_year
     * @return object
     * */
    function setExpiryYear($expiry_year){
        $this->expiry_year = $expiry_year;
        return $this;
    }
    /**
     * Sets the transaction transaction end point
     * @param string $end_point Transaction expiry_year
     * @return object
     * */
    function setEndPoint($end_point){
        $this->end_point = $end_point;
        return $this;
    }


     /**
     * Sets the transaction authmodel
     * @param string $authmodel 
     * @return object
     * */
    function setAuthModel($authmodel){
        $this->authModelUsed = $authmodel;
        return $this;
    }
    
    
    /**
     * gets the transaction amount
     * @return string
     * */
    function getAmount(){
        return $this->amount;
    }
    
    /**
     * Sets the allowed payment methods
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both 
     * @return object
     * */
    function setPaymentOptions($paymentOptions){
        $this->paymentOptions = $paymentOptions;
        return $this;
    }
    
    /**
     * gets the allowed payment methods
     * @return string
     * */
    function getPaymentOptions(){
        return $this->paymentOptions;
    }
    
    /**
     * Sets the transaction description
     * @param string $customDescription The description of the transaction
     * @return object
     * */
    function setDescription($customDescription){
        $this->customDescription = $customDescription;
        return $this;
    }
    
    /**
     * gets the transaction description
     * @return string
     * */
    function getDescription(){
        return $this->customDescription;
    }
    
    /**
     * Sets the payment page logo
     * @param string $customLogo Your Logo
     * @return object
     * */
    function setLogo($customLogo){
        $this->customLogo = $customLogo;
        return $this;
    }
    
    /**
     * gets the payment page logo
     * @return string
     * */
    function getLogo(){
        return $this->customLogo;
    }
    
    /**
     * Sets the payment page title
     * @param string $customTitle A title for the payment. It can be the product name, your business name or anything short and descriptive 
     * @return object
     * */
    function setTitle($customTitle){
        $this->customTitle = $customTitle;
        return $this;
    }
    
    /**
     * gets the payment page title
     * @return string
     * */
    function getTitle(){
        return $this->customTitle;
    }
    
    /**
     * Sets transaction country
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     * @return object
     * */
    function setCountry($country){
        $this->country = $country;
        return $this;
    }
    
    /**
     * gets the transaction country
     * @return string
     * */
    function getCountry(){
        return $this->country;
    }
    
    /**
     * Sets the transaction currency
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
     * @return object
     * */
    function setCurrency($currency){
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * gets the transaction currency
     * @return string
     * */
    function getCurrency(){
        return $this->currency;
    }
    
    /**
     * Sets the customer email
     * @param string $customerEmail This is the paying customer's email
     * @return object
     * */
    function setEmail($customerEmail){
        $this->customerEmail = $customerEmail;
        return $this;
    }
    
    /**
     * gets the customer email
     * @return string
     * */
    function getEmail(){
        return $this->customerEmail;
    }
    
    /**
     * Sets the customer firstname
     * @param string $customerFirstname This is the paying customer's firstname
     * @return object
     * */
    function setFirstname($customerFirstname){
        $this->customerFirstname = $customerFirstname;
        return $this;
    }
    
    /**
     * gets the customer firstname
     * @return string
     * */
    function getFirstname(){
        return $this->customerFirstname;
    }
    
    /**
     * Sets the customer lastname
     * @param string $customerLastname This is the paying customer's lastname
     * @return object
     * */
    function setLastname($customerLastname){
        $this->customerLastname = $customerLastname;
        return $this;
    }
    
    /**
     * gets the customer lastname
     * @return string
     * */
    function getLastname(){
        return $this->customerLastname;
    }
    
    /**
     * Sets the customer phonenumber
     * @param string $customerPhone This is the paying customer's phonenumber
     * @return object
     * */
    function setPhoneNumber($customerPhone){
        $this->customerPhone = $customerPhone;
        return $this;
    }
    
    /**
     * gets the customer phonenumber
     * @return string
     * */
    function getPhoneNumber(){
        return $this->customerPhone;
    }
    
    /**
     * Sets the payment page button text
     * @param string $payButtonText This is the text that should appear on the payment button on the Rave payment gateway.
     * @return object
     * */
    function setPayButtonText($payButtonText){
        $this->payButtonText = $payButtonText;
        return $this;
    }
    
    /**
     * gets payment page button text
     * @return string
     * */
    function getPayButtonText(){
        return $this->payButtonText;
    }
    
    /**
     * Sets the transaction redirect url
     * @param string $redirectUrl This is where the Rave payment gateway will redirect to after completing a payment
     * @return object
     * */
    function setRedirectUrl($redirectUrl){
        $this->redirectUrl = $redirectUrl;
        return $this;
    }
    
    /**
     * gets the transaction redirect url
     * @return string
     * */
    function getRedirectUrl(){
        return $this->redirectUrl;
    }
    
    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     * @param array $meta This are the other information you will like to store with the transaction. It is a key => value array. eg. PNR for airlines, product colour or attributes. Example. array('name' => 'femi')
     * @return object
     * */
    function setMetaData($meta){
        array_push($this->meta, $meta);
        return $this;
    }
    
    /**
     * gets the transaction meta data
     * @return string
     * */
    function getMetaData(){
        return $this->meta;
    }
    
    /**
     * Sets the event hooks for all available triggers
     * @param object $handler This is a class that implements the Event Handler Interface
     * @return object
     * */
    function eventHandler($handler){
        $this->handler = $handler;
        return $this;
    }
    
    /**
     * Requerys a previous transaction from the Rave payment gateway
     * @param string $referenceNumber This should be the reference number of the transaction you want to requery
     * @return object
     * */
    function requeryTransaction($referenceNumber){
        $this->txref = $referenceNumber;
        $this->requeryCount++;
        $this->logger->notice('Requerying Transaction....'.$this->txref);
        if(isset($this->handler)){
            $this->handler->onRequery($this->txref);
        }

        $data = array(
            'txref' => $this->txref,
            'SECKEY' => $this->secretKey,
            'last_attempt' => '1'
            // 'only_successful' => '1'
        );

        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json');
        $body = Body::json($data);
        $url = $this->baseUrl.'/flwv3-pug/getpaidx/api/xrequery';

        // Make `POST` request and handle response with unirest
        $response = Request::post($url, $headers, $body);
  
        //check the status is success
        if ($response->body && $response->body->status === "success") {
            if($response->body && $response->body->data && $response->body->data->status === "successful"){
               $this->logger->notice('Requeryed a successful transaction....'.json_encode($response->body->data));
                // Handle successful
                if(isset($this->handler)){
                    $this->handler->onSuccessful($response->body->data);
                }
            }elseif($response->body && $response->body->data && $response->body->data->status === "failed"){
                // Handle Failure
                $this->logger->warn('Requeryed a failed transaction....'.json_encode($response->body->data));
                if(isset($this->handler)){
                    $this->handler->onFailure($response->body->data);
                }
            }else{
                // Handled an undecisive transaction. Probably timed out.
                $this->logger->warn('Requeryed an undecisive transaction....'.json_encode($response->body->data));
                // I will requery again here. Just incase we have some devs that cannot setup a queue for requery. I don't like this.
                if($this->requeryCount > 4){
                    // Now you have to setup a queue by force. We couldn't get a status in 5 requeries.
                    if(isset($this->handler)){
                        $this->handler->onTimeout($this->txref, $response->body);
                    }
                }else{
                   $this->logger->notice('delaying next requery for 3 seconds');
                    sleep(3);
                   $this->logger->notice('Now retrying requery...');
                    $this->requeryTransaction($this->txref);
                }
            }
        }else{
           // $this->logger->warn('Requery call returned error for transaction reference.....'.json_encode($response->body).'Transaction Reference: '. $this->txref);
            // Handle Requery Error
            if(isset($this->handler)){
                $this->handler->onRequeryError($response->body);
            }
        }
        return $this;
    }
    
    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway
     * @return string
     * */
    function initialize(){
        $this->createCheckSum();
        $this->transactionData = array_merge($this->transactionData, array('integrity_hash' => $this->integrityHash), array('meta' => $this->meta));
        
        $json = json_encode($this->transactionData);
        echo '<html>';
        echo '<body>';
        echo '<center>Proccessing...<br /><img src="ajax-loader.gif" /></center>';
        echo '<script type="text/javascript" src="'.$this->baseUrl.'/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>';
        echo '<script>';
	    echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'var data = JSON.parse(\''.$json.'\');';
        echo 'getpaidSetup(data);';
        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';
        return $json;
    }

    /**
     * this is the getKey function that generates an encryption Key for you by passing your Secret Key as a parameter.
     * @param string
     * @return string
     * */
    
    function getKey($seckey){
        $hashedkey = md5($seckey);
        $hashedkeylast12 = substr($hashedkey, -12);

        $seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
        $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

        $encryptionkey = $seckeyadjustedfirst12.$hashedkeylast12;
        return $encryptionkey;

    }

    /**
     * this is the encrypt3Des function that generates an encryption Key for you by passing your transaction Data and Secret Key as a parameter.
     * @param string
     * @return string
     * */

    function encrypt3Des($data, $key)
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData);
    }
    /**
     * this is the encryption function that combines the getkey() and encryptDes().
     * @param string
     * @return string
     * */

    function encryption($options){
         //encrypt and return the key using the secrekKey
         $this->key = $this->getkey($this->secretKey);
         //set the data to transactionData
         $this->transactionData = $options;
         //encode the data and the 
        return $this->encrypt3Des( $this->transactionData,  $this->key);
    }

     /**
     * makes a post call to the api 
     * @param array
     * @return object
     * */

    function postURL($data){
        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json');
        $body = Body::json($data);
        $url = $this->baseUrl.'/'.$this->end_point;
        $response = Request::post($url, $headers, $body);
        return $response->raw_body;    // Unparsed body
     }

     
     /**
     * makes a get call to the api 
     * @param array
     * @return object
     * */

     function getURL($url){
        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json');
        $body = Body::json($data);
        $path = $this->baseUrl.'/'.$this->end_point;
        $response = Request::get($path.$url, $headers);
        return $response->raw_body;    // Unparsed body
     }
     /**
     * verify the transaction before giving value to your customers
     *  @param string
     *  @return object
     * */
    function verifyTransaction($txRef, $seckey){
        $this->logger->notice('Verifying transaction...');
        $this->setEndPoint("flwv3-pug/getpaidx/api/v2/verify");
        $this->post_data =  array( 
            'txref' => $txRef,
            'SECKEY' => $seckey
            );
            $result  = $this->postURL($this->post_data);
            $result = json_decode($result,true);
        return $result;
      
    }


     /**
     * Validate the transaction to be charged
     *  @param string
     *  @return object
     * */
    function validateTransaction($otp){
        if(isset($this->authModelUsed)){
            if($this->authModelUsed === "PIN" ){
                $this->logger->notice('Validating otp...');
                $this->setEndPoint("flwv3-pug/getpaidx/api/validatecharge");
                $this->post_data = array(
                    'PBFPubKey' => $this->publicKey,
                    'transaction_reference' => $this->flwRef,
                    'otp' => $otp);
                $result  = $this->postURL($this->post_data);
                return $result;

            }elseif($this->authModelUsed === "VBVSECURECODE"){
                $this->logger->notice('VBVSECURECODE...');
              //Validation for foreign cards
              return "Please validate using the authUrl";
            }elseif($this->authModelUsed === "AUTH"){
                $this->logger->notice('Validating otp...');
                $this->setEndPoint("flwv3-pug/getpaidx/api/validate");
                $this->post_data = array(
                    'PBFPubKey' => $this->publicKey,
                    'transactionreference' => $this->flwRef,
                    'otp' => $otp);
                $result  = $this->postURL($this->post_data);
                return $result;

            }else{
                $this->logger->error('You have not charged this transaction...');
            }
            
        }
    }
     /**
     * Validating your bvn
     *  @param string
     *  @return object
     * */

    function bvn($bvn){
        $this->logger->notice('Validating bvn...');
        $url = "/".$bvn."?seckey=".$this->secretKey;
        return $this->getURL($url);
     } 

     /**
     * Get all Subscription
     *  @return object
     * */

    function getAllSubscription(){
        $this->logger->notice('Getting all Subscription...');
        $url = "?seckey=".$this->secretKey;
        return $this->getURL($url);
     } 

        /**
     * Get all Subscription
     * @param $id,$email
     *  @return object
     * */

    function fetchASubscription($email){
        $this->logger->notice('Fetching a Subscription...');
        $url = "?seckey=".$this->secretKey."&".$email;
        return $this->getURL($url);
     } 

      /**
     * activating  a subscription
     *  @return object
     * */

    function activateSubscription(){
        $this->logger->notice('Activating Subscription...');
        $data = array(
            "seckey"=>$this->secretKey
        );
        return $this->postURL($data);
     } 

      /**
     * Creating a payment plan
     *  @param array
     *  @return object
     * */

    function createPlan($array){
        $this->logger->notice('Creating Payment Plan...');
        return $this->postURL($array);
     } 

       /**
     * Creating a beneficiaries
     *  @param array
     *  @return object
     * */

    function beneficiary($array){
        $this->logger->notice('Creating beneficiaries ...');
        return $this->postURL($array);
     }

     /**
     * transfer payment api 
     *  @param array
     *  @return object
     * */

     function transferSingle($array){
        $this->logger->notice('Processing transfer...');
         return $this->postURL($array);
         
     }


     /**
     * bulk transfer payment api 
     *  @param array
     *  @return object
     * */

    function transferBulk($array){
        $this->logger->notice('Processing bulk transfer...');
         return $this->postURL($array);
         
     }

      /**
     * Refund payment api 
     *  @param array
     *  @return object
     * */

    function refund($array){
        $this->logger->notice('Initiating a refund...');
         return $this->postURL($array);
         
     }

    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway api
     *  @param array
     *  @return object
     * */

     function chargePayment($array){
        $this->options = $array;
        $this->json_options = json_encode($this->options);
        
        $this->logger->notice('Checking payment details..');
        //encrypt the required options to pass to the server
        $this->integrityHash = $this->encryption($this->json_options);

        $this->post_data = array(
            'PBFPubKey' => $this->publicKey,
            'client' => $this->integrityHash,
            'alg' => '3DES-24');

        $result  = $this->postURL($this->post_data);
        
        $this->logger->notice('Payment requires validation..'); 
        // the result returned requires validation
        $result = json_decode($result, true);

        if(isset($result["data"]["authModelUsed"])){
            $this->logger->notice('Payment requires otp validation...');
            $this->authModelUsed = $result["data"]["authModelUsed"];
            $this->flwRef = $result["data"]["flwRef"];
            $this->txRef = $result["data"]["txRef"];
       }
        //passes the result to the suggestedAuth function which re-initiates the charge 
        return $result;
     } 
     /**
     * sends a post request to the virtual APi set by the user
     *  @param array
     *  @return object
     * */

     function vcPostRequest($array){
        $this->post_data = $array;
        //post the data to the API
        $result  = $this->postURL($this->post_data);
        //decode the response 
        $result = json_decode($result, true);
        //return result
        print_r($result);
       // return $result;
     }   
    /**
         * Used to create sub account on the rave dashboard
         *  @param array
         *  @return object
         * */
     function createSubaccount($array){
        $this->options = $array;
        $this->logger->notice('Creating Sub account...');
        //pass $this->options to the postURL function to call the api
        $result  = $this->postURL($this->options);
        return $result;
     }

    /**
     * Handle canceled payments with this method
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     * @return object
     * */
    function paymentCanceled($referenceNumber){
        $this->txref = $referenceNumber;
        $this->logger->notice('Payment was canceled by user..'.$this->txref);
        if(isset($this->handler)){
            $this->handler->onCancel($this->txref);
        }
        return $this;
    }
    
}

// silencio es dorado


