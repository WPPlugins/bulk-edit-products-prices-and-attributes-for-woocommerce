<?php
if (!defined('ABSPATH')) {
    exit;
}
add_action('wp_ajax_eh_bep_get_attributes_action', 'eh_bep_get_attributes_action_callback');
add_action('wp_ajax_eh_bep_count_products', 'eh_bep_count_products_callback');
add_action('wp_ajax_eh_bep_all_products', 'eh_bep_list_table_all_callback');
add_action('wp_ajax_eh_bep_clear_products', 'eh_clear_all_callback');
add_action('wp_ajax_eh_bep_update_products', 'eh_bep_update_product_callback');
add_action('wp_ajax_eh_bulk_edit_display_count', 'eh_bulk_edit_display_count_callback');
add_action('wp_ajax_eh_bep_filter_products', 'eh_bep_search_filter_callback');
function eh_bulk_edit_display_count_callback()
{
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    $value=  sanitize_text_field($_POST['row_count']);
    update_option('eh_bulk_edit_table_row', $value);
    die('success');
}
function eh_bep_count_products_callback()
{
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    session_start();
    $count_product_arr=array('product'=>count($_SESSION['product_ids']));
    die(json_encode($count_product_arr));
}
function eh_bep_get_attributes_action_callback()
{   
    $attribute_name = sanitize_text_field($_POST['attrib']);
    $cat_args       = array(
        'hide_empty' => true,
        'order' => 'ASC'
    );
    $attributes     = wc_get_attribute_taxonomies();
    foreach ($attributes as $key => $value) {
        if ($attribute_name == $value->attribute_name) {
            $attribute_name  = $value->attribute_name;
            $attribute_label = $value->attribute_label;
        }
    }
    $attribute_value = get_terms('pa_' . $attribute_name, $cat_args);
    $return          = "<optgroup label='" . $attribute_label . "' id='grp_" . $attribute_name . "'>";
    foreach ($attribute_value as $key => $value) {
        $return .= "<option value='" . $value->slug . "'>" . $value->name . "</option>";
    }
    $return .= "</optgroup>";
    echo $return;
    exit;
}
function eh_bep_update_product_callback()
{
    set_time_limit(300);
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    session_start();    
    $undo_product_data      = array();
    $title_select           = $_POST['title_select'];
    $sku_select             = $_POST['sku_select'];
    $catalog_select         = $_POST['catalog_select'];
    $shipping_select        = $_POST['shipping_select'];
    $sale_select            = $_POST['sale_select'];
    $regular_select         = $_POST['regular_select'];
    $stock_manage_select    = $_POST['stock_manage_select'];
    $quantity_select        = $_POST['quantity_select'];
    $backorder_select       = $_POST['backorder_select'];
    $stock_status_select    = $_POST['stock_status_select'];
    $length_select          = $_POST['length_select'];
    $width_select           = $_POST['width_select'];
    $height_select          = $_POST['height_select'];
    $weight_select          = $_POST['weight_select'];
    $title_text             = sanitize_text_field($_POST['title_text']);
    $replace_title_text     = sanitize_text_field($_POST['replace_title_text']);
    $sku_text               = sanitize_text_field($_POST['sku_text']);
    $sku_replace_text       = sanitize_text_field($_POST['sku_replace_text']);
    $sale_text              = $_POST['sale_text'];
    $regular_text           = $_POST['regular_text'];
    $quantity_text          = $_POST['quantity_text'];
    $length_text            = $_POST['length_text'];
    $width_text             = $_POST['width_text'];
    $height_text            = $_POST['height_text'];
    $weight_text            = $_POST['weight_text'];
    $hide_price             = sanitize_text_field($_POST['hide_price']);
    $hide_price_role        = (sanitize_text_field($_POST['hide_price_role']) != '') ? sanitize_text_field($_POST['hide_price_role']) : '';
    $price_adjustment       = sanitize_text_field($_POST['price_adjustment']);
    $product_id             = $_SESSION['product_ids'];
    for ($i = 0; $i < count($product_id); $i++) {
        apply_filters('http_request_timeout', 30);
        switch ($hide_price) {
            case 'yes':
                update_post_meta($product_id[$i], 'product_adjustment_hide_price_unregistered', 'yes');
                break;
            case 'no':
                update_post_meta($product_id[$i], 'product_adjustment_hide_price_unregistered', 'no');
                break;
        }
        switch ($price_adjustment) {
            case 'yes':
                update_post_meta($product_id[$i], 'product_based_price_adjustment', 'yes');
                break;
            case 'no':
                update_post_meta($product_id[$i], 'product_based_price_adjustment', 'no');
                break;
        }
        if ($hide_price_role != '') {
            update_post_meta($product_id[$i], 'eh_pricing_adjustment_product_price_user_role', $hide_price_role);
        }
        $temp = wc_get_product($product_id[$i]);
        $temp_type = (WC()->version < '2.7.0') ? $temp->product_type : $temp->get_type();
        $temp_title = (WC()->version < '2.7.0') ? $temp->post->post_title : $temp->get_title();
        if ($temp_type == 'simple') {
            $undo_product_data                   = array();
            $undo_product_data['type']           = 'simple';
            $undo_product_data['title']          = $temp_title;
            $undo_product_data['sku']            = ($temp->get_sku() != null) ? $temp->get_sku() : "";
            $undo_product_data['catalog']        = get_post_meta($product_id[$i], '_visibility', true);
            $undo_product_data['shipping']       = $temp->get_shipping_class_id();
            $undo_product_data['sale']           = $temp->get_sale_price();
            $undo_product_data['regular']        = $temp->get_regular_price();
            $undo_product_data['stock_manage']   = get_post_meta($product_id[$i], '_manage_stock', true);
            $undo_product_data['stock_quantity'] = get_post_meta($product_id[$i], '_stock', true);
            $undo_product_data['backorder']      = get_post_meta($product_id[$i], '_backorders', true);
            $undo_product_data['stock_status']   = get_post_meta($product_id[$i], '_stock_status', true);
            $undo_product_data['length']         = get_post_meta($product_id[$i], '_length', true);
            $undo_product_data['width']          = get_post_meta($product_id[$i], '_width', true);
            $undo_product_data['height']         = get_post_meta($product_id[$i], '_height', true);
            $undo_product_data['weight']         = get_post_meta($product_id[$i], '_weight', true);
            switch ($title_select) {
                case 'set_new':
                    $my_post = array(
                        'ID' => $product_id[$i],
                        'post_title' => $title_text
                    );
                    wp_update_post($my_post);
                    break;
                case 'append':
                    $my_post = array(
                        'ID' => $product_id[$i],
                        'post_title' => $undo_product_data['title'] . $title_text
                    );
                    wp_update_post($my_post);
                    break;
                case 'prepand':
                    $my_post = array(
                        'ID' => $product_id[$i],
                        'post_title' => $title_text . $undo_product_data['title']
                    );
                    wp_update_post($my_post);
                    break;
                case 'replace':
                    $my_post = array(
                        'ID' => $product_id[$i],
                        'post_title' => str_replace($replace_title_text, $title_text, $undo_product_data['title'])
                    );
                    wp_update_post($my_post);
                    break;
            }
            switch ($sku_select) {
                case 'set_new':
                    eh_bep_update_meta_fn($product_id[$i], '_sku', $sku_text);
                    break;
                case 'append':
                    $sku_val = $undo_product_data['sku'] . $sku_text;
                    eh_bep_update_meta_fn($product_id[$i], '_sku', $sku_val);
                    break;
                case 'prepand':
                    $sku_val = $sku_text . $undo_product_data['sku'];
                    eh_bep_update_meta_fn($product_id[$i], '_sku', $sku_val);
                    break;
                case 'replace':
                    $sku_val = str_replace($sku_replace_text, $sku_text, $undo_product_data['sku']);
                    eh_bep_update_meta_fn($product_id[$i], '_sku', $sku_val);
                    break;
            }
            switch ($catalog_select) {
                case 'visible':
                    eh_bep_update_meta_fn($product_id[$i], '_visibility', 'visible');
                    break;
                case 'catalog':
                    eh_bep_update_meta_fn($product_id[$i], '_visibility', 'catalog');
                    break;
                case 'search':
                    eh_bep_update_meta_fn($product_id[$i], '_visibility', 'search');
                    break;
                case 'hidden':
                    eh_bep_update_meta_fn($product_id[$i], '_visibility', 'hidden');
                    break;
            }
            if ($shipping_select != '') {
                wp_set_object_terms((int) $product_id[$i], (int) $shipping_select, 'product_shipping_class');
            }
            switch ($sale_select) {
                case 'up_percentage':
                    if($undo_product_data['sale']!=='')
                    {
                        $per_val  = $undo_product_data['sale'] * ($sale_text / 100);
                        $sale_val = wc_format_decimal($undo_product_data['sale'] + $per_val);
                        eh_bep_update_meta_fn($product_id[$i], '_sale_price', $sale_val);
                    }
                    break;
                case 'down_percentage':
                    if($undo_product_data['sale']!=='')
                    {
                        $per_val  = $undo_product_data['sale'] * ($sale_text / 100);
                        $sale_val = wc_format_decimal($undo_product_data['sale'] - $per_val);
                        eh_bep_update_meta_fn($product_id[$i], '_sale_price', $sale_val);
                    }
                    break;
                case 'up_price':
                    if($undo_product_data['sale']!=='')
                    {
                        $sale_val = $undo_product_data['sale'] + $sale_text;
                        eh_bep_update_meta_fn($product_id[$i], '_sale_price', $sale_val);
                    }
                    break;
                case 'down_price':
                    if($undo_product_data['sale']!=='')
                    {
                        $sale_val = $undo_product_data['sale'] - $sale_text;
                        eh_bep_update_meta_fn($product_id[$i], '_sale_price', $sale_val);
                    }
                    break;
                case 'flat_all':
                    $sale_val = $sale_text;
                    eh_bep_update_meta_fn($product_id[$i], '_sale_price', $sale_val);
                    break;
            }
            switch ($regular_select) {
                case 'up_percentage':
                    if($undo_product_data['regular']!=='')
                    {
                        $per_val     = $undo_product_data['regular'] * ($regular_text / 100);
                        $regular_val = wc_format_decimal($undo_product_data['regular'] + $per_val);
                        eh_bep_update_meta_fn($product_id[$i], '_regular_price', $regular_val);
                    }
                    break;
                case 'down_percentage':
                    if($undo_product_data['regular']!=='')
                    {
                        $per_val     = $undo_product_data['regular'] * ($regular_text / 100);
                        $regular_val = wc_format_decimal($undo_product_data['regular'] - $per_val);
                        eh_bep_update_meta_fn($product_id[$i], '_regular_price', $regular_val);
                    }
                    break;
                case 'up_price':
                    if($undo_product_data['regular']!=='')
                    {
                        $regular_val = $undo_product_data['regular'] + $regular_text;
                        eh_bep_update_meta_fn($product_id[$i], '_regular_price', $regular_val);
                    }
                    break;
                case 'down_price':
                    if($undo_product_data['regular']!=='')
                    {
                        $regular_val = $undo_product_data['regular'] - $regular_text;
                        eh_bep_update_meta_fn($product_id[$i], '_regular_price', $regular_val);
                    }
                    break;
                case 'flat_all':
                    $regular_val = $regular_text;
                    eh_bep_update_meta_fn($product_id[$i], '_regular_price', $regular_val);
                    break;
            }
            if(get_post_meta($product_id[$i], '_sale_price', true)!=='' && get_post_meta($product_id[$i], '_regular_price', true)!=='')
            {
                eh_bep_update_meta_fn($product_id[$i], '_price', get_post_meta($product_id[$i], '_sale_price', true));
            }
            elseif(get_post_meta($product_id[$i], '_sale_price', true)==='' && get_post_meta($product_id[$i], '_regular_price', true)!=='')
            {
                eh_bep_update_meta_fn($product_id[$i], '_price', get_post_meta($product_id[$i], '_regular_price', true));
            }
            elseif(get_post_meta($product_id[$i], '_sale_price', true)!=='' && get_post_meta($product_id[$i], '_regular_price', true)==='')
            {
                eh_bep_update_meta_fn($product_id[$i], '_price', get_post_meta($product_id[$i], '_sale_price', true));
            }
            elseif(get_post_meta($product_id[$i], '_sale_price', true)==='' && get_post_meta($product_id[$i], '_regular_price', true)==='')
            {
                eh_bep_update_meta_fn($product_id[$i], '_price', '');
            }
            switch ($stock_manage_select) {
                case 'yes':
                    eh_bep_update_meta_fn($product_id[$i], '_manage_stock', 'yes');
                    break;
                case 'no':
                    eh_bep_update_meta_fn($product_id[$i], '_manage_stock', 'no');
                    break;
            }
            switch ($quantity_select) {
                case 'add':
                    $quantity_val = number_format($undo_product_data['stock_quantity'] + $quantity_text, 6, '.', '');
                    eh_bep_update_meta_fn($product_id[$i], '_stock', $quantity_val);
                    break;
                case 'sub':
                    $quantity_val = number_format($undo_product_data['stock_quantity'] - $quantity_text, 6, '.', '');
                    eh_bep_update_meta_fn($product_id[$i], '_stock', $quantity_val);
                    break;
                case 'replace':
                    $quantity_val = number_format($quantity_text, 6, '.', '');
                    eh_bep_update_meta_fn($product_id[$i], '_stock', $quantity_val);
                    break;
            }
            switch ($backorder_select) {
                case 'no':
                    eh_bep_update_meta_fn($product_id[$i], '_backorders', 'no');
                    break;
                case 'notify':
                    eh_bep_update_meta_fn($product_id[$i], '_backorders', 'notify');
                    break;
                case 'yes':
                    eh_bep_update_meta_fn($product_id[$i], '_backorders', 'yes');
                    break;
            }
            switch ($stock_status_select) {
                case 'instock':
                    eh_bep_update_meta_fn($product_id[$i], '_stock_status', 'instock');
                    break;
                case 'outofstock':
                    eh_bep_update_meta_fn($product_id[$i], '_stock_status', 'outofstock');
                    break;
            }
            switch ($length_select) {
                case 'add':
                    $length_val = $undo_product_data['length'] + $length_text;
                    eh_bep_update_meta_fn($product_id[$i], '_length', $length_val);
                    break;
                case 'sub':
                    $length_val = $undo_product_data['length'] - $length_text;
                    eh_bep_update_meta_fn($product_id[$i], '_length', $length_val);
                    break;
                case 'replace':
                    $length_val = $length_text;
                    eh_bep_update_meta_fn($product_id[$i], '_length', $length_val);
                    break;
            }
            switch ($width_select) {
                case 'add':
                    $width_val = $undo_product_data['width'] + $width_text;
                    eh_bep_update_meta_fn($product_id[$i], '_width', $width_val);
                    break;
                case 'sub':
                    $width_val = $undo_product_data['width'] - $width_text;
                    eh_bep_update_meta_fn($product_id[$i], '_width', $width_val);
                    break;
                case 'replace':
                    $width_val = $width_text;
                    eh_bep_update_meta_fn($product_id[$i], '_width', $width_val);
                    break;
            }
            switch ($height_select) {
                case 'add':
                    $height_val = $undo_product_data['height'] + $height_text;
                    eh_bep_update_meta_fn($product_id[$i], '_height', $height_val);
                    break;
                case 'sub':
                    $height_val = $undo_product_data['height'] - $height_text;
                    eh_bep_update_meta_fn($product_id[$i], '_height', $height_val);
                    break;
                case 'replace':
                    $height_val = $height_text;
                    eh_bep_update_meta_fn($product_id[$i], '_height', $height_val);
                    break;
            }
            switch ($weight_select) {
                case 'add':
                    $weight_val = $undo_product_data['weight'] + $weight_text;
                    eh_bep_update_meta_fn($product_id[$i], '_weight', $weight_val);
                    break;
                case 'sub':
                    $weight_val = $undo_product_data['weight'] - $weight_text;
                    eh_bep_update_meta_fn($product_id[$i], '_weight', $weight_val);
                    break;
                case 'replace':
                    $weight_val = $weight_text;
                    eh_bep_update_meta_fn($product_id[$i], '_weight', $weight_val);
                    break;
            }
            wc_delete_product_transients($product_id[$i]);
        } 
    }
}
function eh_bep_update_meta_fn($id, $key, $value)
{
    update_post_meta($id, $key, $value);
}
function eh_bep_list_table_all_callback()
{
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    session_start();
    $_SESSION['product_ids'] = eh_bep_get_all_products();
    $obj                     = new Eh_DataTables();
    $obj->input();
    $obj->ajax_response('1');
}
function eh_clear_all_callback()
{
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    session_start();
    $_SESSION['product_ids']=eh_bep_get_first_products();
    $obj = new Eh_DataTables();
    $obj->input();
    $obj->ajax_response();
}
function eh_bep_search_filter_callback()
{
    check_ajax_referer('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
    $filter_type            = $_POST['type'];
    $filter_category        = ($_POST['category'] != '') ? $_POST['category'] : '';
    $filter_attribute       = ($_POST['attribute'] != '') ? explode(',', $_POST['attribute']) : '';
    $filter_attribute_value = ($_POST['attribute_value'] != '') ? $_POST['attribute_value'] : '';
    $filter_range           = $_POST['range'];
    $filter_desired_price   = sanitize_text_field($_POST['desired_price']);
    $filter_minimum_price   = sanitize_text_field($_POST['minimum_price']);
    $filter_maximum_price   = sanitize_text_field($_POST['maximum_price']);
    $all_id                 = eh_bep_get_all_products();
    $filtered_id            = array();
    for ($i = 0; $i < count($all_id); $i++) {
        $temp = wc_get_product($all_id[$i]);
        $temp_type = (WC()->version < '2.7.0') ? $temp->product_type : $temp->get_type();
        $temp_id = (WC()->version < '2.7.0') ? $temp->id : $temp->get_id();
        if ($temp_type == 'simple') {
            $product_category_valid = 0;
            $attribute_valid        = 0;
            $attribute_value_valid  = 0;
            $price_valid            = 0;
            $attribute_value_name   = array();
            if ($filter_category != '') {
                $product_category = get_the_terms($all_id[$i], 'product_cat');
                for ($k = 0; $k < count($product_category); $k++) {
                    for ($j = 0; $j < count($filter_category); $j++) {
                        if ((string) $filter_category[$j] == (string) $product_category[$k]->slug) {
                            $product_category_valid = 1;
                        }
                    }
                }
            } else {
                $product_category_valid = 1;
            }
            if ($filter_attribute != '') {
                $att = $temp->get_attributes();
                if ($att != null) {
                    foreach ($att as $key => $value) {
                        for ($j = 0; $j < count($filter_attribute); $j++) {
                            if (("pa_" . $filter_attribute[$j]) == (string) $value['name']) {
                                $attribute_valid = 1;
                                array_push($attribute_value_name, $value['name']);
                            }
                        }
                    }
                }
            } else {
                $attribute_valid = 1;
            }
            if ($filter_attribute_value != '') {
                $pro_term = array();
                $attribute_value = array();
                for ($l = 0; $l < count($attribute_value_name); $l++) {
                    $pro_term = get_the_terms($all_id[$i], $attribute_value_name);
                }
                foreach ($pro_term as $key => $value) {
                    array_push($attribute_value, $value->slug);
                }
                for ($k = 0; $k < count($filter_attribute_value); $k++) {
                    for ($j = 0; $j < count($attribute_value); $j++) {
                        if ((string) $filter_attribute_value[$k] == (string) $attribute_value[$j]) {
                            $attribute_value_valid = 1;
                        }
                    }
                }
            } else {
                $attribute_value_valid = 1;
            }
            if ($filter_range != 'all') {
                switch ($filter_range) {
                    case '>':
                        if ((int) $temp->get_regular_price() >= (int) $filter_desired_price) {
                            $price_valid = 1;
                        }
                        break;
                    case '<':
                        if ((int) $temp->get_regular_price() <= (int) $filter_desired_price) {
                            $price_valid = 1;
                        }
                        break;
                    case '=':
                        if ((int) $temp->get_regular_price() == (int) $filter_desired_price) {
                            $price_valid = 1;
                        }
                        break;
                    case '|':
                        if ((int) $temp->get_regular_price() >= (int) $filter_minimum_price && (int) $temp->get_regular_price() <= (int) $filter_maximum_price) {
                            $price_valid = 1;
                        }
                        break;
                }
            } else {
                $price_valid = 1;
            }
            if ($product_category_valid != 0 && $attribute_valid != 0 && $attribute_value_valid != 0 && $price_valid != 0) {
                array_push($filtered_id, $temp_id);
            }
        }
    }
    session_start();
    $_SESSION['product_ids']           = $filtered_id;
    $obj_fil                           = new Eh_DataTables();
    $obj_fil->input();
    $obj_fil->ajax_response('1');
}