
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

	set_states('country', 'state_or_province');
	set_states('shipping_country', 'shipping_state_or_province');


	$('#country').change(function()
	{
		set_states('country', 'state_or_province');
	});

	$('#shipping_country').change(function()
	{
		set_states('shipping_country', 'shipping_state_or_province');
	});
});

function set_states(id1, id2)
{
	$('#'+id2).empty();
	var selected = states[$('#'+id1).val()];
	$.each(selected, function(i, item) {
		$('#'+id2).append('<option value="'+item[0]+'">'+item[0]+'</option>');
	});
}
