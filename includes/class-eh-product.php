<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 *  plugin init
 */
class Eh_Bulk_Edit_Init extends Eh_Bulk_Edit_Main
{

    function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'eh_bep_menu_add'
        ));
        add_action('admin_init', array(
            $this,
            'eh_bep_register_plugin_styles_scripts'
        ));
    }
    /**
     * Sub menu add in woocommerce menu
     */
    public function eh_bep_menu_add()
    {

        add_submenu_page('woocommerce', 'Bulk Edit Products', 'Bulk Edit Products', 'manage_woocommerce', 'eh-bulk-edit-product-attr', array(
            $this,
            'eh_bep_template_display'
        ));
    }
    /**
     * Register and enqueue style sheet.
     */
    public function eh_bep_register_plugin_styles_scripts()
    {
        include_once "ajax-apifunctions.php";
        include_once "class-table-data.php";
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ('eh-bulk-edit-product-attr' != $page)
            return;
        wp_nonce_field('ajax-eh-bep-nonce', '_ajax_eh_bep_nonce');
        global $woocommerce;
        $woocommerce_version = function_exists('WC') ? WC()->version : $woocommerce->version;
        wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version);
        wp_register_style('eh-plugin-style', plugins_url('/assets/css/bootstrap.css', dirname(__FILE__)));
        wp_enqueue_style('eh-plugin-style');
        wp_register_style('eh-alert-style', plugins_url('/assets/css/sweetalert2.css', dirname(__FILE__)));
        wp_enqueue_style('eh-alert-style');
        wp_register_script('eh-alert-jquery', plugins_url('/assets/js/sweetalert2.min.js', dirname(__FILE__)));
        wp_enqueue_script('eh-alert-jquery');
        wp_register_script('eh-multibox-jquery', plugins_url('/assets/js/chosen.jquery.js', dirname(__FILE__)));
        wp_enqueue_script('eh-multibox-jquery');
        wp_register_script('eh-tooltip-jquery', plugins_url('/assets/js/tooltip.js', dirname(__FILE__)));
        wp_enqueue_script('eh-tooltip-jquery');
        wp_register_script('eh-custom', plugins_url('/assets/js/eh-custom.js', dirname(__FILE__)));
        $js_var = array(
            'filter_attribute_value_title' => __('Product Attributes Values', 'eh_bulk_edit'),
            'filter_attribute_value_tooltip' => __('Select the attributes in which filters has to be applied', 'eh_bulk_edit'),
            'filter_attribute_value_placeholder' => __('Select Attibutes Values', 'eh_bulk_edit'),
            'filter_price_range_desired_placeholder' => __('Desired Price', 'eh_bulk_edit'),
            'filter_price_range_min_placeholder' => __('Minimum Price', 'eh_bulk_edit'),
            'filter_price_range_max_placeholder' => __('Maximum Price', 'eh_bulk_edit'),
            'process_edit_alert_title' => __('Do you want to Proceed?', 'eh_bulk_edit'),
            'process_edit_alert_confirm_button' => __('Yes, Go on!', 'eh_bulk_edit'),
            'process_edit_alert_cancel_button' => __('No, Wait!', 'eh_bulk_edit'),
            'process_update_alert_title' => __('Proceed with Update?', 'eh_bulk_edit'),
            'process_update_alert_confirm_button' => __('Yes, Update!', 'eh_bulk_edit'),
            'process_update_alert_cancel_button' => __('No, Wait!', 'eh_bulk_edit'),
            'clear_product_alert_title' => __('Are you Sure ?', 'eh_bulk_edit'),
            'clear_product_alert_subtitle' => __('Did you want to Reset?', 'eh_bulk_edit'),
            'clear_product_alert_confirm_button' => __('Yes, Reset!', 'eh_bulk_edit'),
            'clear_product_alert_cancel_button' => __('No, Wait!', 'eh_bulk_edit'),
            'edit_title_new_placeholder' => __('New Title', 'eh_bulk_edit'),
            'edit_title_append_placeholder' => __('Append Title', 'eh_bulk_edit'),
            'edit_title_prepand_placeholder' => __('Prepand Title', 'eh_bulk_edit'),
            'edit_title_replaceable_placeholder' => __('Word to be replaced', 'eh_bulk_edit'),
            'edit_title_replace_placeholder' => __('Replace word', 'eh_bulk_edit'),
            'edit_sku_new_placeholder' => __('New SKU', 'eh_bulk_edit'),
            'edit_sku_append_placeholder' => __('Append SKU', 'eh_bulk_edit'),
            'edit_sku_prepand_placeholder' => __('Prepand SKU', 'eh_bulk_edit'),
            'edit_sku_replaceable_placeholder' => __('Word to be replaced', 'eh_bulk_edit'),
            'edit_sku_replace_placeholder' => __('Replace word', 'eh_bulk_edit'),
            'edit_price_up_per_placeholder' => __('Increase Percentage', 'eh_bulk_edit'),
            'edit_price_down_per_placeholder' => __('Decrease Percentage', 'eh_bulk_edit'),
            'edit_price_up_pri_placeholder' => __('Increase Price', 'eh_bulk_edit'),
            'edit_price_down_pri_placeholder' => __('Decrease Price', 'eh_bulk_edit'),
            'edit_price_flat_pri_placeholder' => __('Set Flat Price to All','eh_bulk_edit'),
            'edit_quantity_add_placeholder' => __('Quantity Will be added', 'eh_bulk_edit'),
            'edit_quantity_sub_placeholder' => __('Quantity Will be Subtracted', 'eh_bulk_edit'),
            'edit_quantity_rep_placeholder' => __('Quantity Will be Replaced', 'eh_bulk_edit'),
            'edit_length_add_placeholder' => __('Length Will be added', 'eh_bulk_edit'),
            'edit_length_sub_placeholder' => __('Length Will be Subtracted', 'eh_bulk_edit'),
            'edit_length_rep_placeholder' => __('Length Will be Replaced', 'eh_bulk_edit'),
            'edit_width_add_placeholder' => __('Width Will be added', 'eh_bulk_edit'),
            'edit_width_sub_placeholder' => __('Width Will be Subtracted', 'eh_bulk_edit'),
            'edit_width_rep_placeholder' => __('Width Will be Replaced', 'eh_bulk_edit'),
            'edit_height_add_placeholder' => __('Height Will be added', 'eh_bulk_edit'),
            'edit_height_sub_placeholder' => __('Height Will be Subtracted', 'eh_bulk_edit'),
            'edit_height_rep_placeholder' => __('Height Will be Replaced', 'eh_bulk_edit'),
            'edit_weight_add_placeholder' => __('Weight Will be added', 'eh_bulk_edit'),
            'edit_weight_sub_placeholder' => __('Weight Will be Subtracted', 'eh_bulk_edit'),
            'edit_weight_rep_placeholder' => __('Weight Will be Replaced', 'eh_bulk_edit'),
            'edit_success_alert_title' => __('Update successful', 'eh_bulk_edit'),
            'edit_success_alert_button' => __('OK', 'eh_bulk_edit')
        );
        wp_localize_script('eh-custom', 'js_obj', $js_var);
        wp_enqueue_script('eh-custom');
    }
    public function eh_bep_template_display()
    {
        include_once EH_BEP_TEMPLATE_PATH . "/template-frontend-filters.php";
    }
}
new Eh_Bulk_Edit_Init();
