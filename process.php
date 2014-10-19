<?php

include('includes/config.php');

$_SESSION = $_POST;
$billing_id = save_billing_address();
if (isset($_SESSION['PayerAddress']['shipping'])) $shipping_id = $billing_id;
else $shipping_id = save_shipping_address();
save_order($billing_id, $shipping_id, $_SESSION['Creditcard']['CardType'], $_SESSION['Creditcard']['Price']);
$_SESSION['result'] = actionConfirm();
$_SESSION['result'];
header('Location: '.$configuration['siteurl'].'complete.php');
exit();
