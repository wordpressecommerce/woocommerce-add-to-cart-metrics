<?php
/**
 * Plugin Name: WooCommerce Add to Cart Metrics
 * Description: Track add-to-cart metrics in a custom DB table when WooCommerce is active.
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
add_action( 'woocommerce_after_add_to_cart_button', 'log_add_to_cart'  );
add_action( 'wp_footer', 'log_loop_add_to_cart'  );

function log_add_to_cart() {
	 $this->log_wc( 'add-to-cart' );
}

function log_loop_add_to_cart() {
	 $this->log_wc( 'add-to-cart' );
}

register_activation_hook( __FILE__, 'wc_metrics_create_db' );
function wc_metrics_create_db() {

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

function get_db_version_option_key() {
	 return 'lw_wc_metric_log';
}

register_deactivation_hook( __FILE__, 'wc_metrics_remove_database' );
function wc_metrics_remove_database() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'wc_metric_log';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("lw_wc_metric_log");
}   
