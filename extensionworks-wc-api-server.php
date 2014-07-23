<?php

require_once(ABSPATH.'wp-content/plugins/woocommerce/includes/api/class-wc-api-server.php');
require_once(ABSPATH.'wp-content/plugins/woocommerce/includes/api/class-wc-api-resource.php');
require_once('extensionworks-wc-api-customers-functions.php');

if (!defined('ABSPATH')){
    // no direct access
    exit;
}

/*
  Overrides WC_API_Server and injects our custom routes
*/
class WC_API_Server_Extensionworks extends WC_API_Server {

    protected $route_pattern;
    protected $route_handler;

    public function __construct($path, $route, $handler){
        parent::__construct($path);
        $this->route_pattern = $route;
        $this->route_handler = $handler;
    }

    public function get_routes(){
        $routes = parent::get_routes();

        // gross hack: inject this route.. only once
        if (!in_array($this->route_pattern, $routes)){
            $routes[$this->route_pattern] = $this->route_handler;
        }

        return $routes;
    }
}

?>