<?php
/**
 * Gravity Forms: fills the Consent field checkbox label with the standard text + privacy link,
 * and adds {vu_legals:...} merge tags.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Gravity_Forms {

	public static function init() {
		if ( ! class_exists( 'GFForms' ) || ! vul_get( 'gf_enable' ) ) {
			return;
		}
		add_filter( 'gform_pre_render', array( __CLASS__, 'fill_consent' ) );
		add_filter( 'gform_pre_validation', array( __CLASS__, 'fill_consent' ) );
		add_filter( 'gform_pre_submission_filter', array( __CLASS__, 'fill_consent' ) );
		add_filter( 'gform_admin_pre_render', array( __CLASS__, 'fill_consent' ) );
		add_filter( 'gform_replace_merge_tags', array( __CLASS__, 'merge_tags' ), 10, 2 );
		add_filter( 'gform_custom_merge_tags', array( __CLASS__, 'register_merge_tags' ) );
	}

	public static function fill_consent( $form ) {
		if ( empty( $form['fields'] ) ) {
			return $form;
		}
		$override = (bool) vul_get( 'gf_override' );
		$text     = vul_merge( vul_get( 'gf_text' ) );
		foreach ( $form['fields'] as &$field ) {
			if ( 'consent' !== $field->type ) {
				continue;
			}
			if ( $override || '' === trim( (string) $field->checkboxLabel ) || 'I agree to the privacy policy.' === $field->checkboxLabel ) {
				$field->checkboxLabel = $text;
			}
		}
		return $form;
	}

	public static function register_merge_tags( $tags ) {
		$tags[] = array( 'label' => 'Vu Legals: Privacy policy URL', 'tag' => '{vu_legals:privacy_url}' );
		$tags[] = array( 'label' => 'Vu Legals: Cookie policy URL', 'tag' => '{vu_legals:cookie_url}' );
		$tags[] = array( 'label' => 'Vu Legals: Company name', 'tag' => '{vu_legals:company}' );
		$tags[] = array( 'label' => 'Vu Legals: Contact email', 'tag' => '{vu_legals:contact_email}' );
		return $tags;
	}

	public static function merge_tags( $text, $form ) {
		if ( false === strpos( $text, '{vu_legals:' ) ) {
			return $text;
		}
		return preg_replace_callback( '/\{vu_legals:([a-z_]+)\}/', fn( $m ) => vul_merge( '{' . $m[1] . '}' ), $text );
	}
}
