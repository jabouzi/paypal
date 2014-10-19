<?php

include('includes/config.php');

$_SESSION = $_POST;
$billing_id = save_billing_address();
if (isset($_SESSION['PayerAddress']['shipping'])) $shipping_id = $billing_id;
else $shipping_id = save_shipping_address();
save_order($billing_id, $shipping_id, $_SESSION['Creditcard']['cardType'], $_SESSION['Creditcard']['Price']);
var_dump(actionConfirm());
