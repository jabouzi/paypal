<?php
session_start();

include_once('db.php');
include_once('functions.php');

if (!isset($_SESSION['uid'])) $_SESSION['uid'] = generate_guid();

$db = Database::getInstance();
$db->setHost('localhost');
$db->setUsername('root');
$db->setPassword('7024043');
$db->setDatabase('paypal');
$db->setPort();
$db->connect(); 
$db->query("SET NAMES 'utf8'",'');

$configuration['live']['paypal'] = array(
	'environment' => 'live',
	'APIUsername' => 'cla_api1.sajy.com',
	'APIPassword' => '2F7VKX4UJTLSSTCX',
	'APISignature' => 'ACArS-gGNkbPwGsoAWrQDy4FY1VhAYVA3pnIb-Y7OOcF6Xh4GEAs4dWY',
	'APICertificate' => null,
	'paypalUrl' => 'https://www.paypal.com/',
);
$configuration['sandbox']['paypal']	 = array(
	'environment' => 'sandbox',
	'APIUsername' => 'cla-facilitator_api1.sajy.com',
	'APIPassword' => '1410979375',
	'APISignature' => 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AXyjSzOSp58ksAMSKEjGJ..jvQ6r',
	'APICertificate' => null,
	'paypalUrl' => 'https://www.sandbox.paypal.com/',
);
$configuration['noShipping'] = 0;
$configuration['description'] = 0;
$configuration['logoimg'] = 0;
