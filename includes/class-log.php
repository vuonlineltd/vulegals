<?php
/**
 * Consent log: proof of consent (UK GDPR Art. 7(1)). Custom table, REST endpoint, CSV export, pruning.
 * Stores a hashed IP only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Log {

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'vul_consent_log';
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		dbDelta( "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			consent_id varchar(36) NOT NULL DEFAULT '',
			created datetime NOT NULL,
			policy_version varchar(20) NOT NULL DEFAULT '',
			source varchar(20) NOT NULL DEFAULT '',
			categories text NOT NULL,
			ip_hash varchar(64) NOT NULL DEFAULT '',
			user_agent varchar(255) NOT NULL DEFAULT '',
			url varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			KEY consent_id (consent_id),
			KEY created (created)
		) {$charset};" );
		if ( ! wp_next_scheduled( 'vul_prune_log' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'vul_prune_log' );
		}
	}

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
		add_action( 'vul_prune_log', array( __CLASS__, 'prune' ) );
		add_action( 'admin_post_vul_export_log', array( __CLASS__, 'export' ) );
	}

	public static function routes() {
		register_rest_route( 'vu-legals/v1', '/consent', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'record' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'id'     => array( 'type' => 'string', 'required' => true ),
				'v'      => array( 'type' => 'string', 'required' => true ),
				'c'      => array( 'type' => 'object', 'required' => true ),
				'source' => array( 'type' => 'string', 'required' => false ),
			),
		) );
	}

	public static function record( WP_REST_Request $req ) {
		if ( ! vul_get( 'log_consent' ) ) {
			return new WP_REST_Response( array( 'ok' => false, 'reason' => 'disabled' ), 200 );
		}
		// Pages are usually full-page cached, so a nonce would go stale; this is a write-only,
		// fully sanitised endpoint. Rate-limit per IP instead.
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key = 'vul_rl_' . md5( $ip );
		$n   = (int) get_transient( $key );
		if ( $n > 20 ) {
			return new WP_REST_Response( array( 'ok' => false, 'reason' => 'rate' ), 429 );
		}
		set_transient( $key, $n + 1, MINUTE_IN_SECONDS );

		global $wpdb;
		$id   = preg_replace( '/[^a-f0-9\-]/i', '', (string) $req->get_param( 'id' ) );
		if ( strlen( $id ) < 8 ) {
			return new WP_REST_Response( array( 'ok' => false, 'reason' => 'id' ), 400 );
		}
		$cats = array();
		foreach ( (array) $req->get_param( 'c' ) as $k => $v ) {
			$k = sanitize_key( $k );
			if ( $k ) {
				$cats[ $k ] = $v ? 1 : 0;
			}
		}
		$salt = wp_salt( 'auth' );
		$wpdb->insert( self::table(), array(
			'consent_id'     => substr( $id, 0, 36 ),
			'created'        => current_time( 'mysql', true ),
			'policy_version' => substr( sanitize_text_field( (string) $req->get_param( 'v' ) ), 0, 20 ),
			'source'         => substr( sanitize_key( (string) $req->get_param( 'source' ) ), 0, 20 ),
			'categories'     => wp_json_encode( $cats ),
			'ip_hash'        => $ip ? hash( 'sha256', $ip . $salt ) : '',
			'user_agent'     => substr( isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '', 0, 255 ),
			'url'            => substr( esc_url_raw( (string) $req->get_header( 'referer' ) ), 0, 255 ),
		) );
		return new WP_REST_Response( array( 'ok' => true ), 201 );
	}

	public static function prune() {
		global $wpdb;
		$days = (int) vul_get( 'log_retention_days' );
		if ( $days < 1 ) {
			return;
		}
		$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . self::table() . ' WHERE created < %s', gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function count() {
		global $wpdb;
		return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::table() ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function recent( $limit = 50 ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' ORDER BY id DESC LIMIT %d', $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function stats() {
		global $wpdb;
		$rows = $wpdb->get_results( 'SELECT source, COUNT(*) AS n FROM ' . self::table() . ' WHERE created > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY) GROUP BY source' ); // phpcs:ignore WordPress.DB.PreparedSQL
		$out  = array();
		foreach ( (array) $rows as $r ) {
			$out[ $r->source ] = (int) $r->n;
		}
		return $out;
	}

	public static function export() {
		if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'vul_export_log' ) ) {
			wp_die( 'Not allowed.' );
		}
		global $wpdb;
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=consent-log-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'id', 'consent_id', 'created_utc', 'policy_version', 'source', 'categories', 'ip_hash', 'user_agent', 'url' ) );
		$offset = 0;
		do {
			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' ORDER BY id ASC LIMIT %d OFFSET %d', 1000, $offset ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
			foreach ( $rows as $r ) {
				fputcsv( $out, $r );
			}
			$offset += 1000;
		} while ( count( $rows ) === 1000 );
		fclose( $out );
		exit;
	}
}
