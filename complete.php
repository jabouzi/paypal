<?php include('includes/config.php'); var_dump($_SESSION); ?>
<?php if (!isset($_SESSION['result']))
{
	header('Location: '.$configuration['siteurl'].'index.php');
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="UTF-8">
		<meta http-equiv="cache-control" content="public">
		<meta http-equiv="expires" content="Fri, 30 Dec 2013 12:00:00 GMT">

		<title>Paypal Payments</title>
		<meta name="description" content="Shopping cart">
		<meta name="keywords" content="">
		<meta name="robots" content="index,follow">
		<meta name="author" content="Skander Software Solutions">
		<link rel="stylesheet" type="text/css" media="screen, projection" href="assets/reset.css">
		<link rel="stylesheet" type="text/css" media="screen, projection" href="assets/paypal.css">
		<link rel="stylesheet" type="text/css" media="print" href="assets/print.css">
	</head>
	<body>
		<div id="wrap">
			<div id="shop">
				<div id="wrapper">
					<section class="section">
						<div class="section_inner fixed">
							<?php if (is_array($_SESSION['result'])) : ?>
								<h2 class="main">Error occured</h2>
								<section id="step3">
									<p class="instructions">
										<?php foreach($_SESSION['result'] as $code => $message) {
											echo $code . ' : ' . $message;
										} ?>
									</p>
							<?php else : ?>
								<h2 class="main">Transaction completed</h2>
								<section id="step3">
									<p class="instructions">Confirmation number : <?php echo $_SESSION['result']; ?></p>
							<?php endif; ?>
								<footer></footer>
							</section>
						</div>
					</section>
				</div>
			</div>
		</div>
	</body>
</html>
<?php clean_order(); ?>
