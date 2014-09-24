<?php
session_start();

include_once('db.php');
include_once('functions.php');

if (!isset($_SESSION['uid'])) $_SESSION['uid'] = generate_guid();

$db = Database::getInstance();
$db->setHost('209.104.115.222');
$db->setUsername('editionstgi');
$db->setPassword('XYZG45ASvMmBPwYA');
$db->setDatabase('editionstgi');
$db->setPort();
$db->connect(); 
$db->query("SET NAMES 'utf8'",'');
