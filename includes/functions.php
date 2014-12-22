<?php

function get_coutries()
{
	$countries = file_get_contents('assets/countries.json');
	return json_decode($countries, true);
}

function get_states($country)
{
	$states = file_get_contents('assets/states.json');
	return json_decode($states[$country]);
}

function parseToPaypal()
{
    global $configuration;
    require 'PaypalOrder.php';
    
    $payerAddress = $_SESSION['PayerAddress'];
    if (isset($_SESSION['PayerAddress']['shipping']))  $shippingAddress = $payerAddress;
    else $shippingAddress = $_SESSION['ShippingAddress'];
    
    $countries = (array)get_coutries();

    $paypal = new PaypalOrder($configuration);
    if ($_SESSION['Creditcard']['CardType'] != 'Paypal')
    {
		$options = array(
			'PayerAddress' => array(
				'Street1' => $payerAddress['street1'],
				'Street2' => $payerAddress['street2'],
				'CityName' => $payerAddress['city_name'],
				'StateOrProvince' => $payerAddress['state_or_province'],
				'Country' => $countries[$payerAddress['country']][2],
				'PostalCode' => $payerAddress['postal_code'],
			),
			'ShippingAddress' => array(
				'Street1' => $shippingAddress['street1'],
				'Street2' => $shippingAddress['street2'],
				'CityName' => $shippingAddress['city_name'],
				'StateOrProvince' => $shippingAddress['state_or_province'],
				'Country' => $countries[$shippingAddress['country']][2],
				'PostalCode' => $shippingAddress['postal_code'],
			),
			'Firstname' => $payerAddress['first_name'],
			'Lastname' => $payerAddress['last_name'],
		);

		$options['Firstname'] = $shippingAddress['first_name'];
		$options['Lastname'] = $shippingAddress['last_name'];
	
		//var_dump($_SESSION, $payerAddress, $options);

		$paypal->setOptions($options);
    }
    
    $amount = $_SESSION['Creditcard']['Price'];
    $paypal->addItem('testproduct_'.$amount, $amount, 1, 'Test Product.', 1, 0);

    return $paypal;
}

function sendCreditRequest()
{
    $errors = array();

    $shipping_total = $_SESSION['Creditcard']['Shipping'];
     
    $paypal = parseToPaypal();
    $paypal->CardOwner = $_SESSION['Creditcard']['CardOwner'];
    $paypal->CreditCardType = $_SESSION['Creditcard']['CardType'];
    $paypal->CreditCardNumber = $_SESSION['Creditcard']['CardNumber'];
    $paypal->ExpMonth = $_SESSION['Creditcard']['ExpMonth'];
    $paypal->ExpYear = $_SESSION['Creditcard']['ExpYear'];
    $paypal->CVV2 = $_SESSION['Creditcard']['CVV2'];
    $paypal->shippingTotal = (double)$shipping_total;
    $paypal->amount =  $_SESSION['Creditcard']['Price'] + (double)$shipping_total;
    $paypal->description = 'Test Product';
    $result = $paypal->sendDirectPaymentRequest(false);
    if ($result) 
    {
        update_order('lastCreditCardDigit', substr($paypal->CreditCardNumber, -4));
        return $paypal->transactionID;
    } 
    else 
    {
        $errorsCodes = $paypal->getErrorsCodes();
        update_order('paypal_error_codes', implode(', ', $errorsCodes));
        return $paypal->getErrors();
    }
    
    return $errors;
}

function sendPaypalRequest()
{
    global $data, $configuration;
    $errors = array();
    $shipping_total = $_SESSION['Creditcard']['Price'];
    
    $paypal = parseToPaypal();
    $paypal->returnURL = $configuration['siteurl']."return.php";
    $paypal->cancelURL = $configuration['siteurl']."cancel.php";
    $paypal->custom = $_SESSION['uid'];
    $paypal->shippingTotal = (double)$shipping_total;
    $paypal->amount = $_SESSION['Creditcard']['Price'] + (double)$shipping_total;
    $paypal->description = 'Test Product';
    $paypal->noShipping = 0;
    $paypal->description = 'Paypal Payments Demo Test';
    $paypal->logoimg = $configuration['siteurl'].'/images/simple_linux_logo_by_dablim-d5k4ghu_small.png';

    if ($paypal->sendExpressCheckoutRequest(false)) 
    {
        return $paypal->token;
    } 
    else 
    {
		$errorsCodes = $paypal->getErrorsCodes();
		update_order('paypal_error_codes', implode(', ', $errorsCodes));
        return $paypal->getErrors();
    }

}

function actionConfirm()
{    
    global $configuration;
    $errors = array();

    if ($_SESSION['Creditcard']['CardType'] == 'Paypal') 
    {
        $result = sendPaypalRequest();
        
        if (is_array($result)) 
        {
            update_order('status', 'Refused');
        } 
        else
        {
            $token = $result;
            update_order('token', $token);
            clean_order();
            $paypalUrl = $configuration['paypal']['paypalUrl'].'/webscr&cmd=_express-checkout&useraction=commit&token=' . $token;
            header('Location: '.$paypalUrl);
            exit();
        }
    } 
    else 
    {
        $guid = $_SESSION['uid'];
        
        if (!isset($_SESSION['orderWaiting']))
        {
            $_SESSION['orderWaiting'] = true;
            update_order('status', 'Waiting');
            $result = sendCreditRequest();
            if (is_array($result)) 
            {
                update_order('status', 'Refused');
            } 
            else 
            {
                $transactionId = $result;
                $_SESSION['transaction_id'] = $transactionId;
                update_order('transaction_id', $transactionId);
                update_order('status', 'Completed');
            }
        } 
        else 
        {
			$result[] = 'Transaction Treated';
        }
    }
    
    if (is_array($result)) 
    {
        $_SESSION['orderWaiting'] = true;
    }
    
    return $result;
}

function actionPaypalReturn($token, $transactionId)
{
    $errors = array();

    if ($token) 
    {
		$order_total = get_order_value('order_total', $token);
        $paypal = parseToPaypal();
        $result = $paypal->confirmExpressCheckout(false, $order_total, $token);

        if (is_array($result)) 
        {
            update_order('status', 'Refused');
        } 
        else 
        {
            $transactionId = $result;
            update_order('transaction_id', $transactionId, $token);
            update_order('status', 'Completed', $token);
        }
    } 
    
    return $result;
}

function actionPaypalCancel($token)
{
	if ($token)
	{
		update_order('status', 'Cancelled', $token);
	}
}

function send_email($email, $message)
{
	require 'mailer.php';
	try {
		$mailer = new Mailer();
		$mailer->setFrom("Paypal APP", "payement@yourwebsite.com");
		$mailer->addRecipient('', $email);
		$mailer->fillSubject('Your Paypal Payement Rrecieved');
		$mailer->fillMessage($message);
		$mailer->send();
	} catch (Exception $e) {
		echo $e->getMessage();
		exit(0);
	}
}

function generate_guid()
{
    $uid = uniqid('', true);
    $data = get_user_ip();
    $hash = hash('sha256', $uid . md5($data));
    return $hash;
}

function save_shipping_address()
{
	global $db;
	$data = $_SESSION['ShippingAddress'];
	$countries = (array)get_coutries();
	$args = array(
		':first_name' => '',
		':last_name' => '',
		':street1' => $data['street1'],
		':street2' => $data['street2'],
		':city_name' => $data['city_name'],
		':state_or_province' => $data['state_or_province'],
		':country' => $countries[$data['country']][1],
		':postal_code' => $data['postal_code'],
		':phone' => '',
		':email' => '',
		':shipping' => ''
	);

    $query = "INSERT INTO address (
			first_name,
			last_name,
			street1,
			street2,
			city_name,
			state_or_province,
			country,
			postal_code,
			phone,
			email,
			shipping
		) VALUES (
		:first_name,
		:last_name,
		:street1,
		:street2,
		:city_name,
		:state_or_province,
		:country,
		:postal_code,
		:phone,
		:email,
		:shipping
	)";
	
    $db->query($query, $args);      
    return $db->lastInsertId();
}

function save_billing_address()
{
	global $db;
	$data = $_SESSION['PayerAddress'];
	$countries = get_coutries();
	$args = array(
		':first_name' => $data['first_name'],
		':last_name' => $data['last_name'],
		':street1' => $data['street1'],
		':street2' => $data['street2'],
		':city_name' => $data['city_name'],
		':state_or_province' => $data['state_or_province'],
		':country' => $countries[$data['country']][1],
		':postal_code' => $data['postal_code'],
		':phone' => $data['phone'],
		':email' => $data['email'],
		':shipping' => isset($data['shipping'])
	);

    $query = "INSERT INTO address (
			first_name,
			last_name,
			street1,
			street2,
			city_name,
			state_or_province,
			country,
			postal_code,
			phone,
			email,
			shipping
		) VALUES (
			:first_name,
			:last_name,
			:street1,
			:street2,
			:city_name,
			:state_or_province,
			:country,
			:postal_code,
			:phone,
			:email,
			:shipping
		)";

    $db->query($query, $args);      
    return $db->lastInsertId();
}

function save_order($payerAddressId, $shippingAddressId, $cardType, $price, $shipping)
{
	global $db;
	$_SESSION['uid'] = generate_guid();
	$args = array(
		':uid' => $_SESSION['uid'],
		':transaction_id' => 0,
		':billing_id' => $payerAddressId,
		':shipping_id' => $shippingAddressId,
		':token' => '0',
		':order_total' => ($price + $shipping),
		':order_description' => 'Paypal_payement_'.$cardType,
		':status' => '',
		':paypal_error_codes' => '',
		':cardtype' => $cardType,
		':lastCreditCardDigit' => ''
	);

	$query = "INSERT INTO orders (
			uid,
			transaction_id,
			billing_id,
			shipping_id,
			token,
			order_total,
			order_description,
			status,
			paypal_error_codes,
			cardtype,
			lastCreditCardDigit
		) VALUES (
			:uid,
			:transaction_id,
			:billing_id,
			:shipping_id,
			:token,
			:order_total,
			:order_description,
			:status,
			:paypal_error_codes,
			:cardtype,
			:lastCreditCardDigit
		)";

    $db->query($query, $args);   
}

function update_order($champ, $value)
{
	global $db;
    $args = array(
		':uid' => $_SESSION['uid'],
		':'.$champ => $value
    );

    $query = "UPDATE orders SET {$champ} = :{$champ} WHERE uid = :uid";
    $db->query($query, $args);
}

function get_order_value($champ, $token)
{
	global $db;
    $args = array(
		'token' => $token,
    );
    $query = "SELECT {$champ} FROM orders WHERE token = :token";
    $res = $db->query($query, $args);

    return $res[0][$champ];
}

function clean_order()
{
	$_SESSION = array();
}

function get_user_ip()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}
