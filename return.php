<?php 
var_dump($_GET);
include('includes/config.php');

$token = mysql_real_escape_string($_REQUEST['token']);
$query = "SELECT * FROM t_order WHERE token = '{$token}'";
$res = mysql_query($query);
$order = mysql_fetch_assoc($res);
$errors = actionPaypalReturn(array(get_order_sum(), $_REQUEST['token']);
