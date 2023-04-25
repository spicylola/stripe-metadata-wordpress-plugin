<?php
/**
 * Process the metadata construction for Woo and Stripe.
 *
 * @package ShonaikeStripeConnect
 */

// Call our namepsace.
namespace LolaShonaike\ShonaikeStripeConnect\Metadata;

// Set our aliases.
use LolaShonaike\ShonaikeStripeConnect as Core;

/**
 * Start our engines.
 */
add_filter( 'wc_stripe_payment_metadata', __NAMESPACE__ . '\include_custom_stripe_metadata', 10, 3 );

/**
 * Add our custom metadata into the overall Stripe payload.
 *
 * @param  array    $metadata  The original metadata array.
 * @param  WC_Order $order     The entire order object from Woo.
 * @param  object   $source    Stripe Payment Method or Source.
 *
 * @return array
 */
function include_custom_stripe_metadata( $metadata, $order, $source ) {
    $count = 1;
    foreach( $order->get_items() as $item_id => $line_item ){
        $item_data = $line_item->get_data();
        $product = $line_item->get_product();
        $product_name = $product->get_name();
        $item_quantity = $line_item->get_quantity();
        $item_total = $line_item->get_total();
        $item_meta_data = $line_item->get_meta_data();
        // This key value pair is specific to a webhook, that i need
        $metadata[$product_name] = $item_quantity;
        $count += 1;
    }
    //$metadata[ __( 'order_object', 'woocommerce-gateway-stripe' ) ] = $order;
    $metadata["order_object" ] = json_encode($order -> get_items());

	// Here is where we add the data that we want.
	//$metadata[ __( 'A label or key', 'woocommerce-gateway-stripe' ) ] = 'some-random-value';
	//$metadata[ __( 'A different label', 'woocommerce-gateway-stripe' ) ] = 'a-different-value';

	// Return the modified array.
	return $metadata;
}
