<?php
/**
 * Known third-party providers: detection patterns and cookie presets.
 * Used by the Scan tool, one-click presets, and the automatic script/iframe gating.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Providers {

	/**
	 * @return array<string, array{name:string, category:string, match:string[], cookies:array[]}>
	 */
	public static function all() {
		$p = array(
			'google-analytics' => array(
				'name'     => 'Google Analytics (GA4)',
				'category' => 'analytics',
				'match'    => array( 'google-analytics.com', 'googletagmanager.com/gtag/js', 'gtag(' ),
				'cookies'  => array(
					array( 'name' => '_ga', 'duration' => '2 years', 'purpose' => 'Distinguishes unique visitors by assigning a randomly generated client ID.' ),
					array( 'name' => '_ga_*', 'duration' => '2 years', 'purpose' => 'Persists session state for a specific Google Analytics property.' ),
					array( 'name' => '_gid', 'duration' => '24 hours', 'purpose' => 'Distinguishes visitors for 24 hours.' ),
				),
			),
			'google-tag-manager' => array(
				'name'     => 'Google Tag Manager',
				'category' => 'necessary',
				'match'    => array( 'googletagmanager.com/gtm.js' ),
				'cookies'  => array(),
			),
			'google-ads' => array(
				'name'     => 'Google Ads',
				'category' => 'marketing',
				'match'    => array( 'googleadservices.com', 'doubleclick.net', 'googlesyndication.com' ),
				'cookies'  => array(
					array( 'name' => '_gcl_au', 'duration' => '3 months', 'purpose' => 'Stores and tracks conversions from Google Ads.' ),
					array( 'name' => 'IDE', 'duration' => '1 year', 'purpose' => 'Used by DoubleClick to measure and target advertising.', 'domain' => '.doubleclick.net' ),
				),
			),
			'meta-pixel' => array(
				'name'     => 'Meta (Facebook) Pixel',
				'category' => 'marketing',
				'match'    => array( 'connect.facebook.net', 'fbq(' ),
				'cookies'  => array(
					array( 'name' => '_fbp', 'duration' => '3 months', 'purpose' => 'Used by Meta to deliver advertising and measure ad performance.' ),
					array( 'name' => 'fr', 'duration' => '3 months', 'purpose' => 'Meta advertising and measurement.', 'domain' => '.facebook.com' ),
				),
			),
			'linkedin-insight' => array(
				'name'     => 'LinkedIn Insight Tag',
				'category' => 'marketing',
				'match'    => array( 'snap.licdn.com', 'px.ads.linkedin.com' ),
				'cookies'  => array(
					array( 'name' => 'li_sugr', 'duration' => '3 months', 'purpose' => 'Used by LinkedIn to make a probabilistic match of a user\'s identity.' ),
					array( 'name' => 'bcookie', 'duration' => '1 year', 'purpose' => 'LinkedIn browser identifier.', 'domain' => '.linkedin.com' ),
					array( 'name' => 'UserMatchHistory', 'duration' => '1 month', 'purpose' => 'LinkedIn Ads ID syncing.', 'domain' => '.linkedin.com' ),
				),
			),
			'hotjar' => array(
				'name'     => 'Hotjar',
				'category' => 'analytics',
				'match'    => array( 'static.hotjar.com', 'script.hotjar.com' ),
				'cookies'  => array(
					array( 'name' => '_hjSessionUser_*', 'duration' => '1 year', 'purpose' => 'Persists the Hotjar user ID across sessions on the same site.' ),
					array( 'name' => '_hjSession_*', 'duration' => '30 minutes', 'purpose' => 'Holds current session data so page views in the same session are attributed together.' ),
				),
			),
			'microsoft-clarity' => array(
				'name'     => 'Microsoft Clarity',
				'category' => 'analytics',
				'match'    => array( 'clarity.ms' ),
				'cookies'  => array(
					array( 'name' => '_clck', 'duration' => '1 year', 'purpose' => 'Persists the Clarity user ID and preferences.' ),
					array( 'name' => '_clsk', 'duration' => '1 day', 'purpose' => 'Connects multiple page views by a user into a single Clarity session recording.' ),
				),
			),
			'hubspot' => array(
				'name'     => 'HubSpot',
				'category' => 'marketing',
				'match'    => array( 'js.hs-scripts.com', 'js.hsforms.net', 'hs-analytics.net' ),
				'cookies'  => array(
					array( 'name' => '__hstc', 'duration' => '6 months', 'purpose' => 'Main HubSpot tracking cookie: domain, visitor ID, timestamps and session count.' ),
					array( 'name' => 'hubspotutk', 'duration' => '6 months', 'purpose' => 'Keeps track of a visitor\'s identity for form submissions.' ),
					array( 'name' => '__hssc', 'duration' => '30 minutes', 'purpose' => 'Tracks sessions.' ),
					array( 'name' => '__hssrc', 'duration' => 'Session', 'purpose' => 'Detects whether the visitor has restarted their browser.' ),
				),
			),
			'youtube' => array(
				'name'     => 'YouTube',
				'category' => 'functional',
				'match'    => array( 'youtube.com/embed', 'youtube-nocookie.com/embed', 'youtu.be' ),
				'cookies'  => array(
					array( 'name' => 'VISITOR_INFO1_LIVE', 'duration' => '6 months', 'purpose' => 'Estimates bandwidth for embedded YouTube video.', 'domain' => '.youtube.com' ),
					array( 'name' => 'YSC', 'duration' => 'Session', 'purpose' => 'Registers a unique ID to keep statistics of which YouTube videos have been viewed.', 'domain' => '.youtube.com' ),
				),
			),
			'vimeo' => array(
				'name'     => 'Vimeo',
				'category' => 'functional',
				'match'    => array( 'player.vimeo.com' ),
				'cookies'  => array(
					array( 'name' => 'vuid', 'duration' => '2 years', 'purpose' => 'Collects data on the user\'s visits, such as which videos were watched.', 'domain' => '.vimeo.com' ),
				),
			),
			'google-maps' => array(
				'name'     => 'Google Maps',
				'category' => 'functional',
				'match'    => array( 'google.com/maps/embed', 'maps.googleapis.com' ),
				'cookies'  => array(
					array( 'name' => 'NID', 'duration' => '6 months', 'purpose' => 'Stores visitor preferences and personalises Google services including embedded maps.', 'domain' => '.google.com' ),
				),
			),
			'recaptcha' => array(
				'name'     => 'Google reCAPTCHA',
				'category' => 'necessary',
				'match'    => array( 'google.com/recaptcha', 'gstatic.com/recaptcha' ),
				'cookies'  => array(
					array( 'name' => '_GRECAPTCHA', 'duration' => '6 months', 'purpose' => 'Used by reCAPTCHA to distinguish humans from bots and protect forms from spam.', 'domain' => 'www.google.com' ),
				),
			),
			'x-pixel' => array(
				'name'     => 'X (Twitter) Pixel',
				'category' => 'marketing',
				'match'    => array( 'static.ads-twitter.com' ),
				'cookies'  => array(
					array( 'name' => 'muc_ads', 'duration' => '2 years', 'purpose' => 'Used by X to deliver and measure advertising.', 'domain' => '.t.co' ),
				),
			),
			'tiktok-pixel' => array(
				'name'     => 'TikTok Pixel',
				'category' => 'marketing',
				'match'    => array( 'analytics.tiktok.com' ),
				'cookies'  => array(
					array( 'name' => '_ttp', 'duration' => '13 months', 'purpose' => 'Used by TikTok to identify visitors for advertising measurement.' ),
				),
			),
			'wordpress' => array(
				'name'     => 'WordPress',
				'category' => 'necessary',
				'match'    => array(),
				'cookies'  => array(
					array( 'name' => 'wordpress_test_cookie', 'duration' => 'Session', 'purpose' => 'Checks whether the browser accepts cookies.' ),
					array( 'name' => 'wordpress_logged_in_*', 'duration' => 'Session', 'purpose' => 'Keeps registered users logged in.' ),
					array( 'name' => 'wp-settings-*', 'duration' => '1 year', 'purpose' => 'Stores admin interface preferences for logged-in users.' ),
					array( 'name' => VUL_COOKIE, 'duration' => 'Up to 1 year', 'purpose' => 'Records the cookie consent choices you make on this site so we don\'t ask again.' ),
				),
			),
			'gravity-forms' => array(
				'name'     => 'Gravity Forms',
				'category' => 'necessary',
				'match'    => array( 'gravityforms' ),
				'cookies'  => array(
					array( 'name' => 'gf_*', 'duration' => 'Session', 'purpose' => 'Supports multi-page forms and saved form progress.' ),
				),
			),
			'woocommerce' => array(
				'name'     => 'WooCommerce',
				'category' => 'necessary',
				'match'    => array( 'woocommerce' ),
				'cookies'  => array(
					array( 'name' => 'woocommerce_cart_hash', 'duration' => 'Session', 'purpose' => 'Helps WooCommerce determine when cart contents change.' ),
					array( 'name' => 'woocommerce_items_in_cart', 'duration' => 'Session', 'purpose' => 'Helps WooCommerce determine when cart contents change.' ),
					array( 'name' => 'wp_woocommerce_session_*', 'duration' => '2 days', 'purpose' => 'Contains a unique code for each customer so WooCommerce knows where to find the cart data in the database.' ),
				),
			),
		);
		return apply_filters( 'vul_providers', $p );
	}

	/**
	 * Which provider (if any) matches a script src / iframe src / inline code?
	 */
	public static function match( $haystack ) {
		foreach ( self::all() as $slug => $p ) {
			foreach ( $p['match'] as $needle ) {
				if ( false !== stripos( $haystack, $needle ) ) {
					return $slug;
				}
			}
		}
		return null;
	}

	/**
	 * Category a URL should be gated to, based on provider matching. Null = don't gate.
	 */
	public static function category_for_url( $url ) {
		$slug = self::match( $url );
		if ( ! $slug ) {
			return null;
		}
		$cat = self::all()[ $slug ]['category'];
		return 'necessary' === $cat ? null : $cat;
	}

	/**
	 * Add all cookies for a provider to the registry. Returns number added.
	 */
	public static function add_preset( $slug ) {
		$all = self::all();
		if ( ! isset( $all[ $slug ] ) ) {
			return 0;
		}
		$p = $all[ $slug ];
		$n = 0;
		foreach ( $p['cookies'] as $c ) {
			$n += VUL_Registry::add_cookie( array(
				'name'     => $c['name'],
				'category' => $p['category'],
				'provider' => $p['name'],
				'purpose'  => $c['purpose'],
				'duration' => $c['duration'],
				'type'     => $c['type'] ?? 'http',
				'domain'   => $c['domain'] ?? '',
			) ) ? 1 : 0;
		}
		return $n;
	}

	/**
	 * Fetch the front page and report which known providers it loads.
	 *
	 * @return array{providers: string[], scripts: string[], iframes: string[], error?: string}
	 */
	public static function scan( $url = null ) {
		$url = $url ?: home_url( '/' );
		$res = wp_remote_get( add_query_arg( 'vul_scan', '1', $url ), array( 'timeout' => 15, 'sslverify' => false, 'user-agent' => 'VuLegals/' . VUL_VERSION ) );
		if ( is_wp_error( $res ) ) {
			return array( 'providers' => array(), 'scripts' => array(), 'iframes' => array(), 'error' => $res->get_error_message() );
		}
		$html    = wp_remote_retrieve_body( $res );
		$scripts = array();
		$iframes = array();
		$found   = array();

		if ( preg_match_all( '/<script[^>]+src=["\']([^"\']+)["\']/i', $html, $m ) ) {
			$scripts = array_values( array_unique( $m[1] ) );
		}
		if ( preg_match_all( '/<iframe[^>]+src=["\']([^"\']+)["\']/i', $html, $m ) ) {
			$iframes = array_values( array_unique( $m[1] ) );
		}
		if ( preg_match_all( '/<script\b(?![^>]*data-vul=)[^>]*>(.*?)<\/script>/is', $html, $m ) ) {
			foreach ( $m[1] as $inline ) {
				$slug = self::match( $inline );
				if ( $slug ) {
					$found[ $slug ] = true;
				}
			}
		}
		foreach ( array_merge( $scripts, $iframes ) as $src ) {
			$slug = self::match( $src );
			if ( $slug ) {
				$found[ $slug ] = true;
			}
		}
		// Always suggest core cookies.
		$found['wordpress'] = true;
		if ( class_exists( 'GFForms' ) ) {
			$found['gravity-forms'] = true;
		}
		if ( class_exists( 'WooCommerce' ) ) {
			$found['woocommerce'] = true;
		}

		$external = array_filter( $scripts, fn( $s ) => false === strpos( $s, wp_parse_url( home_url(), PHP_URL_HOST ) ) );

		return array(
			'providers' => array_keys( $found ),
			'scripts'   => array_values( $external ),
			'iframes'   => $iframes,
		);
	}
}
