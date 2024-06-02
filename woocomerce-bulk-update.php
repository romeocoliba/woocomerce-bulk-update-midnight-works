<?php
/*
Plugin Name: WooCommerce Bulk Update
Description: Bulk update WooCommerce products with a custom field.
Version: 1.5
Author: Romeo Coliba
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'bulk_update_activate');
register_deactivation_hook(__FILE__, 'bulk_update_deactivate');

function bulk_update_activate() {
    // Activation code here...
}

function bulk_update_deactivate() {
    // Deactivation code here...
}

// Add custom field to product general tab
add_action('woocommerce_product_options_general_product_data', 'add_custom_field');
function add_custom_field() {
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_promo_tag',
            'label' => __('Promotional Tag', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Enter the promotional tag for this product.', 'woocommerce')
        )
    );
}

// Save custom field
add_action('woocommerce_process_product_meta', 'save_custom_field');
function save_custom_field($post_id) {
    $custom_field_value = sanitize_text_field($_POST['_custom_promo_tag']);
    update_post_meta($post_id, '_custom_promo_tag', $custom_field_value);
}

// Add admin menu for bulk update
add_action('admin_menu', 'bulk_update_admin_menu');
function bulk_update_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Bulk Update Products',
        'Bulk Update',
        'manage_woocommerce',
        'bulk-update',
        'bulk_update_page'
    );
}

function bulk_update_page() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_admin_referer('bulk_update_nonce')) {
        $selected_products = isset($_POST['selected_products']) ? array_map('intval', $_POST['selected_products']) : array();
        $new_value = sanitize_text_field($_POST['new_value']);

        foreach ($selected_products as $product_id) {
            update_post_meta($product_id, '_custom_promo_tag', $new_value);
        }

        // Redirect to avoid form resubmission
        wp_redirect(admin_url('admin.php?page=bulk-update&updated=true'));
        exit;
    }

    // Handle filters
    $category_filter = isset($_GET['product_category']) ? sanitize_text_field($_GET['product_category']) : '';
    $price_filter = isset($_GET['product_price']) ? floatval($_GET['product_price']) : '';
    $stock_filter = isset($_GET['stock_status']) ? sanitize_text_field($_GET['stock_status']) : '';

    // Fetch products for display
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(),
        'tax_query' => array()
    );

    if (!empty($category_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $category_filter
        );
    }

    if (!empty($price_filter)) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $price_filter,
            'compare' => '<='
        );
    }

    if (!empty($stock_filter)) {
        $args['meta_query'][] = array(
            'key' => '_stock_status',
            'value' => $stock_filter
        );
    }

    $products = get_posts($args);

    // Get categories for the filter dropdown
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    ?>
    <div class="wrap">
        <h1>Bulk Update Products</h1>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true') : ?>
            <div id="message" class="updated notice is-dismissible">
                <p>Products updated successfully.</p>
            </div>
        <?php endif; ?>
        <form method="get" action="">
            <input type="hidden" name="page" value="bulk-update">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="product_category">Category</label></th>
                        <td>
                            <select name="product_category" id="product_category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($category_filter, $category->slug); ?>>
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="product_price">Maximum Price</label></th>
                        <td>
                            <input type="number" step="0.01" name="product_price" id="product_price" value="<?php echo esc_attr($price_filter); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="stock_status">Stock Status</label></th>
                        <td>
                            <select name="stock_status" id="stock_status">
                                <option value="">All Statuses</option>
                                <option value="instock" <?php selected($stock_filter, 'instock'); ?>>In Stock</option>
                                <option value="outofstock" <?php selected($stock_filter, 'outofstock'); ?>>Out of Stock</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button('Filter'); ?>
        </form>
        <form method="post" action="">
            <?php wp_nonce_field('bulk_update_nonce'); ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
                        <th scope="col" class="manage-column column-title">Product</th>
                        <th scope="col" class="manage-column column-title">Category</th>
                        <th scope="col" class="manage-column column-title">Price</th>
                        <th scope="col" class="manage-column column-title">Stock Status</th>
                        <th scope="col" class="manage-column column-title">Current Promotional Tag</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products) : ?>
                        <?php foreach ($products as $product) : ?>
                            <?php
                            $product_id = $product->ID;
                            $current_tag = get_post_meta($product_id, '_custom_promo_tag', true);
                            $product_obj = wc_get_product($product_id);
                            $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
                            $price = $product_obj->get_price();
                            $stock_status = $product_obj->get_stock_status();
                            ?>
                            <tr>
                                <th scope="row" class="check-column"><input type="checkbox" name="selected_products[]" value="<?php echo esc_attr($product_id); ?>" /></th>
                                <td><?php echo esc_html($product->post_title); ?></td>
                                <td><?php echo esc_html(implode(', ', $categories)); ?></td>
                                <td><?php echo esc_html($price); ?></td>
                                <td><?php echo esc_html($stock_status); ?></td>
                                <td><?php echo esc_html($current_tag); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p>
                <label for="new_value">New Promotional Tag:</label>
                <input type="text" id="new_value" name="new_value" required>
            </p>
            <?php submit_button('Update Selected'); ?>
        </form>
    </div>
    <?php
}
