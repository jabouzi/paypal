<?php 

include('includes/config.php');

if (isset($_GET['token'])) 
{
	$_SESSION['result'] = actionPaypalReturn($_GET['token'], $_GET['PayerID']);
}

header('Location: '.$configuration['siteurl'].'complete.php');
exit();
