<?php

require_once 'PayPal.php';
require_once 'Multi.php';
require_once 'PayPal/Profile/Handler/Array.php';
require_once 'PayPal/Profile/API.php';
require_once 'PayPal/Type/DoDirectPaymentRequestType.php';
require_once 'PayPal/Type/DoDirectPaymentRequestDetailsType.php';
require_once 'PayPal/Type/DoDirectPaymentResponseType.php';
require_once 'PayPal/Type/SetExpressCheckoutRequestType.php';
require_once 'PayPal/Type/SetExpressCheckoutRequestDetailsType.php';
require_once 'PayPal/Type/SetExpressCheckoutResponseType.php';
require_once 'Network.php';

class PaypalOrder
{
	private $profile;
	private $options;
	private $request;
	private $charset = 'UTF-8';
	private $items = array();
	private $itemTotal = 0.0;
	private $errors = array();
	public $transactionID;
	public $token;
	private $configuration = array();
	public function __construct($configuration)
	{
		$this->configuration = $configuration;
		@$this->populateApiCredentials();
		$this->buildInitialOptionsStructure();
	}

	public function sendDirectPaymentRequest($authorization = true, Array $options = null)
	{
		if ($options != null) {
			$this->setOptions($options);
		}

		$this->buildDirectPaymentRequest($authorization);

		$caller =& PayPal::getCallerServices($this->profile);

		$isError = true;
		if (method_exists($caller, 'DoDirectPayment')) {
			$response = $caller->DoDirectPayment($this->request);
			$isError = PayPal::isError($response);
			$this->buildErrors($response);
		}

		if (!$isError && $response->getAck() == 'Success') {
			$this->transactionID = $response->TransactionID ;
			return true;
		} else {
			$this->transactionID = -1;
			return false;
		}
	}

	public function sendExpressCheckoutRequest($authorization = true, Array $options = null)
	{
		if ($options != null) {
			$this->setOptions($options);
		}

		$options = array(
			'PAYMENTREQUEST_0_AMT' => $this->amount,
			'PAYMENTREQUEST_0_PAYMENTACTION' => ($authorization) ? 'Authorization' : 'Sale',
			'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currencyID,
			'NOSHIPPING' => $this->noShipping,
			'ReturnUrl' => $this->returnURL,
			'CANCELURL' => $this->cancelURL,
			'LOGOIMG' => $this->logoimg,
		);

		if ($this->shippingTotal > 0) {
			$options = array_merge($options, array(
				'PAYMENTREQUEST_0_SHIPPINGAMT' => $this->shippingTotal,
				'PAYMENTREQUEST_0_ITEMAMT' => $this->formatAmount($this->itemTotal),
			));
		}

		if ($this->noShipping == '0') {
			$options = array_merge($options, array(
				'ADDROVERRIDE' => $this->addressOverride,
				'PAYMENTREQUEST_0_SHIPTOSTREET' => $this->ShippingAddress['Street1'],
				'PAYMENTREQUEST_0_SHIPTOSTREET2' => $this->ShippingAddress['Street2'],
				'PAYMENTREQUEST_0_SHIPTOCITY' => $this->ShippingAddress['CityName'],
				'PAYMENTREQUEST_0_SHIPTOSTATE' => $this->ShippingAddress['StateOrProvince'],
				'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $this->ShippingAddress['Country'],
				'PAYMENTREQUEST_0_SHIPTOZIP' => $this->ShippingAddress['PostalCode'],
			));
		}

		foreach ($this->items as $i => $item) {
			$options['L_PAYMENTREQUEST_0_NAME' . $i] = $item->getName();
			$options['L_PAYMENTREQUEST_0_NUMBER' . $i] = $item->getNumber();
			$options['L_PAYMENTREQUEST_0_DESC' . $i] = $item->getDescription();
			$options['L_PAYMENTREQUEST_0_AMT' . $i] = $item->getAmount()->getval();
			$options['L_PAYMENTREQUEST_0_QTY' . $i] = $item->getQuantity();
		}

		$nvpStr = '';
		foreach ($options as $key => $value) {
			$nvpStr .= '&' . $key . '=' . urlencode($value);
		}

		$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $nvpStr);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				$this->token = urldecode($httpParsedResponseAr["TOKEN"]);
				return true;
		} else  {
				$this->errors[] = 'SetExpressCheckout failed: ' . urldecode(print_r($httpParsedResponseAr, true));
				return false;
		}
	}

	public function confirmExpressCheckout($authorization = true, $amount = 0, $token = '')
	{
		$amount = $this->formatAmount($amount);
		$nvpStr = '&TOKEN=' . $token;
		$httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $nvpStr);
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$payerID = $httpParsedResponseAr['PAYERID'];
			$options = array(
				'TOKEN' => $token,
				'PAYERID' => $payerID,
				'PAYMENTREQUEST_0_AMT' => $amount,
				'PAYMENTREQUEST_0_PAYMENTACTION' => ($authorization) ? 'Authorization' : 'Sale',
				'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currencyID,
			);

			$nvpStr = '';
			foreach ($options as $key => $value) {
				$nvpStr .= '&' . $key . '=' . urlencode($value);
			}

			$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $nvpStr);

			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
					return $httpParsedResponseAr['PAYMENTINFO_0_TRANSACTIONID'];
			} else  {
					$this->error[] = 'DoExpressCheckoutPayment failed: ' . print_r($httpParsedResponseAr, true);
			}
		} else {
			$this->errors[] = print_r($httpParsedResponseAr, true);
		}
		return $this->errors;
	}

	private function buildDirectPaymentRequest($authorization = true)
	{
		$dp_request =& PayPal::getType('DoDirectPaymentRequestType');
		$dp_request->setVersion('51.0', $this->charset);

		$paymentType = ($authorization) ? 'Authorization' : 'Sale';

		$OrderTotal =& PayPal::getType('BasicAmountType');
		$OrderTotal->setattr('currencyID', $this->currencyID);
		$OrderTotal->setval($this->amount, $this->charset);

		$ShippingTotal =& PayPal::getType('BasicAmountType');
		$ShippingTotal->setattr('currencyID', $this->currencyID);
		$ShippingTotal->setval($this->shippingTotal, $this->charset);

		$shipTo =& PayPal::getType('AddressType');
		$shipTo->setName($this->Firstname + ' ' + $this->Lastname);
		$shipTo->setName($this->Firstname + ' ' + $this->Lastname);
		$shipTo->setStreet1($this->ShippingAddress['Street1']);
		$shipTo->setStreet2($this->ShippingAddress['Street2']);
		$shipTo->setCityName($this->ShippingAddress['CityName']);
		$shipTo->setStateOrProvince($this->ShippingAddress['StateOrProvince']);
		$shipTo->setCountry($this->ShippingAddress['Country']);
		$shipTo->setPostalCode($this->ShippingAddress['PostalCode']);

		$payerAddress =& PayPal::getType('AddressType');
		$payerAddress->setName($this->Firstname + ' ' + $this->Lastname);
		$payerAddress->setStreet1($this->PayerAddress['Street1']);
		$payerAddress->setStreet2($this->PayerAddress['Street2']);
		$payerAddress->setCityName($this->PayerAddress['CityName']);
		$payerAddress->setStateOrProvince($this->PayerAddress['StateOrProvince']);
		$payerAddress->setCountry($this->PayerAddress['Country']);
		$payerAddress->setPostalCode($this->PayerAddress['PostalCode']);

		$PaymentDetails =& PayPal::getType('PaymentDetailsType');
		$PaymentDetails->setOrderTotal($OrderTotal);
		$PaymentDetails->setShippingTotal($ShippingTotal);
		$PaymentDetails->setShipToAddress($shipTo);
		if (count($this->items) > 0) {
			$m = new MultiOccurs($PaymentDetails, 'PaymentDetailsItem');
			$m->setChildren($this->items);
			$PaymentDetails->setPaymentDetailsItem($m);
			$ItemTotal =& PayPal::getType('BasicAmountType');
			$ItemTotal->setattr('currencyID', $this->currencyID);
			$ItemTotal->setval($this->formatAmount($this->itemTotal), $this->charset);
			$PaymentDetails->setItemTotal($ItemTotal, $this->charset);
		}

		$PaymentDetails->setOrderDescription($this->description, $this->charset);

		$person_name =& PayPal::getType('PersonNameType');
		$person_name->setFirstName($this->ShippingAddress['Firstname']);
		$person_name->setLastName($this->ShippingAddress['Lastname']);

		$payer =& PayPal::getType('PayerInfoType');
		$payer->setPayerName($person_name);
		$payer->setPayerCountry($this->PayerAddress['Country']);
		$payer->setAddress($payerAddress);

		$card_details =& PayPal::getType('CreditCardDetailsType');
		$card_details->setCardOwner($payer);
		$card_details->setCreditCardType($this->CreditCardType);
		$card_details->setCreditCardNumber($this->CreditCardNumber);
		$card_details->setExpMonth($this->ExpMonth);
		$card_details->setExpYear($this->ExpYear);
		$card_details->setCVV2($this->CVV2);

		$dp_details =& PayPal::getType('DoDirectPaymentRequestDetailsType');
		$dp_details->setPaymentDetails($PaymentDetails);
		$dp_details->setCreditCard($card_details);
		$dp_details->setIPAddress(Network::visitorIpAddress());
		$dp_details->setPaymentAction($paymentType);

		$dp_request->setDoDirectPaymentRequestDetails($dp_details);

		$this->request = $dp_request;
	}

	public function addItem($name, $amount, $quantity = 1, $description = '', $number = '', $tax = 0.0)
	{
		@$itemAmount =& PayPal::getType('BasicAmountType');
		$itemAmount->setattr('currencyID', $this->currencyID);
		$itemAmount->setval($this->formatAmount($amount), $this->charset);

		@$itemTax =& PayPal::getType('BasicAmountType');
		$itemTax->setattr('currencyID', $this->currencyID);
		$itemTax->setval($this->formatAmount($tax), $this->charset);

		@$item =& PayPal::getType('PaymentDetailsItemType');
		$item->setName($name, $this->charset);
		$item->setQuantity($quantity, $this->charset);
		$item->setDescription($description, $this->charset);
		$item->setNumber($number, $this->charset);
		$item->setTax($itemTax, $this->charset);
		$item->setAmount($itemAmount, $this->charset);

		$this->items[] = $item;
		$this->itemTotal += $amount * $quantity + $tax;
	}

	public function setOptions(Array $options)
	{
		foreach ($options as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __set($name, $value)
	{
		if (isSet($this->options[$name])) {
			switch ($name) {
				case 'ExpMonth' : $value = str_pad($value, 2, '0', STR_PAD_LEFT); break;
				case 'ExpYear' : $value = (strlen($value) == 2) ? '20' . $value : $value; break;
				case 'amount' : $value = $this->formatAmount($value); break;
				case 'shippingTotal' : $value = $this->formatAmount($value); break;
				case 'noShipping':
				case 'addressOverride':
					if (!$value || $value === '0') {
						$value = '0';
					} else {
						$value = '1';
					}
					break;
			}
			$this->options[$name] = $value;
		}
	}

	public function __get($name)
	{
		if (isSet($this->options[$name])) {
			return $this->options[$name];
		}
		return '';
	}

	private function populateApiCredentials()
	{
		$environment = $this->configuration['paypal']['environment'];
		$APIUsername = $this->configuration['paypal']['APIUsername'];
		$APIPassword = $this->configuration['paypal']['APIPassword'];
		$APISignature = $this->configuration['paypal']['APISignature'];
		$APICertificate = $this->configuration['paypal']['APICertificate'];

		$handler = & ProfileHandler_Array::getInstance(array(
					'username' => $APIUsername,
					'certificateFile' => null,
					'subject' => null,
					'environment' => $environment));

		$pid = ProfileHandler::generateID();

		$this->profile = new APIProfile($pid, $handler);
		$this->profile->setAPIUsername($APIUsername);
		$this->profile->setAPIPassword($APIPassword);
		$this->profile->setSignature($APISignature);
		//$this->profile->setCertificateFile($APICertificate);
		$this->profile->setEnvironment($environment);
	}

	private function buildInitialOptionsStructure()
	{
		$this->options = array(
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
			'CardOwner' => '',
			'CreditCardType' => '',
			'CreditCardNumber' => '',
			'ExpMonth' => '',
			'ExpYear' => '',
			'CVV2' => '',
			'currencyID' => 'CAD',
			'amount' => 0,
			'shippingTotal' => 0,
			'description' => '',
			'custom' => '',
			'invoiceID' => '',
			'returnURL' => '',
			'cancelURL' => '',
			'noShipping' => '1',
			'addressOverride' => '1',
			'logoimg' => ''
		);
	}

	private function formatAmount($amount)
	{
		if (!is_numeric($amount)) return '';
		return number_format($amount, 2, '.', ',');
	}

	private function buildErrors(&$response)
	{
		if (method_exists($response, 'getErrors')) {
			$errors = $response->getErrors();
			if ($errors != null) {
				if (!is_array($errors)) $errors = array($errors);

				foreach ($errors as $e) {
					$this->errors[$e->getErrorCode()] = $e->getLongMessage();
				}
			}
		} elseif (method_exists($response, 'getFault')) {
			$errorFault = $response->getFault();
			$this->errors[$errorFault->faultcode] = $errorFault->faultstring;
		}
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getErrorsCodes()
	{
		$codes = array();
		foreach ($this->errors as $c => $e) {
			$codes[] = $c;
		}
		return $codes;
	}

	private function PPHttpPost($methodName_, $nvpStr_) {
		$environment = $this->configuration['paypal']['environment'];
		$APIUsername = $this->configuration['paypal']['APIUsername'];
		$APIPassword = $this->configuration['paypal']['APIPassword'];
		$APISignature = $this->configuration['paypal'][ 'APISignature'];
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
				$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
		} else {
			$API_Endpoint = "https://api-3t.paypal.com/nvp";
		}
		$version = urlencode('81.0');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$APIPassword&USER=$APIUsername&SIGNATURE=$APISignature$nvpStr_";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		$httpResponse = curl_exec($ch);
		
		if(!$httpResponse) {
				$this->errors[] = "$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')';
				Error::getInstance()->log("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).') \n');
				return false;
		}
		
		$httpResponseAr = explode("&", $httpResponse);
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
						$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				$this->errors[] = "Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.";
				return false;
		}

		return $httpParsedResponseAr;
	}
}
