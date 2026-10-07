<?php
/**
 * Compatibility: Google Site Kit, WP Consent API, cache / JS-optimiser plugins.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Compat {

	public static function init() {
		// --- Google Site Kit: we own the tags and the consent signals. ---
		if ( vul_get( 'sitekit_takeover' ) ) {
			add_filter( 'googlesitekit_analytics-4_tag_blocked', '__return_true' );
			add_filter( 'googlesitekit_analytics_tag_blocked', '__return_true' );
			add_filter( 'googlesitekit_ads_tag_blocked', '__return_true' );
			add_filter( 'googlesitekit_tagmanager_tag_blocked', '__return_true' );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'dequeue_sitekit' ), 999 );
		}
		add_action( 'admin_notices', array( __CLASS__, 'sitekit_notice' ) );

		// --- WP Consent API (wp-consent-api plugin): declare compliance + let it read our state. ---
		add_filter( 'wp_consent_api_registered_' . plugin_basename( VUL_FILE ), '__return_true' );
		add_filter( 'wp_get_consent_type', fn() => 'optin' );

		// --- Caches / optimisers: never combine, defer or delay the consent runtime. ---
		add_filter( 'litespeed_optimize_js_excludes', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'litespeed_optm_js_defer_exc', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'litespeed_optm_gm_js_exc', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'litespeed_ucss_whitelist', array( __CLASS__, 'css_whitelist' ) );
		add_filter( 'rocket_exclude_js', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'rocket_exclude_defer_js', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'rocket_delay_js_exclusions', array( __CLASS__, 'js_excludes' ) );
		add_filter( 'rocket_rucss_safelist', array( __CLASS__, 'css_whitelist' ) );
		add_filter( 'perfmatters_delay_js_exclusions', array( __CLASS__, 'js_excludes' ) );
	}

	public static function dequeue_sitekit() {
		foreach ( array( 'google_gtagjs', 'googlesitekit-events-provider-gtag', 'googlesitekit-events-provider-content-events', 'googlesitekit-consent-mode' ) as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}

	public static function sitekit_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! defined( 'GOOGLESITEKIT_VERSION' ) ) {
			return;
		}
		if ( 'enabled' !== apply_filters( 'googlesitekit_consent_mode_status', '' ) ) {
			return;
		}
		$url = admin_url( 'admin.php?page=googlesitekit-settings' );
		echo '<div class="notice notice-warning"><p>Site Kit Consent Mode is switched on. Vu Legals already sends Google Consent Mode v2 from the banner, so two sets of signals will race. Turn it off under <a href="' . esc_url( $url ) . '">Site Kit → Settings → Admin Settings</a>, then purge any page cache.</p></div>';
	}

	public static function js_excludes( $list ) {
		$list = is_array( $list ) ? $list : array();
		foreach ( array( 'vu-legals.js', 'VUL_CONFIG', 'vu-legals', 'data-vul', 'dataLayer', "gtag('consent'", 'googletagmanager.com', 'gtag/js', 'gtm.js' ) as $x ) {
			$list[] = $x;
		}
		return array_values( array_unique( $list ) );
	}

	public static function css_whitelist( $list ) {
		$list = is_array( $list ) ? $list : array();
		foreach ( array( '.vul', '.vul-banner', '.vul-modal', '.vul-inline', '.vul-embed', '.vul-btn', '.vul-switch', '.vul-policy' ) as $x ) {
			$list[] = $x;
		}
		return $list;
	}
}
