<?php
if (!defined('ABSPATH')) {
    exit;
}?>
<div class='wrap table-box table-box-main' id='wrap_table' style="position:relative;display: none;">
<?php
eh_bep_list_table();
?>
</div>
<?php
eh_bep_process_edit();
function eh_bep_list_table()
{
?>
<?php
    session_start();
    $_SESSION['product_ids']=eh_bep_get_first_products();
    $obj = new Eh_DataTables();
    $obj->input();
    $obj->prepare_items();
    $obj->search_box('search', 'search_id');
?>
    <button id='process_edit' value='edit_products' style="background-color: green;color: white;" class='button button-large'><span class="update-text"><?php _e('Process Edit', 'eh_bulk_edit'); ?></span><span class="edit"></span></button>
    <label>Table Row</label>
    <input id='display_count_order' style="width:132px" type='number' value="<?php $count=get_option('eh_bulk_edit_table_row');if($count){echo $count;}?>" placeholder="Number of Rows">
    <button id='save_dislay_count_order'class='button button-primary'><?php _e('Save', 'eh-stripe-gateway'); ?></button>

    <form id="movies-filter" method="get">
            <input type="hidden" name="action" value="all" />
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <?php $obj->display(); ?>   
    </form>
    <?php
}
function eh_bep_process_edit()
{
    global $woocommerce;
    ?>
    <div class='wrap postbox table-box table-box-main' id="edit_product" style='padding:0px 20px;display: none'>
        <h2>
            <?php _e('Update the Products', 'eh_bulk_edit'); ?>
        </h2>
        <hr>
        <table class='eh-edit-table' id='update_general_table'>
            <tr>
                <td class='eh-edit-tab-table-left'>
                                <?php _e('Title', 'eh_bulk_edit');?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Set new or update title', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='title_action'>
                    <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                    <option value='set_new'><?php _e('Set New', 'eh_bulk_edit'); ?></option>
                    <option value='append'><?php _e('Append', 'eh_bulk_edit'); ?></option>
                    <option value='prepand'><?php _e('Prepend', 'eh_bulk_edit'); ?></option>
                    <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='title_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('SKU', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Set new or update SKU. For variable products check desired option for parent or variations', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='sku_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='set_new'><?php _e('Set New', 'eh_bulk_edit'); ?></option>
                        <option value='append'><?php _e('Append', 'eh_bulk_edit'); ?></option>
                        <option value='prepand'><?php _e('Preped', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='sku_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Product Visiblity', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select product visibilty on Catalog/Search page', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='catalog_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='visible'><?php _e('Catalog/Search', 'eh_bulk_edit'); ?></option>
                        <option value='catalog'><?php _e('Catalog', 'eh_bulk_edit'); ?></option>
                        <option value='search'><?php _e('Search', 'eh_bulk_edit'); ?></option>
                        <option value='hidden'><?php _e('Hidden', 'eh_bulk_edit'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                                <?php _e('Shipping Class', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Set shipping class for filterd products. If the shipping class exists it will be replaced', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='shipping_class_action'>
                        <?php
                            $ship = $woocommerce->shipping->get_shipping_classes();
                            if(count($ship)>0)
                            {
                                ?>
                                <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                                <?php
                                foreach ($ship as $key => $value)
                                {
                                    echo "<option value='" . $value->term_id . "'>" . $value->name . "</option>";
                                }
                            }
                            else
                            {
                                ?>
                                <option value=''><?php _e('< No Shipping Class >', 'eh_bulk_edit'); ?></option>
                                <?php
                            }
                            
                        ?>
                    </select>
                    <span id='shipping_class_check_text'></span>
                </td>
            </tr>
        </table>
        <h2>
            <?php _e('Price', 'eh_bulk_edit'); ?>
        </h2>
        <hr>
        <table class='eh-edit-table' id="update_price_table">
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Sale Price', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select the desired option to adjust sale price', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='sale_price_action'>
                    <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                    <option value='up_percentage'><?php _e('Increase by Percentage ( + %)', 'eh_bulk_edit'); ?></option>
                    <option value='down_percentage'><?php _e('Decrease by Percentage ( - %)', 'eh_bulk_edit'); ?></option>
                    <option value='up_price'><?php _e('Increase by Price ( + $)', 'eh_bulk_edit'); ?></option>
                    <option value='down_price'><?php _e('Decrease by Price ( - $)', 'eh_bulk_edit');?></option>
                    <option value='flat_all'><?php _e('Flat Price to All', 'eh_bulk_edit');?></option>
                    </select>
                    <span id='sale_price_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                                <?php _e('Regular Price', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select the desired option to adjust Regular price', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='regular_price_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='up_percentage'><?php _e('Increase by Percentage ( + %)', 'eh_bulk_edit'); ?></option>
                        <option value='down_percentage'><?php _e('Decrease by Percentage ( - %)', 'eh_bulk_edit'); ?></option>
                        <option value='up_price'><?php _e('Increase by Price ( + $)', 'eh_bulk_edit'); ?></option>
                        <option value='down_price'><?php _e('Decrease by Price ( - $)', 'eh_bulk_edit'); ?></option>
                        <option value='flat_all'><?php _e('Flat Price to All', 'eh_bulk_edit');?></option>
                    </select>
                    <span id='regular_price_text'></span>
                </td>
            </tr>
        </table>
        <h2>
            <?php _e('Stock', 'eh_bulk_edit'); ?>
        </h2>
        <hr>
        <table class='eh-edit-table' id='update_stock_table'>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Manage Stock ?', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Enable or Disable manage stock for products or variations', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='manage_stock_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='yes'><?php _e('Enable', 'eh_bulk_edit'); ?></option>
                        <option value='no'><?php _e('Disable', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='manage_stock_check_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Stock Quantity', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update stock quantity', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='stock_quantity_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='add'><?php _e('Add', 'eh_bulk_edit'); ?></option>
                        <option value='sub'><?php _e('Subtract', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='stock_quantity_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Allow Backorders ?', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select the required backorder option', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='allow_backorder_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='no'><?php _e('Do not allow', 'eh_bulk_edit'); ?></option>
                        <option value='notify'><?php _e('Allow, but notify customer', 'eh_bulk_edit'); ?></option>
                        <option value='yes'><?php _e('Allow', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='backorder_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Stock Status', 'eh_bulk_edit'); ?>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update the stock status', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='stock_status_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='instock'><?php _e('In Stock', 'eh_bulk_edit'); ?></option>
                        <option value='outofstock'><?php _e('Out of Stock', 'eh_bulk_edit'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <h2>
            <?php _e('Properties', 'eh_bulk_edit'); ?>
        </h2>
        <hr>
        <table class='eh-edit-table' id='update_properties_table'>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Length', 'eh_bulk_edit'); ?>
                    <span style="float:right;"><?php echo strtolower(get_option('woocommerce_dimension_unit')); ?></span>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update the length', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='length_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='add'><?php _e('Add', 'eh_bulk_edit'); ?></option>
                        <option value='sub'><?php _e('Subtract', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='length_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Width', 'eh_bulk_edit'); ?>
                    <span style="float:right;"><?php echo strtolower(get_option('woocommerce_dimension_unit')); ?></span>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update the width', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='width_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='add'><?php _e('Add', 'eh_bulk_edit'); ?></option>
                        <option value='sub'><?php _e('Subtract', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='width_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Height', 'eh_bulk_edit'); ?>
                    <span style="float:right;"><?php echo strtolower(get_option('woocommerce_dimension_unit')); ?></span>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update the height', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='height_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='add'><?php _e('Add', 'eh_bulk_edit'); ?></option>
                        <option value='sub'><?php _e('Subtract', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='height_text'></span>
                </td>
            </tr>
            <tr>
                <td class='eh-edit-tab-table-left'>
                    <?php _e('Weight', 'eh_bulk_edit'); ?>
                    <span style="float:right;"><?php echo strtolower(get_option('woocommerce_weight_unit')); ?></span>
                </td>
                <td class='eh-edit-tab-table-middle'>
                    <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Update the weight', 'eh_bulk_edit'); ?>'></span>
                </td>
                <td class='eh-edit-tab-table-input-td'>
                    <select id='weight_action'>
                        <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                        <option value='add'><?php _e('Add', 'eh_bulk_edit'); ?></option>
                        <option value='sub'><?php _e('Subtract', 'eh_bulk_edit'); ?></option>
                        <option value='replace'><?php _e('Replace', 'eh_bulk_edit'); ?></option>
                    </select>
                    <span id='weight_text'></span>
                </td>
            </tr>
        </table>
        <?php
            if (in_array('pricing-discounts-by-user-role-woocommerce/pricing-discounts-by-user-role-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
                {
            ?>
            <h2>
                <?php _e('Role Based Pricing', 'eh_bulk_edit'); ?>
            </h2>
            <hr>
            <table class='eh-edit-table' id='update_general_table'>
                <tr>
                    <td class='eh-edit-tab-table-left'>
                        <?php _e('Hide price', 'eh_bulk_edit'); ?>
                    </td>
                    <td class='eh-edit-tab-table-middle'>
                        <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select option to hide price for unregistered users.', 'eh_bulk_edit'); ?>'></span>
                    </td>
                    <td class='eh-edit-tab-table-input-td'>
                        <select id='visibility_price'>
                            <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                            <option value='no'><?php _e('Show Price', 'eh_bulk_edit'); ?></option>
                            <option value='yes'><?php _e('Hide Price', 'eh_bulk_edit'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='eh-edit-tab-table-left'>
                        <?php _e('Hide product price based on user role', 'eh_bulk_edit'); ?>
                    </td>
                    <td class='eh-edit-tab-table-middle'>
                        <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('For selected user role, hide the product price', 'eh_bulk_edit'); ?>'></span>
                    </td>
                    <td class='eh-edit-tab-table-input-td'>
                        <span class='select-eh'>
                            <select data-placeholder='<?php _e('User Role', 'eh_bulk_edit'); ?>' id='hide_price_role_select' multiple class='hide-price-role-select-chosen' >
                                <?php
                                    global $wp_roles;
                                    $roles = $wp_roles->role_names;
                                    foreach ($roles as $key => $value)
                                    {
                                        echo "<option value='" . $key . "'>" . $value . "</option>";
                                    }
                                ?>
                            </select>
                        </span>
                    </td>
                </tr>
                <?php
                    $enabled_roles = get_option('eh_pricing_discount_product_price_user_role');
                    if (is_array($enabled_roles)) 
                    {
                        if (!in_array('none', $enabled_roles)) 
                        {
                ?>
                <tr>
                    <td class='eh-edit-tab-table-left'>
                        <?php _e('Enforce product price adjustment', 'eh_bulk_edit'); ?>
                    </td>
                    <td class='eh-edit-tab-table-middle'>
                        <span class='woocommerce-help-tip tooltip' data-tooltip='<?php _e('Select option to enforce indvidual price adjustment', 'eh_bulk_edit'); ?>'></span>
                    </td>
                    <td class='eh-edit-tab-table-input-td'>
                        <select id='price_adjustment_action'>
                            <option value=''><?php _e('< No Change >', 'eh_bulk_edit'); ?></option>
                            <option value='yes'><?php _e('Enable', 'eh_bulk_edit'); ?></option>
                            <option value='no'><?php _e('Disable', 'eh_bulk_edit'); ?></option>
                        </select>
                    </td>
                </tr>
                <?php
                        }
                    }
                ?>
            </table>
            <?php
                }
            ?>
    <button id='update_button' value='update_button' style="margin-bottom: 2%;background-color: green;color: white;" class='button button-large'><span class="update-text"><?php _e('Update Product', 'eh_bulk_edit'); ?></span><span class="update"></span></button>
    <button id='cancel_update_button' value='cancel_update_button' style="margin-bottom: 2%;" class='button button-primary button-large'><span class="update-text"><?php _e('Cancel Update', 'eh_bulk_edit'); ?></span></button>
    <button id='reset_update_button' value='reset_update_button' style="margin-bottom: 2%;" class='button button-primary button-large'><span class="update-text"><?php _e('Reset Update', 'eh_bulk_edit'); ?></span></button>
    </div>    
    <?php
}