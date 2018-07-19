/**
 * Created by brian3t on 7/28/15.
 */

var addtocart_orchid_gift = function (event) {
    var target = $(event.target);
    var delivery_date = $(':input[name="addon-260-delivery_date[date]"]');
    addon.val(target.parent().parent().find('input.custom-qty').data('option'));

    $('#main form>button[type="submit"]').click();
};
var date_gen_Y_m_d_string = function($date){
    var $month = $date.getMonth() + 1;
  return   $date.getFullYear() + '-' + ($month < 10 ? '0' : '') + $month + '-' +
      ($date.getDate() < 10 ? '0' : '') + $date.getDate();
};
jQuery(document).ready(function ($) {
    $color_input = $(':input[name="addon-260-color"]');
    $shipping_class_chosen = $('#shipping-class-chosen');
    $deli_date_woo_input = $(':input[name="addon-260-delivery[date]"]');
    $deli_date_picker = $(':input[name^="deli_date_picker"]');
    $deli_date_picker_1 = $('#date_picker_1');
    $deli_date_picker_2 = $('#date_picker_2');
    $deli_date_picker_3 = $('#date_picker_3');
    $deli_date_picker_4 = $('#date_picker_4');
    $pick_delivery_btn_1 = $($('.pick_delivery')[0]);
    $pick_delivery_btn_2 = $($('.pick_delivery')[1]);
    $pick_delivery_btn_3 = $($('.pick_delivery')[2]);
    $pick_delivery_btn_4 = $($('.pick_delivery')[3]);

    $default_date = new Date();
    $default_date.setDate($default_date.getDate() + 7 + 4 - $default_date.getDay());
    var $output = date_gen_Y_m_d_string($default_date);
    $soonest_date = new Date();
    $soonest_date.setDate($soonest_date.getDate() + 7 + 2 - $soonest_date.getDay());
    $four_weeks_from_now = new Date();
    $four_weeks_from_now.setDate($four_weeks_from_now.getDate() + 28);

    $deli_date_picker_zdps = $('input.datepicker').Zebra_DatePicker({
        first_day_of_week: 0,
        direction: [date_gen_Y_m_d_string($soonest_date), date_gen_Y_m_d_string($four_weeks_from_now)],
        //select_other_months: true,
        show_other_months: false,
        start_date: $output,
        disabled_dates: ['* * * 0,1'],
        onSelect: function (event) {
            var $d = this.val();
            $deli_date_woo_input.val($d);
            var $d_array = $d.split('-');
            var selDate = new Date($d_array[0], $d_array[1] -1 , $d_array[2]);
            if (selDate.getDay() == 6){
                $shipping_class_chosen.val('Weekend delivery - $24.99').trigger('change');
            }
            else{
                $shipping_class_chosen.val('Weekday delivery - $14.99').trigger('change');
            }
        }
    });
    $deli_date_picker.val($output);

    $('.dp_yearpicker').after($('<span/>').append($('<div/>', {id: "deli_date_note"})
            .prepend(
            '<span class="left50" style="float: left">' +
            '   <span class="color-swatch" style="background-color: rgb(106,168,254); width: 12px; height: 12px;">&nbsp;</span>' +
            '   <span style="vertical-align: bottom; display: inline">&nbsp;Standard Delivery $14.99</span>' +
            '</span>' +
            '<span class="left50" style="float: right;">' +
            '   <span class="color-swatch" style="background-color: mediumpurple; width: 12px; height: 12px;">&nbsp;</span>' +
            '   <span style="vertical-align: bottom; display: inline">&nbsp;Weekend Delivery $24.99</span>' +
            '</span>'
        )
    ).html());
    window.$deli_date_picker_1_zdp = $($deli_date_picker_zdps[0]).data('Zebra_DatePicker');
    window.$deli_date_picker_2_zdp = $($deli_date_picker_zdps[1]).data('Zebra_DatePicker');
    window.$deli_date_picker_3_zdp = $($deli_date_picker_zdps[2]).data('Zebra_DatePicker');
    window.$deli_date_picker_4_zdp = $($deli_date_picker_zdps[3]).data('Zebra_DatePicker');

    $pick_delivery_btn_1.after($deli_date_picker_1);
    $deli_date_picker_1.hide();
    $pick_delivery_btn_2.after($deli_date_picker_2);
    $deli_date_picker_2.hide();
    $pick_delivery_btn_3.after($deli_date_picker_3);
    $deli_date_picker_3.hide();
    $pick_delivery_btn_4.after($deli_date_picker_4);
    $deli_date_picker_4.hide();

    $pick_delivery_btn_1.click(function (event) {
        $deli_date_picker_1.show();
        $deli_date_picker_1_zdp.show();
    });

    $pick_delivery_btn_2.click(function (event) {
        $deli_date_picker_2.show();
        $deli_date_picker_2_zdp.show();
    });
    $pick_delivery_btn_3.click(function (event) {
        $deli_date_picker_3.show();
        $deli_date_picker_3_zdp.show();
    });

    $pick_delivery_btn_4.click(function (event) {
        $deli_date_picker_4.show();
        $deli_date_picker_4_zdp.show();
    });

    $('button.custom_add[type="submit"]').click(function(event){
        //purple-1, white-2
        var sel_color = $(this).parent().find('input[name="color_selected"]').val();
        $color_input.val(sel_color);
        $('form.variations_form.cart').submit();
    });

});