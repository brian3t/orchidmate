jQuery(document).ready(function ($) {
    var $amounts = $('span.woocommerce-Price-amount.amount');
    $.each($amounts, function (i, v) {
        if ($(v).text() === '$0.00'){
            $(v).hide();
        }
    })
});