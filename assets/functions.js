
jQuery(document).ready(function() {
    $('.credit').show();    
    if ($('#cardtype1').is(':checked')) 
	{
		$('.credit').hide();
	}
	
	$('#shipping_adress').show();
    if ($('#shipping_billing').is(':checked')) 
	{
		$('#shipping_adress').hide();
	}
	
    $('.cardType').click(function() {
        $('.credit').show();
        if ($('#cardtype1').is(':checked')) 
		{
			$('.credit').hide();
		}
    });
    
    $('#shipping_billing').click(function() {
        $('#shipping_adress').show();
		if ($('#shipping_billing').is(':checked')) 
		{
			$('#shipping_adress').hide();
		}
    });
});

/*****************************END DOCUMENT READY ******************************/
