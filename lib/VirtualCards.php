<?php

namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');

use Flutterwave\Rave;

class VirtualCard
{
    protected $vc;
    //initialise the constructor
    function __construct()
    {
        $this->vc = new Rave($_ENV['PUBLIC_KEY'], $_ENV['SECRET_KEY'], $_ENV['ENV']);
    }
    //create card function
    function create($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/new");
            return $this->vc->vcPostRequest($array);
    }
    //get the detials of a card using the card id
    function get($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/get");
            return $this->vc->vcPostRequest($array);
    }
    //list all the virtual cards on your profile
    function list($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/search");
            return $this->vc->vcPostRequest($array);
    }
    //terminate a virtual card on your profile
    function terminate($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/" . $array['id'] . "/terminate");
            return $this->vc->vcPostRequest($array);
    }
    //fund a virtual card
    function fund($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/fund");
            return $this->vc->vcPostRequest($array);
    }
   // list card transactions
    function transactions($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/transactions");
            return $this->vc->vcPostRequest($array);
    }
    //withdraw funds from card
    function withdraw($array)
    {
            //set the endpoint for the api call
            $this->vc->setEndPoint("v2/services/virtualcards/withdraw");
            return $this->vc->vcPostRequest($array);
    }
}
