<?php

if (!defined('ABSPATH')){
    // no direct access
    exit;
}

/**
 * Get the customer for the given email
 *
 * @param string $email the customer email
 * @param string $fields
 * @return array
 *
 * copied from: https://github.com/woothemes/woocommerce/commit/4fe3dd28fb3751aab
 */
function get_customer_by_email( $email, $fields = null ) {
    $instance = new WC_API_Customers(WC()->api->server);

    if ( is_email( $email ) ) {
        $customer = get_user_by( 'email', $email );
        if ( ! is_object( $customer ) ) {
            return new WP_Error( 'woocommerce_api_invalid_customer_email', __( 'Invalid customer Email', 'woocommerce' ), array( 'status' => 404 ) );
        }
    } else {
        return new WP_Error( 'woocommerce_api_invalid_customer_email', __( 'Invalid customer Email', 'woocommerce' ), array( 'status' => 404 ) );
    }

    return $instance->get_customer( $customer->ID, $fields );
}

?>