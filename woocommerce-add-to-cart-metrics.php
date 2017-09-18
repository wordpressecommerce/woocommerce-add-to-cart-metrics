<?php
/**
 * Plugin Name: WooCommerce Add to Cart Metrics
 * Description: Record add-to-cart metrics in a custom DB table when WooCommerce is active.
 * Version:     1.0.0
 * Author:      Liquid Web
 * Author URI:  https://www.liquidweb.com
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: woocommerce-add-to-cart-metrics
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.2.0
 */
 
// Event tracking
add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'log_add_to_cart' ) );
add_action( 'wp_footer', array( $this, 'log_loop_add_to_cart' ) );
add_action( 'woocommerce_after_cart', array( $this, 'log_remove_from_cart' ) );
add_action( 'woocommerce_after_mini_cart', array( $this, 'log_remove_from_cart' ) );
add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'log_remove_from_cart_attributes' ), 10, 2 );
add_action( 'woocommerce_after_shop_loop_item', array( $this, 'log_listing_impression' ) );
add_action( 'woocommerce_after_shop_loop_item', array( $this, 'log_listing_click' ) );
add_action( 'woocommerce_after_single_product', array( $this, 'log_product_detail' ) );
add_action( 'woocommerce_after_checkout_form', array( $this, 'log_checkout_process' ) );
}

function log_add_to_cart() {
	 $this->log_wc( 'add-to-cart' );
}

function log_loop_add_to_cart() {
	 $this->log_wc( 'add-to-cart' );
}

register_activation_hook( __FILE__, 'wc_metrics_create_db' );
function wc_metrics_create_db() {
	// Create DB Here
}

function get_db_version_option_key() {
	 return 'lw_wc_metric_log';
}


function wc_metrics_create_new_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'wc_metric_log';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date datetime DEFAULT CURRENT_TIMESTAMP,
		views smallint(5) NOT NULL,
		clicks smallint(5) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

register_deactivation_hook( __FILE__, 'wc_metrics_remove_database' );
function wc_metrics_remove_database() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'wc_metric_log';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("lw_wc_metric_log");
}   
