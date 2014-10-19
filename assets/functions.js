
jQuery(document).ready(function() {
	$(".credit").children().show();
	$(".credit").show();
	if ($("#cardType1").is(":checked"))
	{
		$(".credit").hide();
		$(".credit").children().hide();
	}

	$("#shipping_adress").children().show();
	$("#shipping_adress").show();
	if ($("#shipping").is(":checked"))
	{
		$("#shipping_adress").hide();
		$("#shipping_adress").children().hide();
	}

	$(".cardType").click(function() {
		$(".credit").children().show();
		$(".credit").show();
		if ($("#cardType1").is(":checked"))
		{
			$(".credit").hide();
			$(".credit").children().hide();
		}
	});

	$("#shipping").click(function() {
		$("#shipping_adress").children().show();
		$("#shipping_adress").show();
		if ($("#shipping").is(":checked"))
		{
			$("#shipping_adress").hide();
			$("#shipping_adress").children().hide();
		}
	});

	set_states("country", "state_or_province");
	set_states("shipping_country", "shipping_state_or_province");


	$("#country").change(function()
	{
		set_states("country", "state_or_province");
	});

	$("#shipping_country").change(function()
	{
		set_states("shipping_country", "shipping_state_or_province");
	});
	
	$('.numberonly').keyup(function () { 
		this.value = this.value.replace(/[^0-9]/g,'');
	});
	
	$('.priceonly').keyup(function () { 
		this.value = this.value.replace(/[^0-9\.]/g,'');
	});
});

function set_states(id1, id2)
{
	$("#"+id2).empty();
	var selected = states[$("#"+id1).val()];
	$.each(selected, function(i, item) {
		$("#"+id2).append("<option value='"+item[0]+"'>"+item[0]+"</option>");
	});
}
