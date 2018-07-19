<?php
/*
 * Copyright (c) 2005-2013 Basilis Kanonidis - creativeg.gr
 * All Rights Reserved.
 * This software is the proprietary information of creativeg.
 */
add_action('wp_loaded', 'plp_cartcontent');
add_action("admin_enqueue_scripts", "plp_admininit");
add_filter('contextual_help', 'plp_help', 10, 3);
if (! function_exists('pramnos_shortenText')) {
    /**
     * Similar to substr, but it never splits a word.
     *
     * @param string $text The text you want to shorten
     * @param int $length Number of characters
     * @param string $moreText Added to text to display that its shorten
     * @param string $charset
     *
     * @return string
     */
    function pramnos_shortenText($text, $length, $moreText = '&hellip;', $charset = 'utf-8')
    {
        if (! is_numeric($length)) {
            throw new Exception('Invalid length');
        }
        if ($length == 0) {
            return $text;
        }
        $ex = trim(strip_tags($text));
        if (mb_strlen($ex, $charset) > $length) {
            $last_space = mb_strrpos(mb_substr($ex, 0, $length, $charset), ' ', $charset);
            $ex         = mb_substr($ex, 0, $last_space, $charset) . $moreText;
        }

        return nl2br($ex);
    }
}
function plp_cartcontent()
{
    global $woocommerce;
    $poj = new WC_Product_Factory();

    $count = 0;
    if (isset($_POST['productadd'])) {

        $woocommerce->cart = new WC_Cart();
        $woocommerce->cart->get_cart_from_session();

        foreach ($_POST['productadd'] as $productId => $quantity) {
            $quantity  = (int) $quantity;
            $productId = (int) $productId;
            $product   = $poj->get_product($productId);
            if ($quantity > 0) {
                if (isset($product->variation_id) && isset($product->parent)) {
                    $woocommerce->cart->add_to_cart($product->parent->id, $quantity, $product->variation_id, $product->get_variation_attributes());
                } else {
                    $woocommerce->cart->add_to_cart($productId, $quantity);
                }
                $count ++;
            }
        }
        if ($count >= 1) {
            $success_msg = get_option('plp_custom_success_msg');
            wc_add_notice($success_msg, "success");
        } else {
            $error_msg = get_option('plp_custom_err_msg');
            wc_add_notice($error_msg, "error");
        }
    }
}

function plp_admininit()
{
    wp_enqueue_script('jquery-ui-sortable');
}

add_shortcode('productslist', 'plp_shortcode');
load_plugin_textdomain('plp', false, basename(dirname(__FILE__)) . '/languages/');


/*  Enqueue javascript
/* ------------------------------------ */
function pl_scripts()
{
    wp_enqueue_script('scripts', plugins_url('pl.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'pl_scripts');


/**
 * @param $atts
 *
 * @return string
 * @throws Exception
 */
function plp_shortcode($atts)
{
    if (get_option('plp_css') == 'yes') {
        wp_register_style('productlistpages', plugins_url() . '/productlistpages/productlistpages.css');
        wp_enqueue_style('productlistpages');
    }
    $permalink = get_permalink();
    $content   = '';
    if (trim(get_option('plp_customcss')) != '') {
        $content .= "<style>\n" . trim(get_option('plp_customcss')) . "\n</style>";
    }
    $content .= '<div class="woocommerce">';
    $fieldOrder = get_option('plp_fieldsorder');
    if (! is_array($fieldOrder)) {
        $fieldOrder = array();
    }
    if (! isset($fieldOrder['name'])) {
        $fieldOrder['name'] = '1';
    }
    if (! isset($fieldOrder['image'])) {
        $fieldOrder['image'] = '2';
    }
    if (! isset($fieldOrder['excerpt'])) {
        $fieldOrder['excerpt'] = '3';
    }
    if (! isset($fieldOrder['content'])) {
        $fieldOrder['content'] = '4';
    }
    if (! isset($fieldOrder['price'])) {
        $fieldOrder['price'] = '5';
    }
    if (! isset($fieldOrder['stock'])) {
        $fieldOrder['stock'] = '6';
    }
    if (! isset($fieldOrder['sku'])) {
        $fieldOrder['sku'] = '7';
    }
    if (! isset($fieldOrder['variation'])) {
        $fieldOrder['variation'] = '8';
    }
    asort($fieldOrder);
    if (isset($atts['posts_per_page'])) {
        wp_reset_query();
    }
    $added = $no_qty = false;
    if (isset($_POST['productadd'])) {
        foreach ($_POST['productadd'] as $productId => $quantity) {
            $quantity  = (int) $quantity;
            $productId = (int) $productId;
            if ($quantity > 0) {
                $added = true;
            } else {
                $no_qty = true;
            }
        }
    }
    if (! isset($atts['titles']) && get_option('plp_displaytitles') == 'yes') {
        $atts['titles'] = 'true';
    }
    if (! isset($atts['imageintitle'])) {
        if (get_option('plp_imageintitle') == '1') {
            $atts['imageintitle'] = 'true';
        } else {
            $atts['imageintitle'] = 'false';
        }
    }
    if (! isset($atts['producttitle'])) {
        if (get_option('plp_displayname') == '1') {
            $atts['producttitle'] = 'true';
        } else {
            $atts['producttitle'] = 'false';
        }
    }
    if (! isset($atts['image'])) {
        if (get_option('plp_displayimage') == '1') {
            $atts['image'] = 'true';
        } else {
            $atts['image'] = 'false';
        }
    }
    if (! isset($atts['stock'])) {
        if (get_option('plp_displaystock') == '1') {
            $atts['stock'] = 'true';
        } else {
            $atts['stock'] = 'false';
        }
    }
    if (! isset($atts['excerpt'])) {
        if (get_option('plp_displayexcerpt') == '1') {
            $atts['excerpt'] = 'true';
        } else {
            $atts['excerpt'] = 'false';
        }
    }
    if (! isset($atts['content'])) {
        if (get_option('plp_displaycontent') == '1') {
            $atts['content'] = 'true';
        } else {
            $atts['content'] = 'false';
        }
    }
    if (! isset($atts['price'])) {
        if (get_option('plp_displayprice') == '1') {
            $atts['price'] = 'true';
        } else {
            $atts['price'] = 'false';
        }
        if (! isset($atts['sku'])) {
            if (get_option('plp_displaysku') == '1') {
                $atts['sku'] = 'true';
            } else {
                $atts['sku'] = 'false';
            }
        }
        if (! isset($atts['variation'])) {
            if (get_option('plp_display_variation') == '1') {
                $atts['variation'] = 'true';
            } else {
                $atts['variation'] = 'false';
            }
        }
        if (! isset($atts['addtocartbutton'])) {
            $atts['addtocartbutton'] = get_option('addtocartposition');
        }

        $content .= '<div class="woocommerce">';
        ob_start();
        wc_print_notices();
        $content .= ob_get_clean();
        $content .= '</div>';

        if ($atts['addtocartbutton'] != 'product') {
            $content .= '<form method="post" action="' . $permalink . '">';
        }
        if ($atts['addtocartbutton'] == 'both' || $atts['addtocartbutton'] == 'top') {
            $content .= '<div align="center">' . '<p>' . '<input class="checkout-button button alt" type="submit" name="SUBMIT" value="' . __('Add to Cart', 'plp') . '" alt="' . __('Add to Cart', 'plp') . '">
                </p><br></div>';
        }
        $content .= '<table class="ProductListShortcode shop_table cart" border="0" width="100%" cellspacing="10" cellpadding="4">';
        if (isset($atts['titles']) && $atts['titles'] == 'true') {
            $content .= '<thead>' . '<tr>';
            foreach ($fieldOrder as $field => $order) {
                switch ($field) {
                    case 'name':
                        if ($atts['producttitle'] == 'true') {
                            if (get_option('plp_namecaption') != '') {
                                $content .= '<th>' . get_option('plp_namecaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Title', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'image':
                        if ($atts['image'] == 'true' && $atts['imageintitle'] != 'true') {
                            if (get_option('plp_imagecaption') != '') {
                                $content .= '<th>' . get_option('plp_imagecaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Image', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'excerpt':
                        if ($atts['excerpt'] == 'true') {
                            if (get_option('plp_excerptcaption') != '') {
                                $content .= '<th>' . get_option('plp_excerptcaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Description', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'content':
                        if ($atts['content'] == 'true') {
                            if (get_option('plp_contentcaption') != '') {
                                $content .= '<th>' . get_option('plp_contentcaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Content', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'price':
                        if ($atts['price'] == 'true') {
                            if (get_option('plp_pricecaption') != '') {
                                $content .= '<th>' . get_option('plp_pricecaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Price', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'stock':
                        if ($atts['stock'] == 'true') {
                            if (get_option('plp_stockcaption') != '') {
                                $content .= '<th>' . get_option('plp_stockcaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Stock', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'sku':
                        if ($atts['sku'] == 'true') {
                            if (get_option('plp_skucaption') != '') {
                                $content .= '<th>' . get_option('plp_skucaption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Sku', 'plp') . '</th>';
                            }
                        }
                        break;
                    case 'variation':
                        if ($atts['variation'] == 'true') {
                            if (get_option('plp_variation_caption') != '') {
                                $content .= '<th>' . get_option('plp_variation_caption') . "</th>\n";
                            } else {
                                $content .= '<th>' . __('Variations', 'plp') . '</th>';
                            }
                        }
                        break;

                }
            }
            $content .= '</tr>' . '</thead>';
        }
        $content . '<tbody>';
        $query_args = array(
            'post_status' => 'publish',
            'post_type' => 'product'
        );
        if (isset($atts['id'])) {
            $ids                    = explode(',', $atts['id']);
            $query_args['post__in'] = $ids;
        }
        if (isset($atts['posts_per_page'])) {
            $paged                        = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query_args['posts_per_page'] = (int) $atts['posts_per_page'];
            $query_args['paged']          = $paged;
        } else {
            $query_args['posts_per_page'] = 99999;
            $query_args['nopaging']       = 1;
        }
        if (isset($atts['orderby'])) {
            $query_args['orderby'] = trim($atts['orderby']);
        } elseif (get_option('plp_orderby') != '') {
            $query_args['orderby'] = get_option('plp_orderby');
        }
        if (isset($atts['order'])) {
            $query_args['order'] = trim(strtoupper($atts['order']));
        } elseif (get_option('plp_order') != '') {
            $query_args['order'] = get_option('plp_order');
        }
        if (isset($atts['cat'])) {
            $field   = 'ID';
            $catsatt = explode(',', $atts['cat']);
            foreach ($catsatt as $ca) { //If any category is not given by ID, switch to Slug
                if (! is_numeric($ca)) {
                    $field = 'slug';
                }
            }
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => $field,
                    'terms' => $catsatt,
                    'operator' => 'IN'
                )
            );
        }
        wp_reset_query();
        global $wp_query;
        $wp_query = new WP_Query();
        $poj      = new WC_Product_Factory();
        $wp_query->query($query_args);
        if ($wp_query->have_posts()):
            while ($wp_query->have_posts()):
                $wp_query->the_post();
                $product = $poj->get_product(get_the_ID());
                //var_dump(get_option('plp_displayvariations') );
                if ($product->is_purchasable()):
                    $hasVariatoon = false;
                    if (get_option('plp_displayvariations') == 'yes') {
                        $variationQuery = new WP_Query();
                        $attributes     = $product->get_attributes();
                        $args           = array(
                            'post_status' => 'publish',
                            'post_type' => 'product_variation',
                            'post_parent' => $product->ID,
                            // 1.6 patch apply for ordering
                            // 'orderby'=>'menu_order',
                            // 'order' => 'asc'
                        );
                        $variationQuery->query($args);
                        if ($variationQuery->have_posts()):
                            $hasVariatoon = true;
                        endif;
                    }

                    if ($hasVariatoon == true) {
                        $originalTitle = get_the_title();
                        $varTitle      = $originalTitle;
                        $link          = get_permalink();
                        /*$var_atts = array();
                        while ($variationQuery->have_posts()) :
                        $variationQuery->the_post();
                        $product = WC_Product_Factory::get_product(get_the_ID());
                        $varTitle = $originalTitle;
                        $var_atts[] = $product->get_variation_attributes();
                        
                        if (is_array($var_atts)) {
                        $comma = ' - ';
                        foreach ($var_atts as $data) {
                        $varTitle .= $comma . urldecode($data) . ' ';
                        $comma = ', ';
                        }
                        }
                        
                        endwhile;
                        
                        print_r($var_atts);
                        $var_dropdown = '';
                        if (is_array($var_atts)) {
                        $var_dropdown .= '<select name="variation">';
                        foreach ($var_atts as $key => $data) {
                        foreach($data as $varition => $val){
                        $var_dropdown .= '<option value="'.urldecode($val).'">'.urldecode($val).'</option>';
                        }
                        $comma = ', ';
                        }
                        $var_dropdown .= '</select>';
                        }
                        */
                        $var_dropdown = $cls = '';
                        $alt          = 1;
                        $var_dropdown .= '<table style="border:none;">';
                        $var_dropdown .= '<tr>';
                        foreach ($attributes as $attribute):
                            if (! ($attribute['is_taxonomy'] && taxonomy_exists($attribute['name']))) {
                                continue;
                            } else {
                                $has_row = true;
                            }
                            if (($alt = $alt * - 1) == 1) {
                                $cls = 'alt';
                            }
                            $var_dropdown .= '<td class="' . $cls . '" style="border:none; width: 180px;">';
                            $var_dropdown .= wc_attribute_label($attribute['name']);
                            //$content .= '<td id="variationtd">';
                            if ($attribute['is_taxonomy']) {
                                $values = wc_get_product_terms($product->id, $attribute['name'], array(
                                    'fields' => 'names'
                                ));
                                $var_dropdown .= '<select name="' . $attribute['name'] . '-' . $product->id . '">';
                                foreach ($values as $val) {
                                    $var_dropdown .= '<option value="' . $val . '">' . $val . '</option>';
                                }
                                $var_dropdown .= '</select>';
                            } else {
                                // Convert pipes to commas and display values
                                $values = array_map('trim', explode(WC_DELIMITER, $attribute['value']));
                                $var_dropdown .= '<select name="' . $attribute['name'] . '[' . $product->id . ']">';
                                foreach ($values as $val) {
                                    $var_dropdown .= '<option value="' . $val . '">' . $val . '</option>';
                                }
                                $var_dropdown .= '</select>';
                            }
                            $var_dropdown .= '</td>';
                        endforeach;
                        $var_dropdown .= '</tr>';
                        $var_dropdown .= '</table>';
                        $content .= '<tr>';
                        foreach ($fieldOrder as $field => $order) {
                            switch ($field) {
                                case 'name':
                                    if ($atts['producttitle'] == 'true') {
                                        if (isset($atts['image']) && $atts['image'] == 'true' && $atts['imageintitle'] == 'true') {
                                            $gal_images = get_post_gallery_images(get_the_ID());
                                            $content .= '<td>' . get_the_post_thumbnail(get_the_ID(), array(
                                                    200,
                                                    200
                                                ));
                                            if ($atts['producttitle'] == 'true') {
                                                $content .= '<br />' . $varTitle;
                                            }
                                            $content .= '</td>';
                                        } else {
                                            $content .= '<td>' . $varTitle . '</td>';
                                        }
                                    }
                                    break;
                                case 'image':
                                    if ($atts['image'] == 'true' && $atts['imageintitle'] != 'true') {
                                        $attachment_ids = $product->get_gallery_attachment_ids();
                                        $image_links    = [];
                                        $content .= '<td class="image">';
                                        foreach ($attachment_ids as $attachment_id) {
                                            $image_links[] = wp_get_attachment_url($attachment_id);
                                            $content .= '<a href="' . wp_get_attachment_url($attachment_id) . '" data-lightbox="image-'. get_the_ID() .'" data-title="' . $product->get_title() . '"><img src="' . wp_get_attachment_url($attachment_id) . '"' . ' alt="picture" /></a>';
//                                            $content .= ;
                                        }
                                        $content .= '</td>';
                                    }
                                    break;
                                case 'excerpt':
                                    if ($atts['excerpt'] == 'true') {
                                        $content .= '<td>' . pramnos_shortenText(get_the_excerpt(), get_option('plp_excerptlength')) . '</td>';
                                    }
                                    break;
                                case 'content':
                                    //customized content here - Brian3T
                                    if ($atts['content'] == 'true') {
                                        $content .= '<td class="content">';
                                        $content .= "<div class='title'><b>$varTitle</b></div>";
                                        $content .= "<span class='price'>". woocommerce_price($product->get_price()) . "</span>";
                                        if ($product->is_in_stock()){
                                        $content .=  '<div class="quantity buttons_added">' . '<input type="button" value="-" class="minus"><input class="input-text qty text" type="number" step="1" min="0" max="' . apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product) . '" name="productadd[' . get_the_ID() . ']" id="productadd_' . get_the_ID() . '" value="0" size="2" maxlength="3">' . '<input type="button" value="+" class="plus"></div>' . '<input class="checkout-button button alt enthusiast" type="button" name="add_to_cart" value="Add to Cart" alt="Add to Cart">';
                                        } else {
                                            $content .= '<span class="sold">SOLD</span>';
                                        }
                                        $content .= "<div class='content'>" . pramnos_shortenText(wpautop(get_the_content(), true), get_option('plp_contentlength')) ."</div>";

                                        $content .= '</td>';
                                    }
                                    ////customized content here - Brian3T
                                    break;
                                case 'price':
                                    if ($atts['price'] == 'true') {
                                        $content .= '<td>' . woocommerce_price($product->get_price()) . '</td>';
                                    }
                                    break;
//                                case 'stock':
//                                    if ($atts['stock'] == 'true') {
//                                        $content .= '<td>' . $product->get_stock_quantity() . '</td>';
//                                    }
//                                    break;
//                                case 'sku':
//                                    if ($atts['sku'] == 'true') {
//                                        $content .= '<td>' . $product->get_sku() . '</td>';
//                                    }
//                                    break;
//                                case 'variation':
//                                    if (strpos($var_dropdown, '</option>') === false) {
//                                        $content .= '<td>None</td>';
//                                    } else if ($atts['variation'] == 'true') {
//                                        $content .= '<td>' . $var_dropdown . '</td>';
//                                    }
//
//                                    break;
                            }
                        }
                        if ($atts['addtocartbutton'] != 'product') {
//                            $content .= '<td class="product-quantity">' . '<div class="quantity buttons_added">' . '<input type="button" value="-" class="minus"><input class="input-text qty text" type="number" step="1" min="0" max="' . apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product) . '" name="productadd[' . get_the_ID() . ']" id="productadd_' . get_the_ID() . '" value="0" size="2" maxlength="3">' . '<input type="button" value="+" class="plus"></div></td>';
                        } else {
                            if ($product->is_purchasable() && $product->is_in_stock()) {
                                $content .= '<td class="product-quantity" style="white-space:nowrap;">' . '<form action="' . esc_url($product->add_to_cart_url()) . '" class="cart" method="post" enctype=\'multipart/form-data\'>';
                                $attributes = $product->get_variation_attributes();
                                if (is_array($attributes)) {
                                    foreach ($attributes as $attribute => $value) {
                                        $content .= '<input type="hidden" name="' . $attribute . '" value="' . implode($value, ',') . '" />' . "\n";
                                    }
                                }
                                $content .= '<input type="hidden" name="product_id" value="' . get_the_ID() . '" />' . "\n";
                                $content .= '<input type="hidden" name="variation_id" value="' . $product->variation_id . '" />' . "\n";
                                if (! $product->is_sold_individually() && apply_filters('woocommerce_quantity_input_min', 1, $product) != 1) {
                                    $content .= '<div class="quantity buttons_added">' . '<input type="button" value="-" class="minus"><input class="input-text qty text" type="number" step="1" min="' . apply_filters('woocommerce_quantity_input_min', 1, $product) . '" max="' . apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product) . '" name="quantity" id="quantity_' . get_the_ID() . '" value="' . apply_filters('woocommerce_quantity_input_min', 1, $product) . '" size="2" maxlength="3">' . '<input type="button" value="+" class="plus"></div>';
                                } else {
                                    $content .= '<input type="hidden" name="quantity" value="1" />';
                                }
                                $content .= '<button type="submit" class="single_add_to_cart_button button alt">' . apply_filters('single_add_to_cart_text', __('Add to cart', 'woocommerce'), $product->product_type) . '</button>' . '</form>' . '</td>';
                            }
                        }
                        $content .= '</tr>';
                        //endwhile;
                    } else {
                        $content .= '<tr>';
                        foreach ($fieldOrder as $field => $order) {
                            switch ($field) {
                                case 'name':
                                    if ($atts['producttitle'] == 'true') {
                                        if (isset($atts['image']) && $atts['image'] == 'true' && $atts['imageintitle'] == 'true') {
                                            $content .= '<td>' . '">' . get_the_post_thumbnail(get_the_ID(), array(
                                                    200,
                                                    200
                                                ));
                                            if ($atts['producttitle'] == 'true') {
                                                $content .= '<br />' . get_the_title();
                                            }
                                            $content .= '</td>';
                                        } else {
                                            $content .= '<td>' . get_the_title() . '</td>';
                                        }
                                    }
                                    break;
                                case 'image':
                                    if ($atts['image'] == 'true' && $atts['imageintitle'] != 'true') {
                                        $attachment_ids = $product->get_gallery_attachment_ids();
                                        $image_links    = [];
                                        $content .= '<td>';
                                        foreach ($attachment_ids as $attachment_id) {
                                            $image_links[] = wp_get_attachment_url($attachment_id);
                                            $content .= '<img src="' . wp_get_attachment_url($attachment_id) . '"' . ' alt="picture" />';
                                        }
                                        $content .= '</td>';
                                    }
                                    break;
                                case 'excerpt':
                                    if ($atts['excerpt'] == 'true') {
                                        $content .= '<td>' . pramnos_shortenText(get_the_excerpt(), get_option('plp_excerptlength')) . '</td>';
                                    }
                                    break;
                                case 'content':
                                    if ($atts['content'] == 'true') {
                                        $content .= '<td>' . pramnos_shortenText(get_the_content(), get_option('plp_contentlength')) . '</td>';
                                    }
                                    break;
                                case 'price':
                                    if ($atts['price'] == 'true') {
                                        $content .= '<td>' . woocommerce_price($product->get_price()) . '</td>';
                                    }
                                    break;
                                case 'stock':
                                    if ($atts['stock'] == 'true') {
                                        $content .= '<td>' . $product->get_stock_quantity() . '</td>';
                                    }
                                    break;
                                case 'sku':
                                    if ($atts['sku'] == 'true') {
                                        $content .= '<td>' . $product->get_sku() . '</td>';
                                    }
                                    break;
                            }
                        }
//                        if ($atts['addtocartbutton'] != 'product') {
//                            $content .= '<td class="product-quantity">' . '<div class="quantity buttons_added">' . '<input type="button" value="-" class="minus"><input class="input-text qty text" type="number" step="1" min="0" max="' . apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product) . '" name="productadd[' . $product->id . ']" id="productadd_' . get_the_ID() . '" value="0" size="2" maxlength="3">' . '<input type="button" value="+" class="plus"></div></td>';
//                        } else {
//                            //add to cart btn
//                            $content .= '<td class="product-quantity" style="white-space:nowrap;">' . '<form action="' . esc_url($product->add_to_cart_url()) . '" class="cart" method="post" enctype=\'multipart/form-data\'>';
//                            if (! $product->is_sold_individually() && apply_filters('woocommerce_quantity_input_min', 1, $product) != 1) {
//                                $content .= '<div class="quantity buttons_added">' . '<input type="button" value="-" class="minus"><input class="input-text qty text" type="number" step="1" min="' . apply_filters('woocommerce_quantity_input_min', 1, $product) . '" max="' . apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product) . '" name="quantity" id="quantity_' . get_the_ID() . '" value="' . apply_filters('woocommerce_quantity_input_min', 1, $product) . '" size="2" maxlength="3">' . '<input type="button" value="+" class="plus"></div>';
//                            } else {
//                                $content .= '<input type="hidden" name="quantity" value="1" />';
//                            }
//                            $content .= '<button type="submit" class="single_add_to_cart_button button alt">' . apply_filters('single_add_to_cart_text', __('Add to cart', 'woocommerce'), $product->product_type) . '</button>' . '</form>' . '</td>';
//                        }
                        $content .= '</tr>';
                    }
                endif;
            endwhile;
        endif;
        $content .= '</tbody></table>';
        if (isset($atts['posts_per_page'])) {
            $content .= '<div class="navigation clearboth" >' . '<div class="alignleft">' . get_previous_posts_link() . '</div>' . '<div class="alignright">' . get_next_posts_link() . '</div></div>';
        }
        if ($atts['addtocartbutton'] == 'both' || $atts['addtocartbutton'] == 'bottom') {
            $content .= '<div class="clearboth" align="center">' . '<p>' . '<input  class="checkout-button button alt" type="submit" name="SUBMIT" value="' . __('Add to Cart', 'plp') . '"  alt="' . __('Add to Cart', 'plp') . '">' . '</p>' . '<br>' . '</div>';
        }
        if ($atts['addtocartbutton'] != 'product') {
            $content .= '</form>';
        }
        $content .= '</div>';
        wp_reset_query();

        return $content;
    }
}