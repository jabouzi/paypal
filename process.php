<?php

//var_dump($_POST);
include('includes/config.php');

$paypal_data = array (
  'PayerAddress' => 
    array (size=17)
      'cardType' => string 'MasterCard' (length=10)
      'first_name' => string '' (length=0)
      'last_name' => string '' (length=0)
      'street1' => string '' (length=0)
      'street2' => string '' (length=0)
      'country' => string '1039' (length=4)
      'state_or_province' => string 'Alberta' (length=7)
      'city_name' => string '' (length=0)
      'postal_code' => string '' (length=0)
      'shipping_street1' => string '' (length=0)
      'shipping_street2' => string '' (length=0)
      'shipping_country' => string '1218' (length=4)
      'shipping_state_or_province' => string 'BÃ©ja' (length=5)
      'shipping_city_name' => string '' (length=0)
      'shipping_postal_code' => string '' (length=0)
      'phone' => string '' (length=0)
      'email' => string '' (length=0)
  'Creditcard' => 
    array (size=5)
      'Price' => string '10.00' (length=0)
      'CreditCardNumber' => string '' (length=0)
      'ExpMonth' => string '' (length=0)
      'ExpYear' => string '' (length=0)
      'CVV2' => string '' (length=0)
      'CardOwner' => string '' (length=0)
);
