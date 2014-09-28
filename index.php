<?php include('includes/config.php'); ?>
<?php $countries = get_coutries();?>
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
		<script type="text/javascript" src="assets/jquery.min.js"></script>
		<script type="text/javascript" src="assets/functions.js"></script>
		<script type="text/javascript" src="assets/states.json"></script>
	</head>
	<body>
		<div id="wrap">
			<div id="shop">
				<div id="wrapper">
					<section class="section">
						<div class="section_inner fixed">
							<h2 class="main">Paypal Payments</h2>
							<section id="step3">
								<form action="/process.php" method="POST">
								<p class="instructions">
									please fill in the necessary information so we can complete your purchase.</p>

									<fieldset class="sept">
										<div class="inner">
											<h3 class="legend">credit card information</h3>
											<div class="fields onefield">
												<fieldset id="payment_method_choice">
													<dl>
														<dt>PAYMENT</dt>
														<dd id="label_cardtype" class="clearfix">
															<label class="paypal_wrapper" style="margin-right: 10px;">
																<input type="radio" value="paypal" id="cardtype1" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/paypal.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="MasterCard" id="cardtype2" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/mastercard.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="Visa" id="cardtype3" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/visa.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="AmericanExpress" id="cardtype4" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/americanexpress.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="Discover" id="cardtype5" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/discover.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="DinersClub" id="cardtype6" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/dinersclub.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="Solo" id="cardtype7" name="PayerAddress[cardType]" class="cardType">
																<img class="paypal" alt="PayPal" src="images/solo.png">
															</label>
														</dd>
													</dl>
												</fieldset>
											</div>
										</div>
									</fieldset>
									<fieldset class="sept credit">
											<div class="fields twofields">
												<div class="fields twofields">
													<label>Price<br>
														<input maxlength="6" placeholder="" name="Creditcard[Price]" type="text" value="10.00"> $
													</label>
												</div>
												<fieldset id="payment_method_choice">
												<label>
													card number<br>
													<input maxlength="16" placeholder="" name="Creditcard[CreditCardNumber]" type="text">
												</label>
												<div id="label_creditcard_expiration" class="label">
													expiration date<br>
													<label for="creditcard_exp_month">expiration date (month)</label>
													<input maxlength="2" placeholder="mm" name="Creditcard[ExpMonth]" id="creditcard_exp_month" type="text">

													<label for="creditcard_exp_year">expiration date (year)</label>
													<input maxlength="2" placeholder="aa" name="Creditcard[ExpYear]" id="creditcard_exp_year" type="text">
												</div>
												</fieldset>
											</div>
											<div class="fields twofields">
												<fieldset id="payment_method_choice">
												<label id="security_code">
													Security code<br>
													<input maxlength="3" id="securitycode" name="Creditcard[CVV2]" type="text">
												</label>
												<label>
													name on the card <br>
													<input name="Creditcard[CardOwner]" id="card_owner" type="text">
												</label>
												</fieldset>
											</div>
									</fieldset>
									<fieldset class="sept credit">
										<div class="inner">
											<h3 class="legend">Billing address</h3>


											<div id="billing_adress" class="fieldset">
												<div class="fields twofields">
													<label>
														FIRST NAME<br>
														<input name="PayerAddress[first_name]" type="text">
													</label>
													<label>
														NAME<br>
														<input name="PayerAddress[last_name]" type="text">
													</label>
												</div>
												<div class="fields twofields">
													<label>
														ADDRESS<br>
														<input name="PayerAddress[street1]" type="text">
													</label>
													<label>
														ADDRESS 2<br>
														<input name="PayerAddress[street2]" type="text">
													</label>
												</div>
												<div class="fields twofields">
													<label>
														Country<br>
														<select id="country" name="PayerAddress[country]">
															<?php foreach($countries as $country) : ?>
																<option value="<?php echo $country[0]; ?>"><?php echo $country[1]; ?></option>
															<?php endforeach; ?>
														</select>
													</label>
													<label>
														State or Province<br>
														<select id="state_or_province" name="PayerAddress[state_or_province]">
														</select>
													</label>
												</div>
												<div class="fields twofields">
													<label>
														city<br>
														<input name="PayerAddress[city_name]" type="text">
													</label>
													<label>
														Postal Code<br>
														<label for="postalcode"></label>
														<input maxlength="10" name="PayerAddress[postal_code]" id="postalcode" type="text">
													</label>
												</div>
											</div>
										</div>
									</fieldset>

									<fieldset class="sept credit">
										<div class="inner">
											<h3 class="legend">Shipping address</h3>
											<div class="fields twofields">
												<label>	Same as Billing
													<input name="PayerAddress[shipping]" id="shipping" type="checkbox" value="1">
												</label>
											</div>
											<div id="shipping_adress" class="fieldset">
												<div class="fields twofields">
													<label>
														ADDRESS<br>
														<input name="PayerAddress[shipping_street1]" type="text">
													</label>
													<label>
														ADDRESS 2<br>
														<input name="PayerAddress[shipping_street2]" type="text">
													</label>
												</div>
												<div class="fields twofields">
													<label>
														Country<br>
														<select id="shipping_country" name="PayerAddress[shipping_country]">
															<?php foreach($countries as $country) : ?>
																<option value="<?php echo $country[0]; ?>"><?php echo $country[1]; ?></option>
															<?php endforeach; ?>
														</select>
													</label>
													<label>
														Province<br>
														<select id="shipping_state_or_province" name="PayerAddress[shipping_state_or_province]">
														</select>
													</label>
												</div>
												<div class="fields twofields">
													<label>
														city<br>
														<input name="PayerAddress[shipping_city_name]" type="text">
													</label>
													<label>
														Postal Code<br>
														<label for="postalcode"></label>
														<input maxlength="10" name="PayerAddress[shipping_postal_code]" id="postalcode" type="text">
													</label>
												</div>
											</div>
										</div>
									</fieldset>

									<fieldset class="sept">
										<div class="inner">
											<h3 class="legend">billing contact</h3>

											<div class="fieldset">
												<div class="fields twofields">
													<label class="tip_tel">
														phone number<br>
														<span data-info="Pour confirmation d'achat"></span>
														<input name="PayerAddress[phone]" type="tel">

													</label>
													<label>
														email<br>
														<input name="PayerAddress[email]" type="text">
													</label>
												</div>
											</div>
										</div>
									</fieldset>


									<footer>
										<div class="buttons">
											<div class="ff_fake_button">
												<button class="bt_submit bt_next_arrow" name="bt_submit" type="submit">Pay<span></span></button>
											</div>
										</div>
										<p class="notice_click_once">please be sure to click only once or you will be billed more than once.</p>
									</footer>
								</form>
							</section>
						</div>
					</section>
				</div>
			</div>
		</div>
	</body>
</html>
