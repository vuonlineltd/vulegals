<?php
/**
 * Removes options, registry posts and the consent log table on uninstall (not on deactivate).
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
global $wpdb;
delete_option( 'vu_legals_settings' );
delete_transient( 'vul_cookies' );
delete_transient( 'vul_scripts' );
wp_clear_scheduled_hook( 'vul_prune_log' );
$ids = get_posts( array( 'post_type' => array( 'vul_cookie', 'vul_script' ), 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
foreach ( $ids as $id ) {
	wp_delete_post( $id, true );
}
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}vul_consent_log" ); // phpcs:ignore
