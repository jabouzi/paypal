function remove_spaces(field_id) {
    jQuery('#' + field_id).val(jQuery('#' + field_id).val().split(' ').join(''));
}
function focus_next_field(field_from, field_to, num_chars) {
    var chars = jQuery('#' + field_from).val().length;
    if (chars == num_chars) {
        jQuery('#' + field_from).next('input').focus();
    }
}
function explode(item, delimiter) {
    tmp_array = new Array(1);
    var count = 0;
    var tempstring = new String(item);
    while (tempstring.indexOf(delimiter) > 0) {
        tmp_array[count] = tempstring.substr(0, tempstring.indexOf(delimiter));
        tempstring = tempstring.substr(tempstring.indexOf(delimiter) + 1, tempstring.length - tempstring.indexOf(delimiter) + 1);
        count = count + 1;
    }
    tmp_array[count] = tempstring;
    return tmp_array;
}
function in_array(what, where) {
    var out = false;
    for (i = 0; i < where.length; i++) {
        if (what == where[i]) {
            out = true;
            break;
        }
    }
    return out;
}
function print_1d_array(array) {
    document.write("<table border=1>");
    document.write("<tr>");
    for (row = 0; row < array.length; row++) {
        document.write("<td>" + array[row] + "</td>");
    }
    document.write("</tr>");
    document.write("</table>");
}
function print_2d_array(array) {
    document.write("<table border=1>");
    for (row = 0; row < array.length; row++) {
        document.write("<tr>");
        for (col = 0; col < array[row].length; col++) {
            document.write("<td>" + array[row][col] + "</td>");
        }
        document.write("</tr>");
    }
    document.write("</table>");
}
function is_array(obj) {
    return obj && !(obj.propertyIsEnumerable('length')) && typeof obj === 'object' && typeof obj.length === 'number';
}
function round_decimals(original_number, decimals) {
    var result1 = original_number * Math.pow(10, decimals);
    var result2 = Math.round(result1);
    var result3 = result2 / Math.pow(10, decimals);

    return pad_with_zeros(result3, decimals);
}
function pad_with_zeros(rounded_value, decimal_places) {
    // Convert the number to a string
    var value_string = rounded_value.toString();

    // Locate the decimal point
    var decimal_location = value_string.indexOf('.');

    // Is there a decimal point?
    if (decimal_location == -1) {
        // If no, then all decimal places will be padded with 0s
        decimal_part_length = 0;

        // If decimal_places is greater than zero, tack on a decimal point
        value_string += decimal_places > 0 ? '.' : '';
    }
    else {
        // If yes, then only the extra decimal places will be padded with 0s
        decimal_part_length = value_string.length - decimal_location - 1;
    }

    // Calculate the number of decimal places that need to be padded with 0s
    var pad_total = decimal_places - decimal_part_length;
    if (pad_total > 0) {
        // Pad the string with 0s
        for (var counter = 1; counter <= pad_total; counter++) {
            value_string += '0';
        }
    }
    return value_string;
}
function format_float(obj) {
    var o = document.getElementById(obj);
    var oo = o.value.replace(',', '.');

    if (isFloat(oo)) {
        o.value = round_decimals(oo, 2);
    }
    else
    {
        o.value = '';
    }
}
function toInt(n) {
    return n * 1;
}
function isFloat(n) {
    if (n == 0 || n == 0.00) {
        return false;
    }
    // Test for integer
    if ((n.length > 0) && !(/[^0-9]/).test(n)) {
        return true;
    }
    else {
        // Test for float
        if ((n.length > 0) && !(/[^0-9.]/).test(n) && (/\.\d/).test(n)) {
            return true;
        }
        else {
            return false;
        }
    }
}
function left(str, n) {
    if (n <= 0) {
        return '';
    } else if (n > String(str).length) {
        return str;
    } else {
        return String(str).substring(0, n);
    }
}
function right(str, n) {
    if (n <= 0) {
        return '';
    } else if (n > String(str).length) {
        return str;
    } else {
        var iLen = String(str).length;
        return String(str).substring(iLen, iLen - n);
    }
}
function mid(str, start, len) {
    // Make sure start and len are within proper bounds
    if (start < 0 || len < 0) {
        return '';
    }
    var iEnd, iLen = String(str).length;
    if (start + len > iLen) {
        iEnd = iLen;
    } else {
        iEnd = start + len;
    }
    return String(str).substring(start, iEnd);
}
function trim(str, chars) {
    return ltrim(rtrim(str, chars), chars);
}
function ltrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
function rtrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function check_email(email) {
    var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if (filter.test(email)) {
        return true;
    }
    return false;
}
// Initialise le slideshow
function slideSwitch() {
    var $active = jQuery('#slideshow IMG.active');

    if ($active.length == 0) {
        $active = jQuery('#slideshow IMG:last');
    }
    // use this to pull the images in the order they appear in the markup
    var $next = $active.next().length ? $active.next() : jQuery('#slideshow IMG:first');

    $active.addClass('last-active');

    $next.css({opacity: 0.0})
            .addClass('active')
            .animate({opacity: 1.0}, 700, function() {
        $active.removeClass('active last-active');
    });
}
/*****************************BEGIN DOCUMENT READY ****************************/
jQuery(document).ready(function() {
    setInterval('slideSwitch()', 3500);
    
    if ($('#page_index').val() == '466')
    {
        if (parseInt($('#order_count').val()))
        {
            $('.items_footer').show();
        }
    }

    // Clear focus
    var clearMePrevious = '';

    // Set form autocomplete=off
    $('.autocomplete-off').attr('autocomplete', 'off');

    // Clear input on focus
    jQuery('.clearfocus').focus(function() {
        if (jQuery(this).val() == jQuery(this).attr('title')) {
            clearMePrevious = jQuery(this).val();
            jQuery(this).val('');
        }
    });

    // If field is empty afterward, add text again
    jQuery('.clearfocus').blur(function() {
        if (jQuery(this).val() == '') {
            jQuery(this).val(clearMePrevious);
        }
    });    
    
    $('.btremove').click(function() {
        var id = $(this).attr("id").split('_');
        var lang = $('#site_lang').val();
        $.post("/"+lang+"/remove-quantity/", {key: id[1], quantity: $('#giftcard_custom_amount').val()}, function(data) {
            window.location.reload();
        });
    });
    
    $('.bt_remove_item').click(function() {
        var id = $(this).attr("id").split('_');
        var lang = $('#site_lang').val();
        $.post("/"+lang+"/remove-order/", {key: id[1]}, function(data) {
            window.location.reload();
        });
    });
    
    $('.btadd').click(function() {
        var id = $(this).attr("id").split('_');
        var lang = $('#site_lang').val();
        $.post("/"+lang+"/add-quantity/", {key: id[1], quantity: $('#giftcard_custom_amount').val()}, function(data) {
            window.location.reload();
        });
    });
    
    $('.bt_amount').click(function() {
        var lang = $('#site_lang').val();
        $.post("/"+lang+"/add-order/", {amount: $(this).attr("data-amount")}, function(data) {
            if (data == 1)
            {
                $('.items_footer').show();
                var count = $('.card_nb_items').html();
                if (count == '') count = 0;
                $('.card_nb_items').html(parseInt(count)+1);
                $('.icon').addClass('icon-active');
            }
        });
    });
    
    $('.bt_custom_amount').click(function() {
        var lang = $('#site_lang').val();
        $.post("/"+lang+"/add-order/", {amount: $("#giftcard_custom_amount").val()}, function(data) {
            if (data == 1)
            {
                $('.items_footer').show();
                $('.error_msg').html('').show();
                var count = $('.card_nb_items').html();
                if (count == '') count = 0;
                $('.card_nb_items').html(parseInt(count)+1);
                $('.icon').addClass('icon-active');
            }
            else
            {
                $('.error_msg').html(data).show();
            }
        });
    });
    
    $('#gift_card_submit').click(function() {
         window.location = $('#cart_url').val();
    });
    
    $('.shipping_method').click(function() {
        if ($(this).val() == 'email')
        {
            $('#shipping_by_email').show();
            $('#shipping_by_mail').hide();
        }
        else
        {
            $('#shipping_by_email').hide();
            $('#shipping_by_mail').show();            
        }
    });
    
    $('.fields').click(function() {
        $('#billing_adress').show();
        if ($('#same_as_shipping_address').is(':checked')) 
        {
            $('#billing_adress').hide();
        }
    });
});

/*****************************END DOCUMENT READY ******************************/
