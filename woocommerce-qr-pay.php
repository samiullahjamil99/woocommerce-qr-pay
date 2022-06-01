<?php
/**
 * @package Woocommerce_QR_Pay
 * @version 1.0.0
 */
/*
Plugin Name: Woocommerce QR Pay
Author: Samiullah Jamil
Version: 1.0.0
Author URI: https://www.samiullahjaml.com/about-me/
*/

include_once dirname(__FILE__) . '/inc/admin-page.php';

function woo_is_current_client_kiosk() {
	$woo_qr_hostnames = get_option("qr-hostnames");
	if (isset($woo_qr_hostnames)) {
		$hostnames_string = $woo_qr_hostnames['hostnames'];
		$hostnames = explode(",",$hostnames_string);
		$ipaddresses = array();
		foreach($hostnames as $hostname) {
			$ipaddresses[] = gethostbyname(trim($hostname));
		}
		//print_r($ipaddresses);
		if (in_array(get_client_ip(), $ipaddresses)) {
			return true;
		}
	}
	return true;
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

add_filter( 'woocommerce_locate_template', 'woo_qr_plugin_template', 10, 3 );
function woo_qr_plugin_template( $template, $template_name, $template_path ) {
     global $woocommerce;
     $_template = $template;
     if ( ! $template_path ) 
        $template_path = $woocommerce->template_url;
 
     $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/woocommerce/';
 
    // Look within passed path within the theme - this is priority
    $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
   );
 
   if( ! $template && file_exists( $plugin_path . $template_name ) && woo_is_current_client_kiosk() )
    $template = $plugin_path . $template_name;
 
   if ( ! $template )
    $template = $_template;

   return $template;
}
function woo_qr_code_checkout_script() {
	if (woo_is_current_client_kiosk()) {
		wp_dequeue_script( 'wc-checkout' );
		if (is_checkout()) {
			wp_enqueue_script('qrcode',plugin_dir_url(__FILE__) . 'assets/js/qrcode.min.js',array('jquery'));
		}
	}
}
add_action( 'wp_enqueue_scripts', 'woo_qr_code_checkout_script',99 );

add_action( 'wp_ajax_pay_by_cash', 'woo_qr_pay_by_cash' );
add_action( 'wp_ajax_nopriv_pay_by_cash', 'woo_qr_pay_by_cash' );
function woo_qr_pay_by_cash() {
	$response = array();
	$cart_hash          = WC()->cart->get_cart_hash();
	//$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
	//$payment_method = "cod";
	$order = new WC_Order();
	$order->set_created_via( 'checkout' );
	$order->set_cart_hash( $cart_hash );
	$order->set_currency( get_woocommerce_currency() );
	$order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
	$order->set_customer_user_agent( wc_get_user_agent() );
	//$order->set_payment_method( isset( $available_gateways[ $payment_method ] ) ? $available_gateways[ $payment_method ] : $payment_method );
	WC()->checkout->set_data_from_cart( $order );
	$order_id = $order->save();
	$order->payment_complete();
	wc_empty_cart();
	$response['message'] = print_r($order_id,true);
	$response['redirect'] = $order->get_checkout_order_received_url();
	wp_send_json($response);
}

add_action( 'wp_ajax_pay_by_card', 'woo_qr_pay_by_card' );
add_action( 'wp_ajax_nopriv_pay_by_card', 'woo_qr_pay_by_card' );
function woo_qr_pay_by_card() {
	$response = array();
	$cart_hash          = WC()->cart->get_cart_hash();
	//$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
	$payment_method = "cod";
	$order = new WC_Order();
	$order->set_created_via( 'checkout' );
	$order->set_cart_hash( $cart_hash );
	$order->set_currency( get_woocommerce_currency() );
	$order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
	$order->set_customer_user_agent( wc_get_user_agent() );
	//$order->set_payment_method( isset( $available_gateways[ $payment_method ] ) ? $available_gateways[ $payment_method ] : $payment_method );
	WC()->checkout->set_data_from_cart( $order );
	$order_id = $order->save();
	WC()->session->set( 'order_awaiting_payment', $order_id );
	wc_empty_cart();
	$response['message'] = $order->get_checkout_payment_url();
	$response['paylink'] = $order->get_checkout_payment_url();
	$response['orderid'] = $order_id;
	wp_send_json($response);
}

add_action('wp_ajax_qr_check_order_status','woo_qr_order_status');
function woo_qr_order_status() {
	$response = array();
	$orderid = intval($_POST['orderid']);
	$order = wc_get_order( $orderid );
	if ($order) {
		$response['status'] = $order->get_status();
		$response['redirect'] = $order->get_checkout_order_received_url();
	}
	wp_send_json($response);
}