<?php
/**
 * Front-end: assets, CSS variables, banner + preferences markup, JS config.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Frontend {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 50 );
		add_shortcode( 'vu_consent_link', array( __CLASS__, 'shortcode_link' ) );
		add_shortcode( 'vu_cookie_settings', array( __CLASS__, 'inline_panel' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
	}

	public static function body_class( $classes ) {
		$classes[] = 'vul-layout-' . vul_get( 'layout' );
		return $classes;
	}

	public static function assets() {
		if ( is_admin() ) {
			return;
		}
		$css = VUL_DIR . 'assets/css/vu-legals.css';
		$js  = VUL_DIR . 'assets/js/vu-legals.js';
		wp_enqueue_style( 'vu-legals', VUL_URL . 'assets/css/vu-legals.css', array(), (string) filemtime( $css ) );
		wp_add_inline_style( 'vu-legals', self::css_vars() );
		wp_enqueue_script( 'vu-legals', VUL_URL . 'assets/js/vu-legals.js', array(), (string) filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => false ) );
		wp_add_inline_script( 'vu-legals', 'window.VUL_CONFIG=' . wp_json_encode( self::config() ) . ';', 'before' );
	}

	/**
	 * CSS variables. In "theme" mode nothing is emitted: the theme defines --vul-* itself.
	 */
	public static function css_vars() {
		$out = '';
		if ( 'custom' === vul_get( 'style_mode' ) ) {
			$out .= sprintf(
				':root{--vul-bg:%s;--vul-fg:%s;--vul-muted:%s;--vul-accent:%s;--vul-accent-fg:%s;--vul-border:%s;--vul-radius:%s;--vul-font:%s;}',
				vul_get( 'colour_bg' ),
				vul_get( 'colour_fg' ),
				vul_get( 'colour_muted' ),
				vul_get( 'colour_accent' ),
				vul_get( 'colour_accent_fg' ),
				vul_get( 'colour_border' ),
				vul_get( 'radius' ),
				vul_get( 'font' ) ?: 'inherit'
			);
		}
		$out .= wp_strip_all_tags( (string) vul_get( 'custom_css' ) );
		return apply_filters( 'vul_css_vars', $out );
	}

	public static function config() {
		$cats = array();
		foreach ( vul_categories() as $slug => $c ) {
			$cats[ $slug ] = array(
				'label'   => $c['label'],
				'default' => 'necessary' === $slug ? true : (bool) $c['default_on'],
				'locked'  => 'necessary' === $slug,
			);
		}
		// Registered first-party cookie names per category, so withdrawal can delete them.
		$names = array();
		foreach ( VUL_Registry::cookies() as $c ) {
			if ( 'necessary' !== $c['category'] && 'http' === $c['type'] ) {
				$names[ $c['category'] ][] = $c['name'];
			}
		}
		$config = array(
			'cookie'      => VUL_COOKIE,
			'cookieNames' => $names,
			'version'     => (string) vul_get( 'policy_version' ),
			'expiry'      => (int) vul_get( 'cookie_expiry_days' ),
			'categories'  => $cats,
			'geo'         => VUL_Geo::decision(),
			'geoOutside'  => vul_get( 'geo_outside' ),
			'consentMode' => (bool) vul_get( 'consent_mode' ),
			'overlay'     => (bool) vul_get( 'overlay' ),
			'layout'      => vul_get( 'layout' ),
			'log'         => (bool) vul_get( 'log_consent' ),
			'restUrl'     => esc_url_raw( rest_url( 'vu-legals/v1/consent' ) ),
			'domain'      => apply_filters( 'vul_cookie_domain', '' ),
			'text'        => array(
				'nochoice' => vul_get( 'text_inline_nochoice' ),
				'current'  => vul_get( 'text_inline_current' ),
				'saved'    => vul_get( 'text_inline_saved' ),
				'unsaved'  => vul_get( 'text_inline_unsaved' ),
			),
		);
		return apply_filters( 'vul_js_config', $config );
	}

	public static function shortcode_link( $atts ) {
		$atts = shortcode_atts( array( 'text' => '', 'class' => '' ), $atts, 'vu_consent_link' );
		return vul_preferences_link( $atts['text'], $atts['class'] );
	}

	/**
	 * Inline settings panel markup ([vu_cookie_settings], and auto-included in the policy).
	 */
	public static function inline_panel() {
		if ( is_admin() && ! wp_doing_ajax() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return '';
		}
		ob_start();
		$template = locate_template( 'vu-legals/inline-panel.php' ) ?: VUL_DIR . 'templates/inline-panel.php';
		include apply_filters( 'vul_inline_panel_template', $template );
		return ob_get_clean();
	}

	public static function render() {
		if ( is_admin() || is_feed() ) {
			return;
		}
		// Markup is always rendered (hidden); JS decides whether to show it, so cached HTML is visitor-neutral.
		$cookie_id  = (int) vul_get( 'cookie_page' );
		$cookie_url = $cookie_id ? get_permalink( $cookie_id ) : '';
		$link       = $cookie_url
			? '<a href="' . esc_url( $cookie_url ) . '">' . esc_html( vul_get( 'text_cookie_link' ) ) . '</a>'
			: esc_html( vul_get( 'text_cookie_link' ) );
		$body       = str_replace( '{cookie_link}', $link, wp_kses_post( vul_merge( vul_get( 'text_body' ) ) ) );
		$layout     = vul_get( 'layout' );
		$template = locate_template( 'vu-legals/banner.php' ) ?: VUL_DIR . 'templates/banner.php';
		include apply_filters( 'vul_banner_template', $template );
	}
}
