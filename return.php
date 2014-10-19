<?php 

include('includes/config.php');

if (isset($_GET['token'])) 
{
	actionPaypalReturn($_GET['token'], $_GET['PayerID']);
}
