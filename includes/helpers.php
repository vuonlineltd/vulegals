<?php
/**
 * Shared helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a setting (merged with defaults). Pass no key for the whole array.
 */
function vul_get( $key = null, $fallback = null ) {
	static $settings = null;
	if ( null === $settings ) {
		$saved    = get_option( VUL_OPTION, array() );
		$settings = wp_parse_args( is_array( $saved ) ? $saved : array(), VUL_Settings::defaults() );
		// Nested category settings need a per-key merge.
		foreach ( VUL_Settings::defaults()['categories'] as $slug => $cat ) {
			$settings['categories'][ $slug ] = wp_parse_args( $settings['categories'][ $slug ] ?? array(), $cat );
		}
	}
	if ( null === $key ) {
		return $settings;
	}
	return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
}

/**
 * Consent categories, in display order. Only enabled ones.
 *
 * @return array<string, array{label:string, description:string, enabled:bool, locked:bool}>
 */
function vul_categories( $include_disabled = false ) {
	$cats = vul_get( 'categories' );
	$out  = array();
	foreach ( array( 'necessary', 'functional', 'analytics', 'marketing' ) as $slug ) {
		if ( ! isset( $cats[ $slug ] ) ) {
			continue;
		}
		if ( ! $include_disabled && empty( $cats[ $slug ]['enabled'] ) && 'necessary' !== $slug ) {
			continue;
		}
		$out[ $slug ] = $cats[ $slug ];
	}
	return apply_filters( 'vul_categories', $out );
}

/**
 * Replace {merge} fields in a string with site/company values.
 */
function vul_merge( $text ) {
	$privacy_id = (int) vul_get( 'privacy_page' );
	$cookie_id  = (int) vul_get( 'cookie_page' );
	$map        = array(
		'{company}'        => vul_get( 'company_name' ) ?: get_bloginfo( 'name' ),
		'{site}'           => get_bloginfo( 'name' ),
		'{site_url}'       => home_url( '/' ),
		'{contact_email}'  => vul_get( 'contact_email' ) ?: get_option( 'admin_email' ),
		'{address}'        => vul_get( 'company_address' ),
		'{company_number}' => vul_get( 'company_number' ),
		'{ico_number}'     => vul_get( 'ico_number' ),
		'{privacy_url}'    => $privacy_id ? get_permalink( $privacy_id ) : home_url( '/privacy-policy/' ),
		'{cookie_url}'     => $cookie_id ? get_permalink( $cookie_id ) : home_url( '/cookie-policy/' ),
		'{terms_url}'      => vul_get( 'terms_page' ) ? get_permalink( (int) vul_get( 'terms_page' ) ) : home_url( '/terms/' ),
		'{accessibility_url}' => vul_get( 'accessibility_page' ) ? get_permalink( (int) vul_get( 'accessibility_page' ) ) : home_url( '/accessibility/' ),
		'{updated}'        => vul_get( 'policy_updated' ) ? wp_date( get_option( 'date_format' ), strtotime( vul_get( 'policy_updated' ) ) ) : wp_date( get_option( 'date_format' ) ),
		'{version}'        => vul_get( 'policy_version' ),
	);
	$map = apply_filters( 'vul_merge_fields', $map );
	return strtr( (string) $text, $map );
}

/**
 * Current consent state as an array of category => bool, or null if no decision recorded.
 */
function vul_consent_state() {
	return VUL_Consent::state();
}

/**
 * Has the visitor consented to a category? Necessary is always true.
 */
function vul_has_consent( $category ) {
	if ( 'necessary' === $category ) {
		return true;
	}
	$state = VUL_Consent::state();
	return $state ? ! empty( $state['cats'][ $category ] ) : false;
}

/**
 * Wrap inline script markup so it is gated until consent for $category is given.
 * Use in templates: echo vul_gate( '<script>...</script>', 'analytics' );
 */
function vul_gate( $html, $category ) {
	return VUL_Consent::gate_html( $html, $category );
}

/**
 * A link/button that opens the preferences modal.
 */
function vul_preferences_link( $text = null, $class = '' ) {
	$text = $text ?: vul_get( 'text_manage' );
	return sprintf(
		'<a href="#" class="vul-open-preferences %s" data-vul-open>%s</a>',
		esc_attr( $class ),
		esc_html( $text )
	);
}
