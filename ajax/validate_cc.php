<?php
include('../includes/luhn.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
{
	$date1 = new DateTime(date("Y-m-d"));
	$year = $_GET['year'];
	$year = $_GET['month'];
	$date2 = new DateTime(date($_GET['year'].'-'.$_GET['month'].'-t'));
	$validcc = luhn_check($_GET['cc']);
	if ($date2 >= $date1 && $validcc) echo 0;
	else if ($date1 > $date2 && !$validcc) echo 1;
	else if ($date1 > $date2) echo 2; 
	else echo 3; 
}
else
{
	echo 'This is not ajax request!';
}
