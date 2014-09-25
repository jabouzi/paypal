<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="UTF-8">
	<meta http-equiv="cache-control" content="public">
	<meta http-equiv="expires" content="Fri, 30 Dec 2013 12:00:00 GMT">

	<title>Shopping cart - skyspa</title>
	<meta name="description" content="Shopping cart">
	<meta name="keywords" content="">
	<meta name="robots" content="index,follow">
	<meta name="author" content="Skander Software Solutions">
	<link rel="stylesheet" type="text/css" media="screen, projection" href="assets/reset.css">
	<link rel="stylesheet" type="text/css" media="screen, projection" href="assets/paypal.css">
	<link rel="stylesheet" type="text/css" media="print" href="assets/print.css">
	<link rel="stylesheet" href="assets/jquery.css" type="text/css" media="screen">
	<script src="assets/modernizr-latest.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/skyspa-test.js"></script>
	<script src="assets/functions.js" type="text/javascript"></script>
	<script src="assets/calendar.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/jquery.js"></script>
</head>
    <body>
<div id="wrap"><header id="header_nav" class="shopping-top">
    <h1><a data-trackevent="logoskyspa|clic|boutique/etape2/fr" href="https://skyspa.dev.tgiprojects.com/en/home/"><img alt="SkySpa" src="assets/logo_skyspa.png" width="42" height="131"></a></h1>
    <nav class="topnav">
        <div class="inner">
            <ul>
			                <li><a data-trackevent="boutique|btretour|etape2/fr" class="bt bt_back_arrow" href="https://skyspa.dev.tgiprojects.com/en/home/"><span>&lt;</span>Retour au site</a></li>
                <li class="phone">1 866 656 9111</li>
			</ul>
		</div>
	</nav>
</header>
<div id="shop">
	<div id="wrapper">
		
		<section class="section">
			<div class="section_inner fixed">
				<h2 class="main">panier d’achats</h2>
				<nav class="breadcrumb">
					<ul>
						
						<li>
							<a href="https://skyspa.dev.tgiprojects.com/en/shopping-cart/">
						purchases</a></li>
						
						
						<li>
							<a href="https://skyspa.dev.tgiprojects.com/en/shopping-cart/send-mode/">
						DELIVERY METHOD</a></li>
						
						
						<li>
							<a href="https://skyspa.dev.tgiprojects.com/en/shopping-cart/payment-mode/">
						PAYMENT</a></li>
						
						
						<li class="active">
						<span>BILLING</span></li>
						
						
						<li class="inactive last">
						<span>Confirmation</span></li>
						
					</ul>
				</nav>
				<section id="step3">
                    <p class="instructions">
                        please fill in the necessary information so we can complete your purchase.                    </p>
                    					<form id="f_step4" autocomplete="off" action="" method="post" novalidate="novalidate">
						
						<input value="CA" name="PayerAddress[country]" type="hidden">
						<input value="Visa" name="CreditCardType" type="hidden">
						
						<fieldset id="payment_creditcard" style="display: block;">
							<div class="inner">
								<h3 class="legend">credit card information</h3>
								<div class="fields onefield">
									<label>
										type of card:<br>
										<select name="CreditCardType">
											<option value="MasterCard">MasterCard</option>
											<option value="Visa" selected="selected">Visa</option>
										</select>
									</label>
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
                
				<script type="text/javascript">
					$(document).ready(function() {
						$('form').validate({
							errorClass: 'error_msg',
							onfocusout: false,
							rules: {
								CreditCardNumber: {
									required: true,
									creditcard: true
								},
								ExpMonth: {
									required: true,
									mindate: true
								},
								ExpYear: {
									required: true,
									mindate: true
								},
								CVV2: {
									required: true,
									range: [1, 999]
								},
								CardOwner: 'required',
								'PayerAddress[first_name]': {
									required: '#same_as_shipping_address:not(:checked)'
								},
								'PayerAddress[last_name]': {
									required: '#same_as_shipping_address:not(:checked)'
								},
								'PayerAddress[street1]': {
									required: '#same_as_shipping_address:not(:checked)'
								},
								'PayerAddress[city_name]': {
									required: '#same_as_shipping_address:not(:checked)'
								},
								'PayerAddress[state_or_province]': {
									required: '#same_as_shipping_address:not(:checked)'
								},
								'PayerAddress[postal_code1]': {
									required: '#same_as_shipping_address:not(:checked)',
									postalcode: true
								},
								'PayerAddress[postal_code2]': {
									required: '#same_as_shipping_address:not(:checked)',
									postalcode: true
								},
								'PayerAddress[email]': {
									required:true,
									email: true
								},
								'PayerAddress[phone]': {
									required: true,
									phone: true
								}
							},
							groups: {
								cc_exp_date: 'ExpMonth ExpYear',
								postalcode: 'PayerAddress[postal_code1] PayerAddress[postal_code2]'
							},
							messages: {
								'ShippingAddress[postal_code1]': 'Invalide(<span class="example">H1H1H1</span>)'
							},
							errorPlacement: function($error, $field) {
								if ($field.attr('id') === 'postalcode1' || $field.attr('id') === 'postalcode2') {
									$field.parent().append($error);
									} else if ($field.parent().attr('id') === 'label_creditcard_expiration') {
									$('#label_creditcard_expiration').append($error);
									} else {
									$field.after($error);   
								}
							},
							submitHandler: function(form) {
								var $form = $(form),
								$btSubmit = $form.find('.bt_submit');
								
								$btSubmit.attr('disabled', 'disabled').addClass('disabled').find('span').addClass('is_loading');
								setTimeout(function() {
									form.submit();
								}, 500);
							}
						});
						$.extend($.validator.messages, {
							required: 'Required',
							email: 'Invalide(<span class="example">nom@domaine.com</span>)',
							range: 'Invalide(<span class="example">000</span>)'
						});
						
						$.validator.addMethod('phone', function(value, element, params) { 
							var phone = $('input[name="PayerAddress[phone]"]').val(),
							regex = /^\+?1?( |-)?\(?(\d{3})\)?( |-)?(\d{3})( |-)?(\d{4})$/i,
							isValid = regex.test(phone.toLowerCase()) ? true : false;
							return isValid;
						}, 'Invalide(<span class="example">###-###-####</span>)');
						
						$.validator.addMethod('postalcode', function(value, element, params) { 
							var postalcode = $('#postalcode1').val() + $('#postalcode2').val(),
							regex = /^[abceghjklmnprstvxy]{1}\d{1}[a-z]{1}\d{1}[a-z]{1}\d{1}$/i,
							isValid = regex.test(postalcode.toLowerCase()) ? true : false;
							return isValid;
						}, 'Invalide(<span class="example">H1H1H1</span>)');
						
						// Validate MasterCard (16) or Visa (13|16) card format
						$.validator.addMethod('creditcard', function(value, element, params) {
							var regex = /^(5[1-5]\d{14})|(4[0-9]{12}(?:[0-9]{3})?)$/,
							isValid = regex.test(value);
							return isValid;
						}, 'Invalide(<span class="example">1234567890123456</span>)');
						
						$.validator.addMethod('mindate',function(value, element, minDate){
							var expMonth = $('#creditcard_exp_month').val(),
							expYear = $('#creditcard_exp_year').val(),
							expDate = new Date('20' + expYear, expMonth)
							date = new Date(),
							isValid = (expDate > date) ? true : false;
							return isValid;
						}, 'Card expired');
						
					});
				</script>
			
				
			</div> <!-- //.section_inner -->
		</section>
	</div>
	
</div>	
<div id="push"></div>
</div>
<nav class="globalnav">
	<div class="inner clearfix">
		<ul class="primary">
			<li data-nav="home" class=""><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/home/">Home</a></li>
			<li data-nav="space" class=""><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/skyspa/terraces-and-baths/">Terraces and baths</a></li>
			<li data-nav="treatment" class=""><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/treatments-and-prices/">Treatments and prices</a></li>
            			<li data-nav="events" class=""><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/events/">Events</a></li>
			<li data-nav="events" class=""><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/promotions/">Promotions</a></li>
			<li data-nav="giftcard"><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/gift-cards/">Gift cards</a></li>
			<li data-nav="contact"><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/hours-and-contact-info/">Hours and contact info</a></li>
			<li data-nav="galerie-video"><a class="hashable" href="https://skyspa.dev.tgiprojects.com/en/video-gallery/">Video gallery</a></li>
			<li><a class="hashable" href="https://skyspa.dev.tgiprojects.com/blog/?lang=en">Blog</a></li>
			
		</ul>
		<ul class="primary second">
			<ul class="conditions">
				<li class="conditions"><a data-trackevent="lightbox|conditions|fr" data-lightbox-id="rules" class="btlightbox" href="javascript:;">
                        TERMS <br>AND CONDITIONS</a></li>
                			</ul>
		</ul>
	</div>
</nav>
<div id="lightbox_rules" class="lightbox" style="display: none;">
    <div class="overlay"></div>
    <div class="box">
        <div class="box_inner">
            <h1>Terms and conditions</h1>
			<h2>
	What you need to know</h2>
<p>
	A swimsuit and sandals are obligatory in the spa. Shampoo, soap and 
hair dryers are available in the locker rooms. You can rent a bathrobe 
or use your own. The minimum age required to access spa facilities is 
16. The minimum age for massage services and treatments is 18. The 
thermal experience is not recommended for pregnant women, anyone with 
heart or respiratory problems, or anyone suffering from high blood 
pressure or diabetes. The advice of a doctor is strongly recommended. 
Drinking alcohol is not recommended before the thermal experience. 
Lunches, glass containers, cameras and cell phones are not allowed. 
Smoking in or around the spa is prohibited. We offer all of our guests 
the chance to relax in a calm, inviting environment. Upon your arrival, 
please speak softly.</p>
<h2>
	Terms and conditions for reservations<br>
	and online purchases</h2>
<p>
	A credit card is required to book massages and treatments. Reservations
 can be made by phone or in person. A valid credit card is required to 
confirm your reservation; any reservation cancelled or modified less 
than 48 hours before the appointment will incur a charge of 50% of the 
full treatment value. If a reserved treatment is cancelled or modified 
less than 24 hours before the appointment, the full treatment value will
 be charged to your credit card. We don’t take reservations for the 
thermal experience. For your comfort, we have established a maximum 
capacity of people who can use our services at one time. Thank you for 
arriving 30 minutes before your massage or treatment. All of our massage
 therapists are members of a recognized association or federation and 
can provide you with insurance receipts upon request. Prices are in 
Canadian dollars, do not include taxes and are subject to change without
 notice. Online purchases from our boutique are non-refundable, 
non-exchangeable, and cannot be exchanged for cash. We cannot be held 
responsible for online purchases delayed by Canada Post.</p>
<h2>
	Terms and conditions for gift cards</h2>
<p>
	Activating the gift card confirms your acceptance of our terms of use. 
The gift card is accepted as a form of payment for services, products 
and packages offered at SkySpa. The total amount of your purchases is 
deducted from the balance on the card. It is not refundable, has no 
monetary value, and cannot be replaced in the event of damage, loss or 
theft.</p>
<h2>
	Terms and conditions for vouchers<br>
	and privilege cards</h2>
<p>
	The voucher is valid for one year after the issue date indicated at the
 top. It cannot be used to buy food or drink at Station Saveurs. It is 
not refundable, has no monetary value, and cannot be replaced in the 
event of damage, loss or theft. The voucher is transferable, but the 
recipient has to have it in their possession when visiting SkySpa.</p>			
		</div>
        <a class="btclose" href="javascript:;">Fermer</a>
    </div>
</div>
<input id="site_lang" value="en" type="hidden">
<input id="page_index" value="472" type="hidden">


	
<div style="display: none;" id="cboxOverlay"></div><div style="display: none;" tabindex="-1" role="dialog" class="" id="colorbox"><div id="cboxWrapper"><div><div style="float: left;" id="cboxTopLeft"></div><div style="float: left;" id="cboxTopCenter"></div><div style="float: left;" id="cboxTopRight"></div></div><div style="clear: left;"><div style="float: left;" id="cboxMiddleLeft"></div><div style="float: left;" id="cboxContent"><div style="float: left;" id="cboxTitle"></div><div style="float: left;" id="cboxCurrent"></div><button id="cboxPrevious" type="button"></button><button id="cboxNext" type="button"></button><button id="cboxSlideshow"></button><div style="float: left;" id="cboxLoadingOverlay"></div><div style="float: left;" id="cboxLoadingGraphic"></div></div><div style="float: left;" id="cboxMiddleRight"></div></div><div style="clear: left;"><div style="float: left;" id="cboxBottomLeft"></div><div style="float: left;" id="cboxBottomCenter"></div><div style="float: left;" id="cboxBottomRight"></div></div></div><div style="position: absolute; width: 9999px; visibility: hidden; display: none;"></div></div></body></html>
