<?php

// visa 4214022891893180  9/2019 
// editionstgi XYZG45ASvMmBPwYA

function get_configs($type = 'live')
{
	$configuration['live']['paypal'] = array(
			'environment' => 'live',
			'APIUsername' => 'cla_api1.sajy.com',
			'APIPassword' => '2F7VKX4UJTLSSTCX',
			'APISignature' => 'ACArS-gGNkbPwGsoAWrQDy4FY1VhAYVA3pnIb-Y7OOcF6Xh4GEAs4dWY',
			'APICertificate' => null
	);
	$configuration['sandbox']['paypal']	 = array(
			'environment' => 'sandbox',
			'APIUsername' => 'cla-facilitator_api1.sajy.com',
			'APIPassword' => '1410979375',
			'APISignature' => 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AXyjSzOSp58ksAMSKEjGJ..jvQ6r',
			'APICertificate' => null
	);
	
	return $configuration[$type];
}

function get_url($type = 'live')
{
	$paypalUrl = array(
		'live' => 'https://www.paypal.com/',
		'sandbox' => 'https://www.sandbox.paypal.com/'
	);
	
	return $paypalUrl[$type];
}

function parseToPaypal($type)
{
    $configuration = get_configs();
    require_once 'SimplePaypalOrder.php';

    $paypal = new SimplePaypalOrder($configuration);
    $options = array(
        'PayerAddress' => array(
            'Street1' => '',
            'Street2' => '',
            'CityName' => '',
            'StateOrProvince' => '',
            'Country' => '',
            'PostalCode' => '',
        ),
        'ShippingAddress' => array(
            'Street1' => '',
            'Street2' => '',
            'CityName' => '',
            'StateOrProvince' => '',
            'Country' => '',
            'PostalCode' => '',
        ),
        'Firstname' => '',
        'Lastname' => '',
    );

    $paypal->setOptions($options);
    
	$prix = '90.00';
	$tps = tps($prix);
	$tvq = tvq($prix);
	//$amount = tax($prix, $tps, $tvq);
    $amount = '0.01';   
    if  ($type == 'numerique') {
		$prix = '25.00';
		$tps = tps($prix);
		$tvq = tvq($prix);
		//$amount = tax($prix, $tps, $tvq);
		$amount = '0.01';        
    }

    $paypal->addItem($type, $amount, 1, 'Livre', 1, 0);
    return $paypal;
}

function sendPaypalRequest($type)
{
    global $data;
    $errors = array();
    
	$prix = '90.00';
	$tps = tps($prix);
	$tvq = tvq($prix);
	//$amount = tax($prix, $tps, $tvq);
    $amount = '0.01';   
    $shipping = '00.00';
    $noShipping = 0;
    if  ($type == 'numerique') {
		$prix = '25.00';
		$tps = tps($prix);
		$tvq = tvq($prix);
		//$amount = tax($prix, $tps, $tvq);
		$amount = '0.01';
		$shipping = '00.00'; 
		$noShipping = 1;
    }
    
    $paypal = parseToPaypal($type);
    $paypal->returnURL = "https://editionstgi.dev.tgiprojects.com/return.php";
    $paypal->cancelURL = "https://editionstgi.dev.tgiprojects.com/cancel.php";
    $paypal->custom = $_SESSION['uid'];
    $paypal->shippingTotal = (double)$shipping;
    $paypal->noShipping = $noShipping;
    $paypal->amount = (double)$amount;
    $paypal->description = 'L\'éducation physique, une histoire de cœur et de passion';
    $paypal->logoimg = 'https://editionstgi.dev.tgiprojects.com/wp-content/themes/default/img/template/logo_college-de-lassomption_paypal.png';
    
    if ($paypal->sendExpressCheckoutRequest(false)) {
        return $paypal->token;
    } else {
        return $paypal->getErrors();
    }
    return $errors;
}

function getPaypalErrors($errorsCodes)
{
    global $data;
    $errors = array();
    foreach ($errorsCodes as $code) {
        if (!is_numeric($code)) continue;
        $code = (int)$code;
        $errors[] = $data['paypal_'.$code];
    }
    
    if (count($errors) == 0) {
        $errors[] = $data['transaction_failed'];
    }
    
    return $errors;
}

function actionConfirmer($type)
{
    global $data;
    $errors = array();
   
	$result = sendPaypalRequest($type);

	if (is_array($result)) {
		$errors[] = $data['transaction_not_processed'];
		return false;
	} 
	else 
	{
		$token = $result;
		update_order('token', $token);
		$paypalUrl = get_url().'webscr&cmd=_express-checkout&useraction=commit&token=' . $token;
	}
	
	return $paypalUrl;
}

function actionPaypalReturn($token, $transactionId)
{
    global $data;
    $errors = array();

    if ($token) {
		$order_total = get_order_value('order_total', $token);
		$type = get_order_value('order_description', $token);
        $paypal = parseToPaypal($type);
        $result = $paypal->confirmExpressCheckout(false, $order_total, $token);
        if (is_array($result)) {
            update_order('status', 'Refused');
            $errors[] = 'transaction_failed';
        } else {
            $transactionId = $result;
            update_order('transaction_id', $transactionId, $token);
            update_order('status', 'Completed', $token);
        }
    } else {
        $errors[] = $data['order_not_found'];
    }
    
    clean_order();
    header('location: /confirmation.php?'.$transactionId);
	exit;
    
    if (count($errors) == 0) {
        return true;
    } else {
        return false;
    }
}

function actionPaypalCancel($token)
{
	if ($token)
	{
		update_order('status', 'Cancelled', $token);
		clean_order();
	}
	
	header('location: /');
	exit;
}

function generate_guid()
{
    $uid = uniqid('', true);
    $data = get_user_ip();
    $hash = hash('sha256', $uid . md5($data));
    return $hash;
}


function save_order($type)
{
	global $db;
	$prix = '90.00';
	$tps = tps($prix);
	$tvq = tvq($prix);
	//$amount = tax($prix, $tps, $tvq);
	$amount = '0.01';   
    if  ($type == 'numerique') {
		$prix = '25.00';
		$tps = tps($prix);
		$tvq = tvq($prix);
		//$amount = tax($prix, $tps, $tvq);
		$amount = '0.01';
    }
    
    $args = array(
		':guid' => $_SESSION['uid'],
		':transaction_id' => '',
		':token' => '',
		':order_total' => $amount ,
		':order_description' => $type,
		':status' => '',
		':paypal_error_codes' => '',
		':creation_date' => date('Y-m-d H:i:s')
	);
   
    $query = "REPLACE INTO orders(guid,transaction_id,token,order_total,order_description,status,paypal_error_codes,creation_date) 
			VALUES (:guid,:transaction_id,:token,:order_total,:order_description,:status,:paypal_error_codes,:creation_date)";
    $db->query($query, $args);
}

function update_order($champ, $value, $token = 0)
{
	global $db;
	if ($token)
	{
		$args = array(
			':token' => $token,
			':'.$champ => $value,
		);
		$query = "UPDATE orders SET {$champ} = :{$champ} WHERE token = :token";
	}
	else
	{
		$args = array(
			':guid' => $_SESSION['uid'],
			':'.$champ => $value,
		);
		$query = "UPDATE orders SET {$champ} = :{$champ} WHERE guid = :guid";
	}
    
    $db->query($query, $args);
}

function get_order_value($champ, $token = 0)
{
	global $db;
	if ($token)
	{
		$args = array(
			':token' => $token
		);
		$query = "SELECT {$champ} FROM orders WHERE token = :token";
	}
	else
	{
		$args = array(
			':guid' => $_SESSION['uid']
		);
		$query = "SELECT {$champ} FROM orders WHERE guid = :guid";
	}
    
    $order_data = $db->query($query, $args);
    return $order_data[0][$champ];
}

function get_download($transaction_id)
{
	global $db;
	if ($transaction_id)
	{
		$args = array(
			':transaction_id' => $transaction_id
		);
		$query = "SELECT download FROM orders WHERE transaction_id = :transaction_id";
	}
    
    $order_data = $db->query($query, $args);
    return $order_data[0]['download'];
}

function get_type($transaction_id)
{
	global $db;
	if ($transaction_id)
	{
		$args = array(
			':transaction_id' => $transaction_id
		);
		$query = "SELECT order_description FROM orders WHERE transaction_id = :transaction_id";
	}
    
    $order_data = $db->query($query, $args);
    if (empty($order_data)) return true;
    return $order_data[0]['order_description'];
}

function update_download($transaction_id)
{
	global $db;
	if ($transaction_id)
	{
		$args = array(
			':transaction_id' => $transaction_id,
			':download' => 1,
		);
		$query = "UPDATE orders SET download = :download WHERE transaction_id = :transaction_id";
	}
	else
	{
		$args = array(
			':guid' => $_SESSION['uid'],
			':'.$champ => $value,
		);
		$query = "UPDATE orders SET {$champ} = :{$champ} WHERE guid = :guid";
	}
    
    $db->query($query, $args);
}

function clean_order()
{
    unset($_SESSION['uid']);
    unset($_SESSION['token']);
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

function tps($total)
{
	return $total*0.05;
}

function tvq($total)
{
	return $total*0.09975;
}

function tax($total, $tps, $tvq)
{
	return $total + $tps + $tvq;
}

function format_price($price)
{
	return number_format($price, 2, ',', ' ');
}
