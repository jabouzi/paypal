<?php

include('includes/config.php');

$_SESSION = $_POST;
$billing_id = save_billing_address();
if (isset($_SESSION['PayerAddress']['shipping'])) $shipping_id = $billing_id;
else $shipping_id = save_shipping_address();
save_order($billing_id, $shipping_id, $_SESSION['Creditcard']['CardType'], $_SESSION['Creditcard']['Price'], $_SESSION['Creditcard']['Shipping']);
$_SESSION['result'] = actionConfirm();
$_SESSION['result'];
$template = file_get_contents('templates/email.txt');
$message = sprintf($template, $_SESSION['result'], money_format('%n', ($_SESSION['Creditcard']['Price']+$_SESSION['Creditcard']['Shipping'])));
var_dump(send_email($_SESSION['PayerAddress']['email'], $message));
//header('Location: '.$configuration['siteurl'].'complete.php');
//exit();
