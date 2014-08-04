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

$woo_plugin_data = get_plugins('/'.'woocommerce');
$reject_plugin = floatval($woo_plugin_data[0]['Version']) >= 2.2 ? true : false;

$routes = array(
    '/customers/email/(?P<email>.+)' => array(
        array('get_customer_by_email', WC_API_SERVER::READABLE)
    )
);


/*
inject additional routes into woocommerce's api
*/
function inject_routes(){
    global $wp;
    global $routes;
    global $reject_plugin;

    if($reject_plugin){
        // woocommerce >= 2.2 and user has not yet removed the plugin. Disable functionality
        return;
    }

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


/*
Notifies the user when this plugin is no longer required
 */
function show_version_warning(){
    global $reject_plugin;

    // only show this to admins
    if($reject_plugin && current_user_can('manage_options')){
        echo '
        <div class="error">
          <p><a style="text-decoration: none;" target="_blank" href="https://github.com/ExtensionWorks/WooCommerce-API-Extender">WooCommerce-API-Extender</a> - You do not require this plugin to be installed as your <strong>WooCommerce version is >= 2.2</strong></p>
          <span style="color: #dd3d36;"><a style="color: #dd3d36;" href="http://www.extensionworks.com">ExtensionWorks</a> advises you to remove this plugin as it is no longer needed and we have disabled it\'s functionality to prevent possible WooCommerce API issues.</span>
        </div>';
    }
}

add_action('woocommerce_api_loaded', 'inject_routes', 0);
add_action('admin_notices', 'show_version_warning');
