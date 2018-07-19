var addtocartcustom = function(event){
    var target = $(event.target);
    var qty = $('input[name="quantity"]');
    qty.val(target.parent().parent().find('input.custom-qty').val());
    var addon = $('select[name="addon-11-model"]');
    addon.val(target.parent().parent().find('input.custom-qty').data('option'));

    $('#main form>button[type="submit"]').click();
};
/*
 @var jQuery qtyElement
 */
var addtocartcustomwithsize = function(event){
    var target = $(event.target);
    var qtyElement = $(target.parent().parent().find('input.custom-qty'));
    var qtyVal = qtyElement.val();
    if (qtyVal < 10){alert("Please buy at least 10 products");return(false);}

    var qty = $('input[name="quantity"]');
    qty.val(qtyVal);
    var addon = $('select[name="addon-13-pmsize"]');
    var qtyRange = 200;
    if (qtyVal < 25){
        qtyRange = 10;
    }
    else if (qtyVal < 50){
        qtyRange = 25;
    }
    else if (qtyVal < 100){
        qtyRange = 50;
    }
    else if (qtyVal < 200){
        qtyRange = 100;
    }

    valueToSelect = addon.find('option[value^="' + qtyElement.data('option') + '-' + qtyRange + 'plus"]');
    addon.val($(valueToSelect).val());

    $('#main form>button[type="submit"]').click();
};

jQuery(document).ready(function($){


    $('body.postid-11 table.easy-table button[type="submit"]').bind('click', addtocartcustom);
});

