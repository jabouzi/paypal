$(document).ready(function() {
	$("form").submit(function(e){
		validate_from(e, $(this).attr('id'));
	});
});

function validate_from(e, form_id)
{
	var required = 0;
	$('.has-error').removeClass('has-error');
	$('.required-error').hide();
	$("#" + form_id).find('[data-validate]').each(function() {
		if ($(this).is(":visible"))
		{
			required += validate_element($(this));
		}
	});

	process_validated_form(e, required, form_id);
}

function process_validated_form(e, required, form_id)
{
	var form = form_id;
	e.preventDefault(e);
	if (!required)
	{
		if ($('#creditcardnumber').is(":visible"))
		{
			$.get("ajax/validate_cc.php", {cc : $('#creditcardnumber').val(), year : $('#creditcard_exp_year').val(), month : $('#creditcard_exp_month').val() }, function(data) {
				if (data == 0)
				{
					$('#'+form).unbind().submit();
					return true;
				}
				else if (data == 1)
				{
					$('#creditcard_exp_year').addClass('has-error');
					$('#creditcard_exp_month').addClass('has-error');
					$('#help-creditcard_exp_year').show();
					$('#help-creditcard_exp_month').show();
					$('#creditcardnumber').addClass('has-error');
					$('#help-creditcardnumber').show();
				}
				else if (data == 2)
				{
					$('#creditcard_exp_year').addClass('has-error');
					$('#creditcard_exp_month').addClass('has-error');
					$('#help-creditcard_exp_year').show();
					$('#help-creditcard_exp_month').show();
				}
				else if (data == 3)
				{
					$('#creditcardnumber').addClass('has-error');
					$('#help-creditcardnumber').show();
				}
			});
		}
		else
		{
			$('#'+form).unbind().submit();
			return true;
		}
	}
}

function validate_element(element)
{
	var required = 0;
	if (element.attr('data-type') == 'email')
	{
		if (!isValidEmailAddress(element)) {  $(element).addClass('has-error'); required++; $('#help-'+element.attr('id')).show()}
	}
	else if (element.attr('data-validate') == 'required')
	{
		if (element.val() == '' || element.val() == null) {$(element).addClass('has-error'); required++; $('#help-'+element.attr('id')).show()}
	}
	
	return required;
}

function isValidEmailAddress(element)
{
	var isValid = false;
	var emailAddress = element.val();
	if (element.attr('data-validate') == 'validate' && emailAddress == '') isValid = true;
	else { var regex = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
		isValid = regex.test(emailAddress.toLowerCase()) ? true : false; }
	return isValid;
}

function blinkit(classname)
{
	var speed = 200;
	effectFadeIn(classname, speed);
	effectFadeOut(classname, speed);
}

function effectFadeIn(classname, speed) {
	$("." + classname).fadeOut(speed);
}

function effectFadeOut(classname, speed) {
	$("." + classname).fadeIn(speed);
}
