jQuery(document).ready(function($){
    var submit_btn = $('input.checkout-button[type="submit"]');
    $('input.checkout-button[type="button"]').click(function(event){
        $(this).prev('div.quantity').find('input.qty').val(1);
        submit_btn.click();
        $successful = false;
        if ($successful){
            $(this).previous('.buttons_added').html('Successfully added to your cart');
        } else {
            $(this).previous('.buttons_added').html('Can not checkout. Product might be out of stock. Please refresh this page for updates.');
        }
        $(this).hide();
    });
});