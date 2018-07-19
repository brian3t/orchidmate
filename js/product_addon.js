var $cart_form;
jQuery(document).ready(function ($) {
    $cart_form = $('form.cart[method="post"]');
    //find the div that contains addtocart buttons
    $('div.woo-sc-hr button.button[type="submit"]:contains("Add")').click(function () {
        var $e = $(this);
        var $addon = $e.closest('tr');
        if (! typeof $addon === 'Object'){
            return;
        }
        var $qty = $($addon.find('input'));
	var qty = parseInt($qty.val());
var option_qty_name = '';
if (qty < 10) { option_qty_name='10plus-1'; }
if (10<= qty && qty < 25){ option_qty_name = '10plus'; }
if (25<= qty && qty < 50){ option_qty_name = '25plus'; }
if (50<= qty && qty < 100){ option_qty_name = '50plus'; }
if (100 <= qty && qty < 200) { option_qty_name = '100plus'; }
if (200 <= qty) { option_qty_name = '200plus'; }
var option_name = $qty.data('option');
if (option_name.indexOf('sq') === false){ //this is orchidmate
	option_name = option_qty_name + '-' + option_name;
}
var $addon_select = $cart_form.find('select.addon-select');
var option_to_select = $addon_select.find('option[value^='+ option_name +']');
        $addon_select.val(option_to_select.val()).trigger('change');
        $cart_form.find('input.qty').val($qty.val()).trigger('change');
        $cart_form.find('button[type="submit"]').trigger('click');
    });
});
