<?php
/**
 * Geo rules. No external lookups: uses CDN/host country headers when present,
 * otherwise a client-side timezone heuristic (Europe/*). Filterable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Geo {

	/** ISO codes where consent-before-tracking applies (EU 27 + EEA + UK + CH). */
	const EU_UK = array(
		'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
		'IS', 'LI', 'NO', 'GB', 'CH',
	);

	public static function init() {}

	/**
	 * Country code from server-side headers, or null if unknown.
	 */
	public static function country() {
		$headers = array(
			'HTTP_CF_IPCOUNTRY',          // Cloudflare
			'HTTP_X_COUNTRY_CODE',        // WP Engine GeoTarget / generic
			'HTTP_X_VERCEL_IP_COUNTRY',
			'HTTP_CLOUDFRONT_VIEWER_COUNTRY',
			'HTTP_X_APPENGINE_COUNTRY',
			'GEOIP_COUNTRY_CODE',
			'HTTP_X_GEO_COUNTRY',
		);
		$code = null;
		foreach ( $headers as $h ) {
			if ( ! empty( $_SERVER[ $h ] ) ) {
				$code = strtoupper( substr( sanitize_text_field( wp_unslash( $_SERVER[ $h ] ) ), 0, 2 ) );
				break;
			}
		}
		if ( 'XX' === $code || 'T1' === $code ) {
			$code = null;
		}
		return apply_filters( 'vul_country', $code );
	}

	/**
	 * Countries where the banner is required under current settings.
	 */
	public static function required_countries() {
		$mode = vul_get( 'geo_mode' );
		if ( 'custom' === $mode ) {
			$list = preg_split( '/[\s,]+/', strtoupper( vul_get( 'geo_countries' ) ), -1, PREG_SPLIT_NO_EMPTY );
			return array_values( array_unique( array_map( fn( $c ) => substr( $c, 0, 2 ), $list ) ) );
		}
		return self::EU_UK;
	}

	/** Is a full-page cache in play? Then per-visitor request headers can't be trusted. */
	public static function page_cache_active() {
		$active = defined( 'LSCWP_V' ) || defined( 'WP_ROCKET_VERSION' ) || defined( 'W3TC' ) || defined( 'WPCACHEHOME' )
			|| class_exists( 'WpeCommon' ) || defined( 'KINSTAMU_VERSION' ) || defined( 'CACHE_ENABLER_VERSION' ) || function_exists( 'wpfc_clear_all_cache' )
			|| ( defined( 'WP_CACHE' ) && WP_CACHE );
		return apply_filters( 'vul_page_cache_active', $active );
	}

	/**
	 * Decision for this request: 'show', 'implied', 'hide', or 'client' (JS decides by timezone).
	 */
	public static function decision() {
		$mode = vul_get( 'geo_mode' );
		if ( 'all' === $mode ) {
			return 'show';
		}
		if ( self::page_cache_active() ) {
			// Cached HTML is shared, so the browser must decide (timezone heuristic); otherwise show to all.
			return vul_get( 'geo_timezone_fallback' ) ? 'client' : 'show';
		}
		$country = self::country();
		if ( $country ) {
			return in_array( $country, self::required_countries(), true ) ? 'show' : vul_get( 'geo_outside' );
		}
		return vul_get( 'geo_timezone_fallback' ) ? 'client' : 'show';
	}
}
