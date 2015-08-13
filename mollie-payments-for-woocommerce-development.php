<?php
/**
 * Plugin Name: Mollie Payments for WooCommerce (development)
 * Plugin URI: https://www.mollie.com/
 * Description: Development plugin for Mollie Payments for WooCommerce
 * Version: 1.0
 * Author: Mollie
 * Author URI: https://www.mollie.com
 * Requires at least: 3.8
 * Tested up to: 4.2.4
 * License: GPLv2 or later
 */

add_action('init', function () {
    // Register Mollie autoloader
    require_once dirname(dirname(__FILE__)) . '/mollie-payments-for-woocommerce/includes/mollie/wc/autoload.php';

    Mollie_WC_Autoload::register();

    // Overwrite payment webhook
    /*add_filter('mollie-payments-for-woocommerce_webhook_url', function($webhook_url) {
        // Overwrite plugin webhook URL (I use ngrok.io)
        $new_webhook_url = str_replace($_SERVER['HTTP_HOST'], '63950d2f.ngrok.io', $webhook_url);

        Mollie_WC_Plugin::debug("Overwrite webhook url: $webhook_url => $new_webhook_url");

        return $new_webhook_url;
    });*/

    // Overwrite Mollie API endpoint for local Mollie installation (Mollie engineers only)
    /*add_filter('mollie-payments-for-woocommerce_api_endpoint', function($api_endpoint) {
        return 'http://api.mollie.dev';
    });*/

    // Used payment parameters
    add_action('mollie-payments-for-woocommerce_create_payment', function($data, $order) {
        Mollie_WC_Plugin::debug("Order {$order->id} create payment parameters: " . print_r($data, TRUE));
    }, $priority = 10, $accepted_args = 2);

    // Mollie Payment created
    add_action('mollie-payments-for-woocommerce_payment_created', function($payment, $order) {
        Mollie_WC_Plugin::debug("Order {$order->id} payment created: " . print_r($payment, TRUE));
    }, $priority = 10, $accepted_args = 2);
});

register_activation_hook(__FILE__, function() {
    if (!is_plugin_active('mollie-payments-for-woocommerce/mollie-payments-for-woocommerce.php'))
    {
        $error = 'Could not enable Mollie Payments for WooCommerce (Development), enable Mollie Payments for WooCommerce plugin first.';
        $title = 'Mollie Payments for WooCommerce plugin not active';

        wp_die($error, $title, array('back_link' => true));
    }
});

add_action('admin_init', function() {
    // Mollie Payments for WooCommerce plugin not activated
    if (!is_plugin_active('mollie-payments-for-woocommerce/mollie-payments-for-woocommerce.php'))
    {
        // Deactivate myself
        deactivate_plugins(plugin_basename(__FILE__));

        add_action('admin_notices', function() {
            echo '<div class="error"><p>Mollie Payments for WooCommerce (development) deactivated because it depends on Mollie Payments for WooCommerce.</p></div>';
        });
    }
});