<?php
var_dump($_GET);
include('includes/config.php'); 
$_SESSION['paypal_message'] = 'You cancelled your paypal transaction, click <a href="indx.php">here</a>to try again.';
actionPaypalCancel();
header('location: complete.php');
exit();
