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
	'APIUsername' => '',
	'APIPassword' => '',
	'APISignature' => '',
	'APICertificate' => null,
	'paypalUrl' => 'https://www.paypal.com/',
);
$configuration['sandbox']['paypal']	 = array(
	'environment' => 'sandbox',
	'APIUsername' => 'jabouzi_api1.gmail.com',
	'APIPassword' => '4X6MBWTCUJSVL7JS',
	'APISignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31As0cm3KQl7X48szW.6PrWXS8NxCU',
	'APICertificate' => null,
	'paypalUrl' => 'https://www.sandbox.paypal.com/',
);
$configuration['noShipping'] = 0;
$configuration['description'] = 0;
$configuration['logoimg'] = 0;
