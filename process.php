<?php

//var_dump($_POST);
include('includes/config.php');

$billing_id = save_billing_address();
if (isset($_POST['shipping'])) $shipping_id = $billing_id;
else $shipping_id = save_shipping_address();
var_dump($shipping_id, $billing_id);
//save_order($_POST);
//actionConfirmer($_GET['type']);

//array (size=3)
  //'PayerAddress' => 
    //array (size=18)
      //'cardType' => string 'paypal' (length=6)
      //'first_name' => string '' (length=0)
      //'last_name' => string '' (length=0)
      //'street1' => string '' (length=0)
      //'street2' => string '' (length=0)
      //'country' => string '1019' (length=4)
      //'state_or_province' => string '1019' (length=4)
      //'city_name' => string '' (length=0)
      //'postal_code' => string '' (length=0)
      //'shipping' => string '1' (length=1)
      //'shipping_street1' => string '' (length=0)
      //'shipping_street2' => string '' (length=0)
      //'shipping_country' => string '1001' (length=4)
      //'shipping_state_or_province' => string '1001' (length=4)
      //'shipping_city_name' => string '' (length=0)
      //'shipping_postal_code' => string '' (length=0)
      //'phone' => string '51411122222' (length=11)
      //'email' => string 'aa@aa.com' (length=9)
  //'Creditcard' => 
    //array (size=6)
      //'Price' => string '10.00' (length=5)
      //'CreditCardNumber' => string '' (length=0)
      //'ExpMonth' => string '' (length=0)
      //'ExpYear' => string '' (length=0)
      //'CVV2' => string '' (length=0)
      //'CardOwner' => string '' (length=0)
  //'bt_submit' => string '' (length=0)
