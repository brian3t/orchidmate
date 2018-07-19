jQuery(function($) {
    /*$(document).ready(function() {
        $('form.cart').submit(function(e) {
            if ($(this).find('.qty').val() < 1) {
                e.preventDefault();
                alert('Select Product Quantity first');
            }
        });
    });*/
    jQuery(function($) {
        /*$(document).ready(function() {
            $('form.cart').submit(function(e) {
                if ($(this).find('.qty').val() < 1) {
                    e.preventDefault();
                    alert('Select Product Quantity first');
                }
            });
        });*/

        $(document).on('click', '.plus, .minus', function() {

            // Get values
            var $qty =$($(this).parent().children('input.qty'));
            var
                currentVal = 0,
                max = parseFloat($qty.attr('max')),
                min = parseFloat($qty.attr('min')),
                step = $qty.attr('step');

            // Format values
            if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
            if (max === '' || max === 'NaN') max = '';
            if (min === '' || min === 'NaN') min = 0;
            if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

            // Change the value
            if ($(this).is('.plus')) {

                if (max && (max == currentVal || currentVal > max)) {
                    $qty.val(max);
                } else {
                    console.log(currentVal);
                    $qty.val(currentVal + parseFloat(step));
                }

            } else {

                if ((typeof min !== "undefined") && (min == currentVal || currentVal < min)) {
                    $qty.val(min);
                } else if (currentVal > 0) {
                    $qty.val(currentVal - parseFloat(step));
                }

            }

            // Trigger change event
            $qty.trigger('change');
        });
    });


});