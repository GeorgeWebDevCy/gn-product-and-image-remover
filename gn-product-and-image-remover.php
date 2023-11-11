<?php
/**
 * GN Product and Image Remover1
 *
 * @package      GNCYPRODUCTREMOVER
 * @author        George Nicolaou
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   GN Product and Image Remover
 * Plugin URI:    https://www.georgenicolaou.me/plugins/gn-product-and-image-remover
 * Description:   Allows you to delete Woocommerce images after deleting product unless the image is used in another product
 * Version:       1.0.0
 * Author:        George Nicolaou
 * Author URI:    https://www.georgenicolaou.me/
 * Text Domain:   gn-product-and-image-remover
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH'))
	exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function GNCYPRODUCTA() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define('GNCYPRODUCTREMOVER_NAME', 'GN Product and Image Remover');

// Plugin version
define('GNCYPRODUCTREMOVER_VERSION', '1.0.0');

// Plugin Root File
define('GNCYPRODUCTREMOVER_PLUGIN_FILE', __FILE__);

// Plugin base
define('GNCYPRODUCTREMOVER_PLUGIN_BASE', plugin_basename(GNCYPRODUCTREMOVER_PLUGIN_FILE));

// Plugin Folder Path
define('GNCYPRODUCTREMOVER_PLUGIN_DIR', plugin_dir_path(GNCYPRODUCTREMOVER_PLUGIN_FILE));

// Plugin Folder URL
define('GNCYPRODUCTREMOVER_PLUGIN_URL', plugin_dir_url(GNCYPRODUCTREMOVER_PLUGIN_FILE));

/**
 * Load the main class for the core functionality
 */
require_once GNCYPRODUCTREMOVER_PLUGIN_DIR . 'core/class-gn-product-and-image-remover.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  George Nicolaou
 * @since   1.0.0
 * @return  object|Gn_Product_And_Image_Remover
 */

// Check if WooCommerce is active
function gncy_product_image_remover_check_for_woocommerce() {
    if (!class_exists('woocommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Sorry, but this plugin requires WooCommerce to be installed and active. Please install WooCommerce and try again.');
    }
}
register_activation_hook(__FILE__, 'gncy_product_image_remover_check_for_woocommerce');

// Load the main class for the core functionality
require_once plugin_dir_path(__FILE__) . 'core/class-gn-product-and-image-remover.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @return object|Gn_Product_And_Image_Remover
 */
function GNCYPRODUCTREMOVER() {
    return Gn_Product_And_Image_Remover::instance();
}

add_action('before_delete_post', 'gncy_product_remover_delete_product_images', 10, 1);

function gncy_product_remover_delete_product_images($post_id) {
    if ('product' !== get_post_type($post_id)) {
        return; // Only process WooCommerce products
    }

    $product = wc_get_product($post_id);

    if (!$product) {
        return;
    }

    $featured_image_id = $product->get_image_id();
    $image_galleries_id = $product->get_gallery_image_ids();

    if (!empty($featured_image_id)) {
        $is_featured_image_used = gncy_product_remover_is_image_used($featured_image_id, $post_id);
        if (!$is_featured_image_used) {
            wp_delete_attachment($featured_image_id, true);
        }
    }

    if (!empty($image_galleries_id)) {
        foreach ($image_galleries_id as $single_image_id) {
            $is_image_used = gncy_product_remover_is_image_used($single_image_id, $post_id);
            if (!$is_image_used) {
                wp_delete_attachment($single_image_id, true);
            }
        }
    }
}

function gncy_product_remover_is_image_used($image_id, $current_product_id) {
    $query = new WP_Query(
        array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_thumbnail_id',
                    'value' => $image_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_product_image_gallery',
                    'value' => $image_id,
                    'compare' => 'LIKE'
                )
            ),
            'post__not_in' => array($current_product_id),
            'fields' => 'ids',
            'posts_per_page' => -1
        )
    );

    return ($query->have_posts());
}

GNCYPRODUCTREMOVER();