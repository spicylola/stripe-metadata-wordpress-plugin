<?php
/**
 * Process the metadata construction for Woo and Stripe.
 *
 * @package ShonaikeStripeConnect
 */

namespace LolaShonaike\ShonaikeStripeConnect\Metadata;

use LolaShonaike\ShonaikeStripeConnect as Core;
use WC_Order;

/**
 * Build metadata using your exact field naming:
 *   $metadata[$product_name] = $item_quantity
 * Also adds a correlator: wp_order_id
 */
function soffun_build_metadata_exact_names( WC_Order $order ) : array {
	$metadata = [
		'wp_order_id' => (string) $order->get_id(),
		'source'      => 'wordpress',
	];

	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( ! $product ) {
			continue;
		}

		$product_name  = $product->get_name();           // e.g., "20 Denard"
		$item_quantity = (string) $item->get_quantity(); // Stripe values should be strings

		// Note: Stripe metadata key length limit is 40 chars.
		// If you ever introduce long names, trim here:
		// $product_name = mb_substr( $product_name, 0, 40 );

		$metadata[ $product_name ] = $item_quantity;
	}

	return $metadata;
}

/**
 * 1) Modern on-site card form (UPE / Elements → PaymentIntent)
 * Hook: wc_stripe_intent_metadata (2 args)
 */
add_filter( 'wc_stripe_intent_metadata', __NAMESPACE__ . '\soffun_stripe_intent_metadata', 10, 2 );
function soffun_stripe_intent_metadata( $metadata, $order ) {
	if ( ! $order instanceof WC_Order ) {
		return $metadata;
	}
	$extra = soffun_build_metadata_exact_names( $order );
	return array_merge( $metadata, $extra );
}

/**
 * 2) Stripe Checkout redirect (Checkout Session → PaymentIntent)
 * Hook: wc_stripe_create_session_params (2 args)
 * Put metadata under payment_intent_data.metadata
 */
add_filter( 'wc_stripe_create_session_params', __NAMESPACE__ . '\soffun_stripe_checkout_session_params', 10, 2 );
function soffun_stripe_checkout_session_params( $params, $order ) {
	if ( ! $order instanceof WC_Order ) {
		return $params;
	}
	$extra = soffun_build_metadata_exact_names( $order );

	if ( ! isset( $params['payment_intent_data'] ) || ! is_array( $params['payment_intent_data'] ) ) {
		$params['payment_intent_data'] = [];
	}
	if ( ! isset( $params['payment_intent_data']['metadata'] ) || ! is_array( $params['payment_intent_data']['metadata'] ) ) {
		$params['payment_intent_data']['metadata'] = [];
	}

	$params['payment_intent_data']['metadata'] = array_merge(
		$params['payment_intent_data']['metadata'],
		$extra
	);

	return $params;
}

/**
 * 3) Legacy fallback (older gateway flows)
 * Hook: wc_stripe_payment_metadata (3 args)
 * Keeps your original behavior for older installs.
 */
add_filter( 'wc_stripe_payment_metadata', __NAMESPACE__ . '\include_custom_stripe_metadata_legacy', 10, 3 );
function include_custom_stripe_metadata_legacy( $metadata, $order, $source ) {
	if ( ! $order instanceof WC_Order ) {
		return $metadata;
	}
	$extra = soffun_build_metadata_exact_names( $order );
	return array_merge( $metadata, $extra );
}

/**
 * Note:
 * Removed:
 *   $metadata["order_object"] = json_encode( $order->get_items() );
 * Stripe metadata has strict limits (≤ 50 keys total, key ≤ 40 chars, value ≤ 500 chars).
 * Use wp_order_id to fetch full order details on your server when needed.
 */

/* Optional: debug markers during testing (remove after validation)
add_filter( 'wc_stripe_intent_metadata', function( $m, $o ) { $m['debug_marker'] = 'upe-hook-ok'; return $m; }, 99, 2 );
add_filter( 'wc_stripe_create_session_params', function( $p, $o ) { $p['payment_intent_data']['metadata']['debug_marker'] = 'checkout-hook-ok'; return $p; }, 99, 2 );
add_filter( 'wc_stripe_payment_metadata', function( $m, $o, $s ) { $m['debug_marker'] = 'legacy-hook-ok'; return $m; }, 99, 3 );
*/

