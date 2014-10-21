<?php

include('includes/config.php'); 

if (isset($_GET['token'])) 
{
	actionPaypalCancel($_GET['token']);
}

header('Location: '.$configuration['siteurl'].'index.php');
exit();
