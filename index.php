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
	</head>
    <body>
		<div id="wrap">
			<div id="shop">
				<div id="wrapper">			
					<section class="section">
						<div class="section_inner fixed">
							<h2 class="main">Paypal Payments</h2>
							<section id="step3">
								<form action="process.php" type="POST">
								<p class="instructions">
									please fill in the necessary information so we can complete your purchase.                    </p>
													<form id="f_step4" autocomplete="off" action="" method="post" novalidate="novalidate">
									
									<input value="CA" name="PayerAddress[country]" type="hidden">
									<input value="Visa" name="CreditCardType" type="hidden">
									
									<fieldset id="payment_creditcard" style="display: block;">
										<div class="inner">
											<h3 class="legend">credit card information</h3>
											<div class="fields onefield">
												<fieldset id="payment_method_choice">
													<dl>
														<dt>PAYMENT</dt>
														<dd id="label_cardtype" class="clearfix">
															<label class="paypal_wrapper" style="margin-right: 10px;">
																<input type="radio" value="paypal" id="cardtype1" name="payPal">
																<img width="60" height="38" class="paypal" alt="PayPal" src="images/paypal.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="MasterCard" id="cardtype2" name="cardType">
																<img width="60" height="38" class="paypal" alt="PayPal" src="images/mastercard.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="Visa" id="cardtype3" name="cardType">
																<img width="60" height="38" class="paypal" alt="PayPal" src="images/visa.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="AmericanExpress" id="cardtype4" name="cardType">
																<img width="60" height="38" class="paypal" alt="PayPal" src="images/americanexpress.png">
															</label>
															<label class="paypal_wrapper"  style="margin-right: 10px;">
																<input type="radio" value="Discover" id="cardtype5" name="cardType">
																<img width="60" height="38" class="paypal" alt="PayPal" src="images/discover.png">
															</label>
														</dd>
													</dl>
												</fieldset>
											</div>
											<div class="fields twofields">
												<label>
													card number<br>
													<input maxlength="16" placeholder="0000000000000000" name="CreditCardNumber" type="text">
												</label>
												<div id="label_creditcard_expiration" class="label">
													expiration date<br>
													<label for="creditcard_exp_month">expiration date (month)</label>
													<input maxlength="2" placeholder="mm" name="ExpMonth" id="creditcard_exp_month" type="text">
													
													<label for="creditcard_exp_year">expiration date (year)</label>
													<input maxlength="2" placeholder="aa" name="ExpYear" id="creditcard_exp_year" type="text">
												</div>
											</div>
											<div class="fields twofields">
												<label id="security_code">
													Security code<br>
													<input maxlength="3" id="securitycode" name="CVV2" type="text">
												</label>
												<label>
													name on the card <br>
													<input name="CardOwner" id="card_owner" type="text">
												</label>
											</div>
										</div>
									</fieldset>
									<fieldset class="sept" style="display: block;">
										<div class="inner">
											<h3 class="legend">billing address</h3>
																			
											
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
												<div class="fields threefields">
													<label>
														city<br>
														<input name="PayerAddress[city_name]" type="text">
													</label>
													<label>
														Province<br>
														<select id="state_or_province" name="PayerAddress[state_or_province]">
															<option value="AB" selected="selected">Alberta</option>
															<option value="BC">British Columbia</option>
															<option value="MB">Manitoba</option>
															<option value="NB">New Brunswick</option>
															<option value="NL">Newfoundland and Labrador</option>
															<option value="NT">Northwest Territories</option>
															<option value="NS">Nova Scotia</option>											
															<option value="NU">Nunavut</option>
															<option value="ON">Ontario</option>
															<option value="PE">Prince Edward Island</option>
															<option value="QC">Québec</option>	
															<option value="SK">Saskatchewan</option>										
															<option value="YT">Yukon</option>
														</select>
													</label>
													<div id="label_postalcode" class="label">
														Postal Code<br>
														<label for="postalcode1"></label>
														<input maxlength="3" name="PayerAddress[postal_code1]" id="postalcode1" type="text">
														<label for="postalcode2">Postal Code (3 last characters)</label>
														<input maxlength="3" name="PayerAddress[postal_code2]" id="postalcode2" type="text">
													</div>
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
														<span data-info="Pour confirmation d'achat" class="ttip">?</span>
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
											<a class="bt bt_light bt_back_arrow" href="https://skyspa.dev.tgiprojects.com/en/shopping-cart/payment-mode/"><span></span>return to previous step</a>
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
