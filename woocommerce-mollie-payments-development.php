<?php
/**
 * Plugin Name: WooCommerce Mollie Payments (development)
 * Plugin URI: https://www.mollie.com/
 * Description: Mollie payments for WooCommerce (development)
 * Version: 1.0
 * Author: Mollie
 * Author URI: https://www.mollie.com
 * Requires at least: 3.0
 * Tested up to: 3.0
 * License: http://www.opensource.org/licenses/bsd-license.php  Berkeley Software Distribution License (BSD-License 2)
 */

add_action('init', function () {
    // Register Mollie autoloader
    require_once dirname(dirname(__FILE__)) . '/woocommerce-mollie-payments/includes/WC/Mollie/Autoload.php';

    WC_Mollie_Autoload::register();

    // Overwrite payment webhook
    /*add_filter('woocommerce-mollie-payments_webhook_url', function($webhook_url) {
        // Overwrite plugin webhook URL (I use ngrok.io)
        $new_webhook_url = str_replace($_SERVER['HTTP_HOST'], '63950d2f.ngrok.io', $webhook_url);

        WC_Mollie::debug("Overwrite webhook url: $webhook_url => $new_webhook_url");

        return $new_webhook_url;
    });*/

    // Overwrite Mollie API endpoint for local Mollie installation (Mollie engineers only)
    /*add_filter('woocommerce-mollie-payments_api_endpoint', function($api_endpoint) {
        return 'http://api.mollie.dev';
    });*/

    // Used payment parameters
    add_action('woocommerce-mollie-payments_create_payment', function($data, $order) {
        WC_Mollie::debug("Order {$order->id} create payment parameters: " . print_r($data, TRUE));
    }, $priority = 10, $accepted_args = 2);

    // Mollie Payment created
    add_action('woocommerce-mollie-payments_payment_created', function($payment, $order) {
        WC_Mollie::debug("Order {$order->id} payment created: " . print_r($payment, TRUE));
    }, $priority = 10, $accepted_args = 2);
});

register_activation_hook(__FILE__, function () {
    if (!is_plugin_active('woocommerce-mollie-payments/woocommerce-mollie-payments.php'))
    {
        $error = 'Could not enable WooCommerce Mollie Payments (Development), enable WooCommerce Mollie Payments plugin first.';
        $title = 'WooCommerce Mollie Payments plugin not active';

        wp_die($error, $title, array('back_link' => true));
    }
});

add_action('admin_init', function() {
    // WooCommerce Mollie plugin not activated
    if (!is_plugin_active('woocommerce-mollie-payments/woocommerce-mollie-payments.php'))
    {
        // Deactivate myself
        deactivate_plugins(plugin_basename(__FILE__));

        add_action('admin_notices', function() {
            echo '<div class="error"><p>WooCommerce Mollie Payments (development) deactivated because it depends on WooCommerce Mollie Payments.</p></div>';
        });
    }
});