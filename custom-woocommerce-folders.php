<?php
/**
 * Plugin Name: Custom WooCommerce Folders
 * Description: Create custom folders in the WordPress uploads directory for WooCommerce products.
 * Version: 1.0
 * Author: Rahees Ahmed
 */

// Include the admin page
include_once(plugin_dir_path(__FILE__) . 'admin-page.php');

// Create a folder in the uploads directory when a new product is published
add_action('transition_post_status', 'create_product_folder', 10, 3);
function create_product_folder($new_status, $old_status, $post) {
    if ('publish' === $new_status && 'publish' !== $old_status && 'product' === $post->post_type) {
        $upload_dir = wp_upload_dir();
        $folder_name = get_option('custom_woocommerce_folder', 'products');
        $product_folder = $upload_dir['basedir'] . '/' . $folder_name . '/' . $post->ID;

        if (!file_exists($product_folder)) {
            wp_mkdir_p($product_folder);
        }
    }
}

// Move uploaded product images to the custom folder
add_filter('wp_handle_upload_prefilter', 'move_product_images_to_folder');
function move_product_images_to_folder($file) {
    add_filter('wp_handle_upload', function($data) use ($file) {
        global $post;

        if ($post && 'product' === get_post_type($post->ID)) {
            $upload_dir = wp_upload_dir();
            $folder_name = get_option('custom_woocommerce_folder', 'products');
            $product_folder = $upload_dir['basedir'] . '/' . $folder_name . '/' . $post->ID;

            $filename = basename($data['file']);
            $new_file_path = $product_folder . '/' . $filename;

            if (rename($data['file'], $new_file_path)) {
                $data['file'] = $new_file_path;
                $data['url'] = $upload_dir['baseurl'] . '/' . $folder_name . '/' . $post->ID . '/' . $filename;
            }
        }

        return $data;
    });

    return $file;
}
