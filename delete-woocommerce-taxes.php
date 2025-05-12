<?php
/**
 * Plugin Name: WooCommerce Bulk Tax Remover
 * Plugin URI:  https://yourwebsite.com/
 * Description: A plugin to bulk delete all WooCommerce tax rates safely.
 * Version:     1.1
 * Author:      Sheikh Abdul Fahad
 * Author URI:  https://yourwebsite.com/
 * License:     GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ensure WooCommerce is active
function wbt_check_woocommerce_active() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p><strong>WooCommerce Bulk Tax Remover</strong> requires WooCommerce to be installed and active.</p></div>';
        });
        return false;
    }
    return true;
}

// Add admin menu
function wbt_add_admin_menu() {
    if (!wbt_check_woocommerce_active()) return;
    add_submenu_page(
        'woocommerce',
        'Delete WooCommerce Taxes',
        'Delete WooCommerce Taxes',
        'manage_options',
        'wbt-delete-taxes',
        'wbt_delete_taxes_page'
    );
}
add_action('admin_menu', 'wbt_add_admin_menu');

// Plugin settings page
function wbt_delete_taxes_page() {
    ?>
    <div class="wrap">
        <h1>Delete WooCommerce Taxes</h1>
        <p>Click the button below to permanently delete all tax rates in WooCommerce.</p>
        <form method="post">
            <?php wp_nonce_field('wbt_delete_taxes', 'wbt_nonce'); ?>
            <input type="submit" name="wbt_delete_taxes" class="button button-danger" value="Delete All Taxes" onclick="return confirm('Are you sure? This action cannot be undone.');">
        </form>
    </div>
    <?php

    if (isset($_POST['wbt_delete_taxes']) && check_admin_referer('wbt_delete_taxes', 'wbt_nonce')) {
        wbt_delete_all_taxes();
    }
}

// Function to delete all WooCommerce tax rates
function wbt_delete_all_taxes() {
    global $wpdb;

    // Get table names
    $tax_rates_table = $wpdb->prefix . 'woocommerce_tax_rates';
    $tax_rate_locations_table = $wpdb->prefix . 'woocommerce_tax_rate_locations';

    // Check if tables exist before deleting
    if ($wpdb->get_var("SHOW TABLES LIKE '$tax_rates_table'") == $tax_rates_table) {
        $wpdb->query("DELETE FROM $tax_rates_table");
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$tax_rate_locations_table'") == $tax_rate_locations_table) {
        $wpdb->query("DELETE FROM $tax_rate_locations_table");
    }

    echo '<div class="updated"><p><strong>All WooCommerce tax rates have been deleted successfully.</strong></p></div>';
}
