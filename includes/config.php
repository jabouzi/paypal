<?php
session_start();

$root = $_SERVER['DOCUMENT_ROOT'].'/';
$root = str_replace('//', '/', $root);
ini_set('include_path',ini_get('include_path').':'.$root.'lib/:'.$root.'lib/Paypal/:');

include_once('db.php');
include_once('functions.php');

if (!isset($_SESSION['uid'])) $_SESSION['uid'] = generate_guid();

$db = Database::getInstance();
if (strstr($_SERVER['HTTP_HOST'], 'skanderjabouzi.com'))
{
	$db->setHost('localhost');
	$db->setUsername('jabouzic_db');
	$db->setPassword('7024043');
	$db->setDatabase('jabouzic_paypal');
}
else
{
	$db->setHost('localhost');
	$db->setUsername('root');
	$db->setPassword('7024043');
	$db->setDatabase('paypal');
}
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
