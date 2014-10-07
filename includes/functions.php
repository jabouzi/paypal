<?php

function get_coutries()
{
	$countries = file_get_contents('assets/countries.json');
	return json_decode($countries);
}

function get_states($country)
{
	$states = file_get_contents('assets/states.json');
	return json_decode($states[$country]);
}

function get_configs($type = 'sandbox')
{
	global $configuration;
	return $configuration[$type];
}

function parseToPaypal()
{
    global $configuration;
    require 'PaypalOrder.php';
    
    if (isset($_POST['shipping'])) $payerAddress = $shippingAddress;

    $paypal = new SimplePaypalOrder($configuration);
    $obtions = array(
        'PayerAddress' => array(
            'Street1' => $payerAddress['street1'],
            'Street2' => $payerAddress['street2'],
            'CityName' => $payerAddress['city_name'],
            'StateOrProvince' => $payerAddress['state_or_province'],
            'Country' => $payerAddress['country'],
            'PostalCode' => $payerAddress['postal_code'],
        ),
        'ShippingAddress' => array(
            'Street1' => $shippingAddress['street1'],
            'Street2' => $shippingAddress['street2'],
            'CityName' => $shippingAddress['city_name'],
            'StateOrProvince' => $shippingAddress['state_or_province'],
            'Country' => $shippingAddress['country'],
            'PostalCode' => $shippingAddress['postal_code'],
        ),
        'Firstname' => $payerAddress['first_name'],
        'Lastname' => $payerAddress['last_name'],
    );
    if ($_SESSION['same_as_shipping_address']) {
        $obtions['PayerAddress'] = $obtions['ShippingAddress'];
        $obtions['Firstname'] = $shippingAddress['first_name'];
        $obtions['Lastname'] = $shippingAddress['last_name'];
    }
    $paypal->setOptions($obtions);
    
    $number = 1;
    foreach ($_SESSION['cart'] as $amount => $quanity) {
        $paypal->addItem('giftcard_'.$amount, $amount, $quanity, 'Cartes cadeaux.', $number++, 0);
    }
    return $paypal;
}

function sendCreditRequest()
{
    $errors = array();

    $shipping_total = 0;
    if ($_SESSION['shipping_address']['shipping_method'] == 'recommanded_parcel') $shipping_total = 13.8;
    else if ($_SESSION['shipping_address']['shipping_method'] == 'regular_post') $shipping_total = 5.0;
     
    $paypal = parseToPaypal();
    $paypal->CardOwner = $_POST['CardOwner'];
    $paypal->CreditCardType = $_POST['CreditCardType'];
    $paypal->CreditCardNumber = $_POST['CreditCardNumber'];
    $paypal->ExpMonth = $_POST['ExpMonth'];
    $paypal->ExpYear = $_POST['ExpYear'];
    $paypal->CVV2 = $_POST['CVV2'];
    $paypal->shippingTotal = (double)$shipping_total;
    $paypal->amount = (double)get_order_sum() + (double)$shipping_total;
    $paypal->description = 'Cartes cadeaux.';
    $result = $paypal->sendDirectPaymentRequest(false);
    if ($result) {
        $_SESSION['lastCreditCardDigit'] = substr($paypal->CreditCardNumber, -4); // 4 derniers digits
        update_order('lastCreditCardDigit', $_SESSION['lastCreditCardDigit']);
        return $paypal->transactionID;
    } else {
        $errorsCodes = $paypal->getErrorsCodes();
        update_order('paypal_error_codes', implode(', ', $errorsCodes));
        $errors = getPaypalErrors($errorsCodes);
    }
    
    return $errors;
}

function sendPaypalRequest()
{
    global $data;
    $errors = array();
    $shipping_total = 0;
    if ($_SESSION['shipping_address']['shipping_method'] == 'recommanded_parcel') $shipping_total = 13.8;
    else if ($_SESSION['shipping_address']['shipping_method'] == 'regular_post') $shipping_total = 5.0;
    
    $paypal = parseToPaypal();
    $paypal->returnURL = rewrite_url(520);
    $paypal->cancelURL = rewrite_url(521);
    $paypal->custom = $_SESSION['uid'];
    $paypal->shippingTotal = (double)$shipping_total;
    $paypal->amount = (double)get_order_sum() + (double)$shipping_total;
    $paypal->description = 'Cartes cadeaux.';
    
    if ($paypal->sendExpressCheckoutRequest(false)) {
        return $paypal->token;
    } else {
        $errors[] = $data['error_transaction'];
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

function actionConfirmer()
{
    global $data;
    $errors = array();

    if ($_SESSION['shipping_method']['cardType'] == 'MasterCard' ||
        $_SESSION['shipping_method']['cardType'] == 'Visa') {
        
        $guid = $_SESSION['uid'];
        
        if (!isset($_SESSION['orderWaiting']) && get_order_count() > 0) {
            $_SESSION['orderWaiting'] = true;
            update_order('status', 'Waiting');
            $result = sendCreditRequest();
            if (is_array($result)) {
                update_order('status', 'Refused');
                $errors = $result;
            } else {
                $transactionId = $result;
                $_SESSION['transaction_id'] = $transactionId;
                update_order('transaction_id', $transactionId);
                update_order('status', 'Completed');
                sendConfirmationEmail($guid);
            }
        } else {
			$errors[] = $data['transaction_treated'];
        }
    } 
    else 
    {
        $result = sendPaypalRequest();
        //echo '<pre>';
        //print_r($result);
        //echo '<pre>';
        if (is_array($result)) {
            $errors[] = $data['transaction_not_processed'];
        } else 
        {
            $token = $result;
            update_order('token', $token);
            clean_order();
            $paypalUrl = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=' . $token;
            header('Location: '.$paypalUrl);
            exit();
        }
    }
    
    if (count($errors) == 0) {
        $transctionId = $_SESSION['transaction_id'];
        clean_order();
        header('Location: '.rewrite_url(473).$transctionId.'/');
        exit();
    } else {
        $_SESSION['orderWaiting'] = true;
    }
    
    return $errors;
}

function actionPaypalReturn($order)
{
    global $data;
    $errors = array();

    if ($order != null) {
        $paypal = parseToPaypal();
        $result = $paypal->confirmExpressCheckout(false, $order['order_total'], $order['token']);
        if (is_array($result)) {
            update_order('status', 'Refused');
            $errors[] = $data['transaction_failed'];
        } else {
            $transactionId = $result;
            update_order('transaction_id', $transactionId, $order['guid']);
            update_order('status', 'Completed', $order['guid']);
            sendConfirmationEmail($order['guid']);
        }
    } else {
        $errors[] = $data['order_not_found'];
    }
    
    if (count($errors) == 0) {
        header('Location: '.rewrite_url(473).$transactionId.'/');
        exit();
    } else {
        return $errors;
    }
}

function actionPaypalCancel($order)
{
    if ($order != null) {
        update_order('status', 'Cancelled');
    }
    
    header('Location: '.rewrite_url(468));
    exit();
}

function sendConfirmationEmail($guid)
{
    global $lang, $data;
    
    $guid = mysql_real_escape_string($guid);
    $query = "SELECT * FROM t_order WHERE guid = '{$guid}'";
    $res = mysql_query($query);
    $order = mysql_fetch_assoc($res);

    $query = "SELECT * FROM t_order_item WHERE order_guid = '{$guid}'";
    $res = mysql_query($query);
    while($temp = mysql_fetch_assoc($res))
    {
        $order_items[] = $temp;
    }
    
    $query = "SELECT * FROM t_address WHERE id = '{$order['payer_address_id']}'";
    $res = mysql_query($query);
    $order_address = mysql_fetch_assoc($res);
    
    $query = "SELECT * FROM t_address WHERE id = '{$order['shipping_address_id']}'";
    $res = mysql_query($query);
    $shipping_address = mysql_fetch_assoc($res);
    
    $configuration = array('email', array(
        'outgoing' => array(
            'transport' => 'sendmail', // smtp or sendmail or mail
            'parameters' => array(
                'path' => '/usr/sbin/sendmail', // pour sendmail, pas nécessaire pour smtp
            ),
            'fromName' => 'skyspa.ca',
            'fromAddress' => 'info@skyspa.ca',
        ),
        'adminAddress' => 'info@skyspa.ca',
    ));
    
    require_once ROOT.'includes/SimpleMailer.php';
    require_once ROOT.'includes/SimpleTranslate.php';
    require_once ROOT.'includes/SimpleTemplate.php';
    
	// Envoi du email
	$emailConfig = $configuration['email']['outgoing'];
	$mailer = new SimpleMailer(SimpleMailer::OUTGOING_SERVER);
	$adminMailer = new SimpleMailer(SimpleMailer::OUTGOING_SERVER);
	
	$message = new SimpleMessage();
	$adminMessage = new SimpleMessage();
	$message->setSubject('Confirmation d\'achat');
	$adminMessage->setSubject('Confirmation d\'achat');
	$message->setFrom('info@skyspa.ca', 'skyspa.ca');
	$adminMessage->setFrom('info@skyspa.ca', 'skyspa.ca');
	if ($order['shipping_method'] == 'email') {
		$message->setTo($order_address['email']);
	} else {
		$message->setTo($shipping_address['email']);
	}
	$adminMessage->setTo('info@skyspa.ca');
	
	// Gestion des templates
	$htmlContent = file_get_contents(ROOT . 'templates' . '/' . $lang . '/email.html');
	$adminHtmlContent = file_get_contents(ROOT . 'templates' . '/' . $lang . '/adminEmail.html');
	$plainContent = file_get_contents(ROOT . 'templates' . '/' . $lang .  '/email.txt');
	$adminPlainContent = file_get_contents(ROOT . 'templates' . '/' . $lang . '/adminEmail.txt');
	
	$templateHtml = new SimpleTemplate($htmlContent);
	$adminTemplateHtml = new SimpleTemplate($adminHtmlContent);
	$templatePlainText = new SimpleTemplate($plainContent);
	$adminTemplatePlainText = new SimpleTemplate($adminPlainContent);

	$itemsList = '';
	foreach ($order_items as $item) {
        if ($item['quantity'] > 1) $itemsList .= sprintf($data['gift_card'], $item['quantity'], SimpleTranslate::formatNumber($item['amount'], $lang));
        else $itemsList .= sprintf($data['gift_card'], $item['quantity'], SimpleTranslate::formatNumber($item['amount'], $lang));
	}
	if ($order['shipping_method'] == 'recommanded_parcel') {
		$itemsList .= sprintf($data['delivry_fee'], SimpleTranslate::formatNumber($order['shipping_total'], $lang));
	}
	if ($order['shipping_method'] == 'regular_post') {
		$itemsList .= sprintf($data['delivry_fee'], SimpleTranslate::formatNumber($order['shipping_total'], $lang));
	}
	
	switch ($order['shipping_method']) {
		case 'regular_post': 
            if ($nbItems > 1) $shippingRelatedMessage = $data['email_message2'];
            else $shippingRelatedMessage = $data['email_message1'];
			$shippingMethod = $data['reg_mail'];
			break;
		case 'recommanded_parcel': 
			if ($nbItems > 1) $shippingRelatedMessage = $data['email_message4'];
			else $shippingRelatedMessage = $data['email_message3'];
            $shippingMethod = $data['par_mail'];
			break;
		case 'email': 
			if ($nbItems > 1) $shippingRelatedMessage = $data['email_message6'];
			else $shippingRelatedMessage = $data['email_message5'];
            $shippingMethod = $data['email_mail'];
			break;
	}
	if ($order['shipping_method'] == 'email') {
		$shippingAddress = 'courriel: ' . $shipping_address['email']; 
	} else {
		$shippingAddress = 'Nom: ' .$shipping_address['first_name'] . ' ' . 
			$shipping_address['last_name'] . '<br />';
		$shippingAddress .= 'Adresse: ' . $shipping_address['street1'] . ' ' .
			$shipping_address['street2'] . '<br />' . $shipping_address['city_name'] . ', ' .
			$shipping_address['state_or_province'] . ', ' . $shipping_address['postal_code'] . '<br />';
	}
	
	if ($_SESSION['token'] != '') {
		$payerAddress = $order_address['phone'] . '<br />';
		$payerAddress .= 'Courriel : ' . $order_address['email']. '<br /><br />';
	} else {
		$payerAddress = 'Nom: ' .$order_address['first_name'] . ' ' . 
			$order_address['last_name'] . '<br />';
		$payerAddress .= 'Adresse: ' . $order_address['street1'] . ' ' .
			$order_address['street2'] . '<br />' . $order_address['city_name'] . ', ' .
			$order_address['state_or_province'] . ', ' . $order_address['postal_code'] . '<br />';
		$payerAddress .= $order_address['phone'] . '<br />';
		$payerAddress .= 'Courriel : ' . $order_address['email'] . '<br /><br />';
	}
	
	if (isset($order['lastCreditCardDigit']) && strlen($order['lastCreditCardDigit']) == 4) {
		$creditCard = 'xxxx-xxxx-xxxx-' . $order['lastCreditCardDigit'] . '<br />';
	} else {
		$creditCard = 'Payé par paypal';
	}
    
	$str =  $data['one_card'];
    if ($nbItems > 1) $str = $data['many_cards'];
	$variables = array(
		'User' => $order_address['first_name'] . ' ' . 
			$order_address['last_name'],
		'urlRoot' => SITE_URL,
		'transactionId' => $order['transaction_id'],
		'itemsList' => $itemsList,
		'orderTotal' => sprintf($data['text_format'],  SimpleTranslate::formatNumber(get_order_sum($order_items), $lang)),
		'shippingRelatedMessage' => $shippingRelatedMessage,
        'productType' => $str
	);
	$adminVariables = array_merge($variables, array(
		'shippingMethod' => $shippingMethod,
		'shippingAddress' => $shippingAddress,
		'payerAddress' => $payerAddress,
		'creditcard' => $creditCard,
		
	));
	$templatePlainText->prepareContent($variables);
	$adminTemplatePlainText->prepareContent($adminVariables);
	$templateHtml->prepareContent($variables);
	$adminTemplateHtml->prepareContent($adminVariables);
	
	$message->setPlainTextBody($templatePlainText->getContent());
	$adminMessage->setPlainTextBody($adminTemplatePlainText->getContent());
	$message->setHtmlBody($templateHtml->getContent());
	$adminMessage->setHtmlBody($adminTemplateHtml->getContent());
	
	if ($order['shipping_method'] == 'email') {
		$attachments = array();
		$cpt = 1;
		foreach ($order_items as $item) {
			for ($i=1;$i<=$item['quantity'];$i++) {
				$id = str_pad($cpt, 2, '0', STR_PAD_LEFT);
				$attachments[] = createPdf($item['amount'], $id, $order);
				$cpt++;
			}
		}
		$message->setAttachments($attachments);
		$adminMessage->setAttachments($attachments);
	}
	if (!$mailer->send($message)) {
		//Error::getInstance()->log('Mailer: ' . $mailer->getError() . '\n');
		$errors[] = $data['error_mail'];
		//$this->saveInSession();
	}
	if (!$adminMailer->send($adminMessage)) {
		//Error::getInstance()->log('Mailer: ' . $adminMailer->getError() . '\n');
	}
	
	$mailer->disconnect();
	$adminMailer->disconnect();
	//if (isSet($attachments) && count($attachments) > 0) {
		//foreach ($attachments as $a) {
			//@unlink($a);
		//}
	//}
}

function createPdf($sum, $id, $order)
{
    global $lang;
    require_once ROOT.'includes/SimplePDF.php';
	$price = $sum;
	$transaction_id = $order['transaction_id'] . '-' . $id;
	$unique = sha1(uniqid(time()));
	$date = time(); //optional . Today as default.
	$pdfFilename = '/tmp/certificate_' . $sum . '_' . $unique . '.pdf';
	
	$pdf = new SimplePDF($lang, $price, $transaction_id, $date);
	$pdf->distiller();
	$pdf->Output($pdfFilename, 'F');
    
	return $pdfFilename;
}

function get_order_sum($order_items = array())
{
    $sum = 0;
    if (!count($order_items))
    {
        if (isset($_SESSION['cart']))
        {
            foreach($_SESSION['cart'] as $key => $order)
            {
                $sum = $sum + ((double)$key * (double)$order);
            } 
        }
    }
    else
    {
        foreach($order_items as $item)
        {
            $sum = $sum + ((double)$item['amount'] * (double)$item['quantity']);
        } 
    }
    
    return $sum;
}

function get_order_count($order_items = array())
{
    $count = 0;
    if (!count($order_items))
    {
        if (isset($_SESSION['cart']))
        {
            foreach($_SESSION['cart'] as $key => $order)
            {
                $count = $count + intval($order);
            } 
        }
    }
    else
    {
        foreach($order_items as $item)
        {
            $count = $count + intval($item['quantity']);
        } 
    }
    
    return $count;
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
	$data = $_POST['PayerAddress'];
	$countries = (array)get_coutries();
	$states = (array)get_states($data['shipping_country']);
	$args = array(
		':first_name' => $data['shipping_first_name'],
		':last_name' => $data['shipping_last_name'],
		':street1' => $data['shipping_street1'],
		':street2' => $data['shipping_street2'],
		':city_name' => $data['shipping_city_name'],
		':state_or_province' => $states[$data['shipping_state_or_province']][0],
		':country' => $countries[$data['shipping_country']][1],
		':postal_code' => $data['shipping_postal_code'],
		':phone' => $data['shipping_phone'],
		':email' => $data['shipping_email'],
		':shipping' => isset($data['shipping'])
	);

    $query = "INSERT INTO address VALUES (''
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
		:shipping,
		''
	)";
	
    $db->query($query, $args);      
    return mysql_insert_id();
}

function save_billing_address()
{
	global $db;
	$data = $_POST['PayerAddress'];
	$countries = (array)get_coutries();
	$states = (array)get_states($data['country']);
	var_dump($data['country'], $data['state_or_province']);
	var_dump($countries, $states);
	$args = array(
		':first_name' => $data['first_name'],
		':last_name' => $data['last_name'],
		':street1' => $data['street1'],
		':street2' => $data['street2'],
		':city_name' => $data['city_name'],
		':state_or_province' => $states[$data['state_or_province']][0],
		':country' => $countries[$data['country']][1],
		':postal_code' => $data['postal_code'],
		':phone' => $data['phone'],
		':email' => $data['email'],
		':shipping' => isset($data['shipping'])
	);

    $query = "INSERT INTO address VALUES (''
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
		:shipping,
		''
	)";
	var_dump($args);
    $db->query($query, $args);      
    return mysql_insert_id();
}

function update_order($champ, $value, $uid = 0)
{
    $guid = $uid;
    if (!$uid) $guid = mysql_real_escape_string($_SESSION['uid']);
    $champ = mysql_real_escape_string($champ);
    $value = mysql_real_escape_string($value);
    $query = "UPDATE t_order SET {$champ} = '{$value}' WHERE guid = '{$guid}'";
    mysql_query($query);
}

function get_order_value($champ)
{
    $guid = mysql_real_escape_string($_SESSION['uid']);
    $champ = mysql_real_escape_string($champ);
    $query = "SELECT {$champ} FROM t_order WHERE guid = '{$guid}'";
    $res = mysql_query($query);
    $order_data = mysql_fetch_assoc($res);
    return $order_data[$champ];
}

function save_items()
{
    foreach($_SESSION['cart'] as $key => $item)
    {
        $query = "INSERT INTO t_order_item VALUE ('',
            '".mysql_real_escape_string($_SESSION['uid'])."',
            '".mysql_real_escape_string('giftcard_'.$key)."',
            '".mysql_real_escape_string($key)."',
            '0',
            '',
            '".mysql_real_escape_string($item)."',
            '".date('Y-m-d H:i:s')."',
            '".date('Y-m-d H:i:s')."')";
        mysql_query($query);  
    }
}

function clean_order()
{
    unset($_SESSION['cart']);
    unset($_SESSION['shipping_method']);
    unset($_SESSION['shipping_address']);
    unset($_SESSION['uid']);
    unset($_SESSION['same_as_shipping_address']);
    unset($_SESSION['lastCreditCardDigit']);
    unset($_SESSION['token']);
    unset($_SESSION['orderWaiting']);
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
