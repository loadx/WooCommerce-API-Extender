<?php

/**
 * Plugin Name: WooCommerce-API-Extender
 * Plugin URI: http://www.extensionworks.com.au/
 * Description: This plugin enables WooCommerce's API to lookup customers and orders by email address. This will be a standard feature in WooCommerce 2.2 and this plugin is merely an extension to enable the required behaviour now.  This plugin will notify when it is no longer required.
 * Version: 1.0
 * Author: ExtensionWorks
 * Author URI: http://www.extensionworks.com
 * License: MIT
 */
require_once(WP_CONTENT_DIR.'/plugins/woocommerce/includes/api/class-wc-api-server.php');
require_once('extensionworks-wc-api-server.php');

if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

$routes = array(
    '/customers/email/(?P<email>.+)' => array(
        array('get_customer_by_email', WC_API_SERVER::READABLE)
    )
);

add_action('woocommerce_api_loaded', 'inject_routes', 0);

function inject_routes(){
    global $wp;
    global $routes;

    foreach($routes as $route => $handler){
        $match = preg_match( '@^' . $route . '$@i', urldecode( $wp->query_vars['wc-api-route']));
        if($match){
            WC()->api->server = new WC_API_Server_Extensionworks($wp->query_vars['wc-api-route'], $route, $handler);
            WC()->api->register_resources(WC()->api->server);
            WC()->api->server->serve_request();
            exit();

        }
    }
}
