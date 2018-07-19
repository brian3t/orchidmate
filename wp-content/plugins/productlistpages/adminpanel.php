<?php
add_action('admin_menu', 'plp_config_page');
/**
 * Help Handler
 * @global type $plp_hook
 * @param string $contextual_help
 * @param string $screen_id
 * @param object $screen
 * @return string
 */
function plp_help($contextual_help, $screen_id, $screen) {
    global $plp_hook;
    //$contextual_help = vendorship_parse('help-general');
    if ($screen_id == $plp_hook) {
        if (!isset($_GET['tab'])) {
            $_GET['tab'] = 'general';
        }
        switch ($_GET['tab']) {
            default:
                break;
        }
    }
    $screen->add_help_tab(array(
        'id' => 'shortcodes', //unique id for the tab
        'title' => 'Shortcodes', //unique visible title for the tab
        'content' => '<p>' . __('To display the products list, just add <strong>[productslist]</strong> to any page. It will display a table with all the default options (set by this panel).','plp') . '</p>'
        . __('<h3>Parameters</h3>'
        . '<ul><li><strong>titles=true/false</strong>: Display field titles on table.</li>'
        . '<li><strong>posts_per_page=x</strong>: If you want to have pagination, set how many products per page to display.</li>'
        . '<li><strong>cat=x</strong>: If you want to list pages from specific categories, use this parameter with the categori IDs or slugs.</li>'
        . '<li><strong>image=true/false</strong>: Display the product image column or not</li>'
        . '<li><strong>order=desc</strong>: ASC or DESC order.</li>'
        . '<li><strong>orderby=title/ID</strong>: What the product sorting should be based on.</li>'
        . '<li><strong>imageintitle=true/false</strong>: If this is true, the product image will be shown in title column instead of it\'s own.</li>'
        . '<li><strong>id=x</strong>: If you want to display specific products, add the product IDs here.</li>'
        . '<li><strong>producttitle=true/false</strong>: Set true to display the product title.</li>'
        . '<li><strong>excerpt=true/false</strong>: Set to true to display the product excerpt (description).</li>'
        . '<li><strong>content=true/false</strong>: Set to true to display the whole product content.</li>'
        . '<li><strong>price=true/false</strong>: Set to true to display the price of products.</li>'
        . '<li><strong>addtocartbutton=top/bottom/both/none/product</strong>: This sets the position of the Add to Cart button. If is set to product, no quantity box will be displayed.</li>'
        . '<li><strong>stock=true/false</strong>: Set to true to display stock levels of products.</li>'
        . '<li><strong>sku=true/false</strong>: Set to true to display SKU of products.</li>'
		. '<li><strong>variations=true/false</strong>: Set to true to display variations of products.</li>'
        . '</ul>', 'plp')
    ));
    $screen->add_help_tab(array(
        'id' => 'plp_changelog', //unique id for the tab
        'title' => __('ChangeLog', 'plp'), //unique visible title for the tab
        'content' => '<ul>'
        . '<li>1.0:<br />'.__('Initial Release', 'plp').'</li>'
        . '<li>1.1:<br />'.__('Added a CSS file.', 'plp').'</li>'
        . '<li>1.2:<br />'.__('Bugfixes, shortcodes.', 'plp').'</li>'
        . '<li>1.3:<br />'.__('Bugfixes, admin panel to choose if CSS will be loaded.', 'plp').'</li>'
        . '<li>1.4:<br />'.__('New admin panel with default display options, change of order, etc.<br />'
                . 'added id parameter to view specific products by ID<br />'
                . 'added producttitle parameter (default is true)<br />'
                . 'added add to cart button option: addtocartbutton Values: both (default), top, bottom, none', 'plp').'</li>'
        . '<li>1.5<br />'.__('cat parameter now also works with slugs<br />'
                . 'you can now add custom css rules in admin panel<br />'
                . 'minor fixes and security checks<br />'
                . 'option to have add to cart button for each product instead of quantity box<br />'
                . 'check if a product is purchasable and in stock before it gets displayed<br />'
                . 'maximum quantity is now determinated by wooCommerce and it\'s not a default 99<br />'
                . 'added some more documentation<br />'
                . 'option to display stock and new shortcode variation (stock)','plp').'</li>'
		. '<li>1.6<br />'.__('Now, with variations support!<br />'
                . 'SKU support added also<br />'
                . 'Bootstrap CSS Support, for responsive table design<br />'
                . 'WPML Support<br />'
				. 'Minnor Bug Fixes<br />'
                . 'Quantity Validation, if set to zero, nothing will be added to cart','plp').'</li>'
        . '</ul>'
    ));
    $screen->add_help_tab(array(
        'id' => 'plp_support', //unique id for the tab
        'title' => __('Support', 'plp'), //unique visible title for the tab
        'content' => '<p>' .  __('If you need support, please email us at info@creativeg.gr.', 'plp') . '</p>'
    ));
    return $contextual_help;
}
function plp_config_page() {
    global $plp_hook;
    if (function_exists('add_submenu_page') && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
        $plp_hook = add_submenu_page('options-general.php', __('Product List Pages', 'plp'), __('Product List Pages', 'plp'), 'manage_options', 'plp_config', 'plp_config');
    }
}
/**
 * webNoiseShield Configuration Screen
 */
function plp_config() {
    // variables for the field and option names
    $hidden_field_name = 'mt_submit_hidden';
    // Read in existing option value from database
    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
        update_option('plp_css', esc_attr($_POST['plp_css']));
        if (isset($_POST['plp_displayname']) && $_POST['plp_displayname'] == '1'){
            update_option('plp_displayname', "1");
        }
        else {
            update_option('plp_displayname', "0");
        }
        update_option('plp_namecaption', esc_attr($_POST['plp_namecaption']));
        if (isset($_POST['plp_displayimage']) && $_POST['plp_displayimage'] == '1'){
            update_option('plp_displayimage', "1");
        }
        else {
            update_option('plp_displayimage', "0");
        }
        update_option('plp_imagecaption', esc_attr($_POST['plp_imagecaption']));
        if (isset($_POST['plp_imageintitle']) && $_POST['plp_imageintitle'] == '1'){
            update_option('plp_imageintitle', "1");
        }
        else {
            update_option('plp_imageintitle', "0");
        }
        if (isset($_POST['plp_displayexcerpt']) && $_POST['plp_displayexcerpt'] == '1'){
            update_option('plp_displayexcerpt', "1");
        }
        else {
            update_option('plp_displayexcerpt', "0");
        }
        update_option('plp_excerptcaption', esc_attr($_POST['plp_excerptcaption']));
        update_option('plp_excerptlength', (int)$_POST['plp_excerptlength']);
        if (isset($_POST['plp_displaycontent']) && $_POST['plp_displaycontent'] == '1'){
            update_option('plp_displaycontent', "1");
        }
        else {
            update_option('plp_displaycontent', "0");
        }
        update_option('plp_contentcaption', esc_attr($_POST['plp_contentcaption']));
        update_option('plp_contentlength', (int)$_POST['plp_contentlength']);
        if (isset($_POST['plp_displayprice']) && $_POST['plp_displayprice'] == '1'){
            update_option('plp_displayprice', "1");
        }
        else {
            update_option('plp_displayprice', "0");
        }
        update_option('plp_pricecaption', esc_attr($_POST['plp_pricecaption']));
        if (isset($_POST['plp_displaystock']) && $_POST['plp_displaystock'] == '1'){
            update_option('plp_displaystock', "1");
        }
        else {
            update_option('plp_displaystock', "0");
        }
        update_option('plp_stockcaption', esc_attr($_POST['plp_stockcaption']));

		//sku
		if (isset($_POST['plp_displaysku']) && $_POST['plp_displaysku'] == '1'){
            update_option('plp_displaysku', "1");
        }
        else {
            update_option('plp_displaysku', "0");
        }
        update_option('plp_skucaption', esc_attr($_POST['plp_skucaption']));
		
		//variation
		if (isset($_POST['plp_display_variation']) && $_POST['plp_display_variation'] == '1'){
            update_option('plp_display_variation', "1");
        }
        else {
            update_option('plp_display_variation', "0");
        }
        update_option('plp_variation_caption', esc_attr($_POST['plp_variation_caption']));
		
		
		update_option('plp_custom_success_msg', esc_attr($_POST['plp_custom_success_msg']));
		update_option('plp_custom_err_msg', esc_attr($_POST['plp_custom_err_msg']));
		
        update_option('plp_customcss', esc_attr($_POST['plp_customcss']));
        update_option('plp_displaytitles', esc_attr($_POST['plp_displaytitles']));
        update_option('plp_displayvariations', esc_attr($_POST['plp_displayvariations']));
        update_option('plp_orderby', esc_attr($_POST['plp_orderby']));
        update_option('plp_order', esc_attr($_POST['plp_order']));
        update_option('addtocartposition', esc_attr($_POST['addtocartposition']));
        update_option('plp_fieldsorder', $_POST['fieldOrder']);
        // Put an settings updated message on the screen
        ?>
        <div class="updated"><p><strong><?php _e('Settings Saved.', 'plp'); ?></strong></p></div>
        <?php
    }
    // Now display the settings editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __('Product List Pages Options', 'plp') . "</h2>";
    // settings form
    ?>
        <style>
            form label {
                display: inline-block;
                min-width: 200px;
            }
        </style>
    <form name="ombaSettings" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <h3><?php _e('Default Fields to Display', 'plp');?></h3>
        <p><?php _e('Drag and drop to change the order', 'plp');?></p>
        <form method="post" action="">
            <?php $fieldOrder = get_option('plp_fieldsorder');
            if (!is_array($fieldOrder)){
                $fieldOrder=array();
            }
            if (!isset($fieldOrder['name'])) {
                $fieldOrder['name']='1';
            }
            if (!isset($fieldOrder['image'])) {
                $fieldOrder['image']='2';
            }
            if (!isset($fieldOrder['excerpt'])) {
                $fieldOrder['excerpt']='3';
            }
            if (!isset($fieldOrder['content'])) {
                $fieldOrder['content']='4';
            }
            if (!isset($fieldOrder['price'])) {
                $fieldOrder['price']='5';
            }
            if (!isset($fieldOrder['stock'])) {
                $fieldOrder['stock']='6';
            }
			if (!isset($fieldOrder['sku'])) {
                $fieldOrder['sku']='7';
            }
			if (!isset($fieldOrder['variation'])) {
                $fieldOrder['variation']='8';
            }
            asort($fieldOrder);
?>
    <table id="fields" class="wp-list-table widefat fixed posts" style="width:95%">
        <thead>
            <tr>
                <th style="width: 80px;"><?php _e('Field', 'plp'); ?></th>
                <th style="width: 60px;"><?php _e('Display', 'plp'); ?></th>
                <th><?php _e('Options', 'plp'); ?></th>
                <th style="width: 180px;"><?php _e('Caption', 'plp'); ?></th>
                <th><?php _e('Shortcode', 'plp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $count=1; foreach ($fieldOrder as $field=>$order):?>
                <?php switch($field) {
                     case 'name': ?>
                        <tr>
                            <td><input type="hidden" name="fieldOrder[name]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Product Name', 'plp');?></td>
                            <td><input <?php if (get_option('plp_displayname') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displayname" value="1" /></td>
                            <td></td>
                            <td><input value="<?php echo get_option('plp_namecaption');?>" type="text" name="plp_namecaption" /></td>
                            <td>producttitle=true/false</td>
                        </tr>
                    <?php break;
                     case 'image': ?>
                        <tr>
                            <td><input type="hidden" name="fieldOrder[image]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Product Image', 'plp');?></td>
                            <td><input <?php if (get_option('plp_displayimage') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displayimage" value="1" /></td>
                            <td style="white-space: nowrap"><?php _e('Display in name column', 'plp');?>: <input <?php if (get_option('plp_imageintitle') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_imageintitle" value="1" /></td>
                            <td><input value="<?php echo get_option('plp_imagecaption');?>" type="text" name="plp_imagecaption" /></td>
                            <td>image=true/false</td>
                        </tr>
                        <?php break;
                     case 'excerpt': ?>
                        <tr>
                <td><input type="hidden" name="fieldOrder[excerpt]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Excerpt', 'plp');?></td>
                <td><input <?php if (get_option('plp_displayexcerpt') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displayexcerpt" value="1" /></td>
                <td style="white-space: nowrap"><?php _e('Excerpt Length', 'plp');?>: <input value="<?php echo (int)get_option('plp_excerptlength');?>" type="number" min="0" max="999" name="plp_excerptlength" /> <?php _e('characters', 'plp');?>.</td>
                <td><input value="<?php echo get_option('plp_excerptcaption');?>" type="text" name="plp_excerptcaption" /></td>
                <td>excerpt=true/false</td>
            </tr>
                        <?php break;
                     case 'content': ?>
            <tr>
                <td><input type="hidden" name="fieldOrder[content]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Content', 'plp');?></td>
                <td><input <?php if (get_option('plp_displaycontent') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displaycontent" value="1" /></td>
                <td style="white-space: nowrap"><?php _e('Content Length', 'plp');?>: <input value="<?php echo (int)get_option('plp_contentlength');?>" type="number" min="0" max="999" name="plp_contentlength" /> <?php _e('characters', 'plp');?>.</td>
                <td><input value="<?php echo get_option('plp_contentcaption');?>" type="text" name="plp_contentcaption" /></td>
                <td>content=true/false</td>
            </tr>
                        <?php break;
                     case 'price': ?>
            <tr>
                <td><input type="hidden" name="fieldOrder[price]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Price', 'plp');?></td>
                <td><input <?php if (get_option('plp_displayprice') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displayprice" value="1" /></td>
                <td></td>
                <td><input value="<?php echo get_option('plp_pricecaption');?>" type="text" name="plp_pricecaption" /></td>
                <td>price=true/false</td>
            </tr>
            <?php break;
                     case 'stock': ?>
            <tr>
                <td><input type="hidden" name="fieldOrder[stock]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Stock', 'plp');?></td>
                <td><input <?php if (get_option('plp_displaystock') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displaystock" value="1" /></td>
                <td></td>
                <td><input value="<?php echo get_option('plp_stockcaption');?>" type="text" name="plp_stockcaption" /></td>
                <td>stock=true/false</td>
            </tr>
                <?php break;
                     case 'sku': ?>
            <tr>
                <td><input type="hidden" name="fieldOrder[sku]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('SKU', 'plp');?></td>
                <td><input <?php if (get_option('plp_displaysku') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_displaysku" value="1" /></td>
                <td></td>
                <td><input value="<?php echo get_option('plp_skucaption');?>" type="text" name="plp_skucaption" /></td>
                <td>sku=true/false</td>
            </tr>
                <?php break;
                     case 'variation': ?>
            <tr>
                <td><input type="hidden" name="fieldOrder[variation]" class="fieldOrder" value="<?php echo $count; $count+=1; ?>" /><?php _e('Variations', 'plp');?></td>
                <td><input <?php if (get_option('plp_display_variation') == '1') { echo 'checked'; } ?> type="checkbox" name="plp_display_variation" value="1" /></td>
                <td></td>
                <td><input value="<?php echo get_option('plp_variation_caption');?>" type="text" name="plp_variation_caption" /></td>
                <td>variation=true/false</td>
            </tr>
                <?php break;?>
                <?php } ?>
            <?php endforeach;?>
        </tbody>
    </table>
            <h3><?php _e('Other Options', 'plp');?></h3>
        <p>
            <label for="plp_css"><?php _e("Load Stylesheet", 'plp'); ?></label>
            <select name="plp_css" id="plp_css">
                <option <?php if (get_option('plp_css') == 'yes') { echo 'selected'; } ?> value="yes"><?php _e('Yes', 'plp');?></option>
                <option <?php if (get_option('plp_css') == 'no') { echo 'selected'; } ?> value="no"><?php _e('No', 'plp');?></option>
            </select>
        </p>
        <p>
            <label for="plp_displaytitles"><?php _e("Display field titles", 'plp'); ?></label>
            <select name="plp_displaytitles" id="plp_displaytitles">
                <option <?php if (get_option('plp_displaytitles') == 'yes') { echo 'selected'; } ?> value="yes"><?php _e('Yes', 'plp');?></option>
                <option <?php if (get_option('plp_displaytitles') == 'no') { echo 'selected'; } ?> value="no"><?php _e('No', 'plp');?></option>
            </select>
        </p>
        <p>
            <label for="plp_displayvariations"><?php _e("Display Variations", 'plp'); ?></label>
            <select name="plp_displayvariations" id="plp_displayvariations">
                <option <?php if (get_option('plp_displayvariations') == 'yes') { echo 'selected'; } ?> value="yes"><?php _e('Yes', 'plp');?></option>
                <option <?php if (get_option('plp_displayvariations') == 'no') { echo 'selected'; } ?> value="no"><?php _e('No', 'plp');?></option>
            </select>
        </p>
        <p>
            <label for="plp_orderby"><?php _e("Order by", 'plp'); ?></label>
            <select name="plp_orderby">
                <option <?php if (get_option('plp_orderby') == 'name') { echo 'selected'; } ?> value="name"><?php _e('Product Name', 'plp');?></option>
                <option <?php if (get_option('plp_orderby') == 'ID') { echo 'selected'; } ?> value="ID"><?php _e('Product ID', 'plp');?></option>
            </select>
            <select name="plp_order">
                <option <?php if (get_option('plp_order') == 'ASC') { echo 'selected'; } ?> value="ASC"><?php _e('ASC', 'plp');?></option>
                <option <?php if (get_option('plp_order') == 'DESC') { echo 'selected'; } ?> value="DESC"><?php _e('DESC', 'plp');?></option>
            </select>
        </p>
        <p>
            <label for="addtocartposition"><?php _e("Add to cart button position", 'plp'); ?></label>
            <select name="addtocartposition" id="addtocartposition">
                <option <?php if (get_option('addtocartposition') == 'both') { echo 'selected'; } ?> value="both"><?php _e('Both', 'plp');?></option>
                <option <?php if (get_option('addtocartposition') == 'top') { echo 'selected'; } ?> value="top"><?php _e('Top', 'plp');?></option>
                <option <?php if (get_option('addtocartposition') == 'bottom') { echo 'selected'; } ?> value="bottom"><?php _e('Bottom', 'plp');?></option>
                <option <?php if (get_option('addtocartposition') == 'product') { echo 'selected'; } ?> value="product"><?php _e('Next to product', 'plp');?></option>
                <option <?php if (get_option('addtocartposition') == 'none') { echo 'selected'; } ?> value="none"><?php _e('None', 'plp');?></option>
            </select>
        </p>
        <p>
            <label for="plp_custom_err_msg"><?php _e('Custom error message', 'plp');?>:</label><br />
            <input type="text" style="width: 80%;" name="plp_custom_err_msg" id="plp_custom_err_msg" value="<?php echo trim(get_option('plp_custom_err_msg'));?>" />
        </p>
        <p>
            <label for="plp_custom_success_msg"><?php _e('Custom success message', 'plp');?>:</label><br />
            <input type="text" style="width: 80%;" name="plp_custom_success_msg" id="plp_custom_success_msg" value="<?php echo trim(get_option('plp_custom_success_msg'));?>" />
        </p>
        <p>
            <label for="plp_customcss"><?php _e('Custom CSS', 'plp');?>:</label><br />
            <textarea style="width: 80%; height: 80px" name="plp_customcss" id="plp_customcss"><?php echo trim(get_option('plp_customcss'));?></textarea>
        </p>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
        <div style="float:right; font-size: smaller;">
            Powered by <a href="http://www.creativeg.gr">creativeG - WordPress Solutions</a>.
        </div>
    </form>
    </div>
    <script>
	jQuery(function() {
		jQuery( "#fields tbody" ).sortable({
                    stop: function(event, ui){
                        var i = 1;
                        jQuery( "#fields tbody tr" ).each(function(){
                            jQuery(this).find('.fieldOrder').val(i);
                            i+=1;
                        });
                    }
                });
	});
	</script>
    <?php
}