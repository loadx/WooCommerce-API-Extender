<?php

/**
 * Plugin Name: WooCommerce API Extender
 * Description: This is assist in building out aadditional functionality of the WooCommerce API
 * Version: 1.0
 * Author: Extension Works
 * Author URI: http://www.extensionworks.com
 * License: MIT
 */
require_once(ABSPATH.'wp-content/plugins/woocommerce/includes/api/class-wc-api-server.php');
require_once('extensionworks-wc-api-server.php');

if ( ! defined( 'ABSPATH' ) ) {
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
