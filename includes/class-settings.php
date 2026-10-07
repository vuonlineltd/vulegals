<?php
/**
 * Settings: defaults, registration and sanitisation. One option array.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Settings {

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	public static function defaults() {
		return array(
			// General / company.
			'company_name'         => '',
			'company_address'      => '',
			'company_number'       => '',
			'ico_number'           => '',
			'contact_email'        => '',
			'privacy_page'         => 0,
			'cookie_page'          => 0,
			'policy_version'       => '1',
			'policy_updated'       => '',
			'cookie_expiry_days'   => 180,
			'log_consent'          => 1,
			'log_retention_days'   => 365,

			// Banner.
			'layout'               => 'box-left', // bar | box-left | box-right | modal
			'overlay'              => 0,
			'show_reject'          => 1,
			'show_manage'          => 1,
			'show_icon'            => 1,
			'text_title'           => 'We use cookies',
			'text_body'            => 'We use essential cookies to make this site work, and optional cookies to understand how it is used and to improve it. Read our {cookie_link}.',
			'text_cookie_link'     => 'cookie policy',
			'text_accept'          => 'Accept all',
			'text_reject'          => 'Reject non-essential',
			'text_manage'          => 'Manage preferences',
			'text_save'            => 'Save preferences',
			'text_prefs_title'     => 'Cookie preferences',
			'text_prefs_body'      => 'Choose which optional cookies you are happy for us to use. You can change this at any time from the link in the footer.',
			'text_always_on'       => 'Always on',
			'text_close'           => 'Close',
			'text_embed_blocked'   => 'This content is blocked until you accept {category} cookies.',
			'text_embed_button'    => 'Accept and load',
			'text_inline_nochoice' => 'You have not made a choice yet. Optional cookies are off.',
			'text_inline_current'  => 'Your current choices: {choices}. Last updated {date}.',
			'text_inline_saved'    => 'Saved. Your choices apply immediately.',
			'text_inline_unsaved'  => 'You have unsaved changes. Click "Save preferences" to apply them.',
			'text_inline_noscript' => 'JavaScript is required to change your cookie settings here. You can also clear cookies for this site in your browser.',
			'policy_inline_panel'  => 1,

			// Style.
			'style_mode'           => 'custom', // custom | theme
			'colour_bg'            => '#ffffff',
			'colour_fg'            => '#1b1c26',
			'colour_muted'         => '#5c5f6e',
			'colour_accent'        => '#1b1c26',
			'colour_accent_fg'     => '#ffffff',
			'colour_border'        => '#e6e4dd',
			'radius'               => '12px',
			'font'                 => 'inherit',
			'custom_css'           => '',

			// Categories.
			'categories'           => array(
				'necessary'  => array(
					'enabled'     => 1,
					'default_on'  => 1,
					'label'       => 'Strictly necessary',
					'description' => 'Required for the site to work: security, load balancing, remembering your consent choices. These cannot be switched off.',
				),
				'functional' => array(
					'enabled'     => 1,
					'default_on'  => 0,
					'label'       => 'Functional',
					'description' => 'Remember choices you make (such as language or region) and provide enhanced features like embedded video and maps.',
				),
				'analytics'  => array(
					'enabled'     => 1,
					'default_on'  => 0,
					'label'       => 'Analytics',
					'description' => 'Help us understand how visitors use the site so we can improve it. Data is aggregated and does not identify you directly.',
				),
				'marketing'  => array(
					'enabled'     => 1,
					'default_on'  => 0,
					'label'       => 'Marketing',
					'description' => 'Used by advertising partners to build a profile of your interests and show you relevant adverts on other sites.',
				),
			),

			// Script gating.
			'handle_map'           => '',
			'auto_block'           => 1,
			'block_embeds'         => 1,
			'embed_category'       => 'functional',
			'block_patterns'       => '',

			// Integrations.
			'gtm_id'               => '',
			'gtm_load'             => 0,
			'consent_mode'         => 1,
			'consent_mode_wait'    => 500,
			'url_passthrough'      => 0,
			'sitekit_takeover'     => 1,
			'gf_enable'            => 1,
			'gf_override'          => 0,
			'gf_text'              => 'I agree to {company} storing and processing my data as described in the <a href="{privacy_url}">privacy policy</a>.',

			// Documents.
			'entity_type'          => 'limited', // limited | llp | sole | partnership | charity
			'jurisdiction'         => 'england-wales',
			'company_phone'        => '',
			'vat_number'           => '',
			'regulator'            => '',
			'professional_body'    => '',
			'contact_page'         => 0,
			'terms_page'           => 0,
			'accessibility_page'   => 0,
			'site_forms'           => 1,
			'site_newsletter'      => 0,
			'site_accounts'        => 0,
			'site_ecommerce'       => 0,
			'site_ugc'             => 0,
			'site_deeplinks'       => 1,
			'site_advice_area'     => '',
			'privacy_data'         => array(
				array( 'category' => 'Contact details', 'examples' => 'name, email address, telephone number, company', 'source' => 'Entered by you in our contact and enquiry forms, or given to us by email or phone', 'purpose' => 'To respond to your enquiry and provide our services', 'basis' => 'interests', 'retention' => 'Up to 2 years after our last contact' ),
				array( 'category' => 'Technical data', 'examples' => 'IP address, browser type, device, pages visited', 'source' => 'Collected automatically by our web server and, if you allow it, analytics cookies', 'purpose' => 'To keep the site secure and understand how it is used', 'basis' => 'interests', 'retention' => 'Server logs up to 90 days; analytics up to 26 months' ),
			),
			'privacy_retention_default' => 'As long as necessary for the purpose it was collected, then securely deleted',
			'privacy_transfers'    => 'uk_eea', // uk | uk_eea | international
			'privacy_transfer_safeguards' => 'UK adequacy regulations, the UK International Data Transfer Agreement, or the UK-US Data Bridge, as applicable',
			'privacy_processors'   => "WP Engine | website hosting | USA / UK data centres\nGravity Forms | form submissions stored in our website database | UK",
			'privacy_processors_auto' => 1,
			'privacy_marketing_channels' => 'email',
			'privacy_marketing_optout' => 'using the unsubscribe link in any email, or by contacting us',
			'privacy_automated'    => 0,
			'privacy_children'     => 0,
			'a11y_wcag_level'      => 'WCAG 2.2 level AA',
			'a11y_status'          => 'partial', // full | partial | non
			'a11y_known_issues'    => '',
			'a11y_tested_date'     => '',
			'a11y_tested_how'      => 'self-assessment using automated tools (Lighthouse, axe) and manual keyboard and screen reader checks',
			'docs_meta'            => array(),
			'docs_overrides'       => array(),

			// Geo.
			'geo_mode'             => 'all', // all | eu_uk | custom
			'geo_countries'        => '',
			'geo_outside'          => 'implied', // implied | hide
			'geo_timezone_fallback' => 1,
		);
	}

	public static function install_defaults() {
		if ( false === get_option( VUL_OPTION ) ) {
			$defaults                   = self::defaults();
			$defaults['policy_updated'] = wp_date( 'Y-m-d' );
			$defaults['contact_email']  = get_option( 'admin_email' );
			$defaults['company_name']   = get_bloginfo( 'name' );
			add_option( VUL_OPTION, $defaults );
		}
	}

	public static function register() {
		register_setting(
			'vu_legals',
			VUL_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
			)
		);
	}

	/**
	 * Sanitise the whole settings array. Unknown keys dropped, known keys typed.
	 */
	public static function sanitize( $input ) {
		$defaults = self::defaults();
		$current  = wp_parse_args( get_option( VUL_OPTION, array() ), $defaults );
		$input    = is_array( $input ) ? $input : array();
		$out      = $current; // Tabs post partial data; keep unposted keys.

		$bools   = array( 'log_consent', 'policy_inline_panel', 'overlay', 'show_reject', 'show_manage', 'show_icon', 'auto_block', 'block_embeds', 'gtm_load', 'consent_mode', 'url_passthrough', 'sitekit_takeover', 'gf_enable', 'gf_override', 'geo_timezone_fallback', 'site_forms', 'site_newsletter', 'site_accounts', 'site_ecommerce', 'site_ugc', 'site_deeplinks', 'privacy_processors_auto', 'privacy_automated', 'privacy_children' );
		$ints    = array( 'privacy_page', 'cookie_page', 'contact_page', 'terms_page', 'accessibility_page', 'cookie_expiry_days', 'log_retention_days', 'consent_mode_wait' );
		$text    = array( 'company_name', 'company_number', 'ico_number', 'policy_version', 'text_title', 'text_cookie_link', 'text_accept', 'text_reject', 'text_manage', 'text_save', 'text_prefs_title', 'text_always_on', 'text_close', 'text_embed_button', 'text_inline_nochoice', 'text_inline_current', 'text_inline_saved', 'text_inline_unsaved', 'text_inline_noscript', 'radius', 'font', 'gtm_id', 'company_phone', 'vat_number', 'regulator', 'professional_body', 'site_advice_area', 'privacy_retention_default', 'privacy_transfer_safeguards', 'privacy_marketing_channels', 'privacy_marketing_optout', 'a11y_wcag_level', 'a11y_tested_how' );
		$area    = array( 'company_address', 'text_body', 'text_prefs_body', 'text_embed_blocked', 'gf_text' );
		$code    = array( 'handle_map', 'block_patterns', 'custom_css', 'geo_countries', 'privacy_processors', 'a11y_known_issues' );
		$colours = array( 'colour_bg', 'colour_fg', 'colour_muted', 'colour_accent', 'colour_accent_fg', 'colour_border' );
		$enums   = array(
			'layout'         => array( 'bar', 'box-left', 'box-right', 'modal' ),
			'style_mode'     => array( 'custom', 'theme' ),
			'embed_category' => array( 'functional', 'analytics', 'marketing' ),
			'geo_mode'       => array( 'all', 'eu_uk', 'custom' ),
			'geo_outside'    => array( 'implied', 'hide' ),
			'entity_type'    => array( 'limited', 'llp', 'sole', 'partnership', 'charity' ),
			'jurisdiction'   => array( 'england-wales', 'scotland', 'northern-ireland' ),
			'privacy_transfers' => array( 'uk', 'uk_eea', 'international' ),
			'a11y_status'    => array( 'full', 'partial', 'non' ),
		);

		// Which tab posted? Booleans that were unticked won't be present, so only reset
		// booleans that belong to the posted tab.
		$tab = isset( $input['_tab'] ) ? sanitize_key( $input['_tab'] ) : 'all';
		$tab_bools = array(
			'general'      => array( 'log_consent', 'policy_inline_panel' ),
			'banner'       => array( 'overlay', 'show_reject', 'show_manage', 'show_icon' ),
			'style'        => array(),
			'categories'   => array(),
			'scripts'      => array( 'auto_block', 'block_embeds' ),
			'integrations' => array( 'gtm_load', 'consent_mode', 'url_passthrough', 'sitekit_takeover', 'gf_enable', 'gf_override' ),
			'geo'          => array( 'geo_timezone_fallback' ),
			'documents'    => array( 'site_forms', 'site_newsletter', 'site_accounts', 'site_ecommerce', 'site_ugc', 'site_deeplinks', 'privacy_processors_auto', 'privacy_automated', 'privacy_children' ),
		);
		$reset_bools = 'all' === $tab ? $bools : ( $tab_bools[ $tab ] ?? array() );
		foreach ( $reset_bools as $k ) {
			$out[ $k ] = 0;
		}

		foreach ( $input as $k => $v ) {
			if ( in_array( $k, $bools, true ) ) {
				$out[ $k ] = $v ? 1 : 0;
			} elseif ( in_array( $k, $ints, true ) ) {
				$out[ $k ] = absint( $v );
			} elseif ( in_array( $k, $text, true ) ) {
				$out[ $k ] = sanitize_text_field( $v );
			} elseif ( in_array( $k, $area, true ) ) {
				$out[ $k ] = wp_kses( $v, array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ), 'strong' => array(), 'em' => array(), 'br' => array() ) );
			} elseif ( in_array( $k, $code, true ) ) {
				$out[ $k ] = sanitize_textarea_field( $v );
			} elseif ( in_array( $k, $colours, true ) ) {
				$c         = sanitize_hex_color( $v );
				$out[ $k ] = $c ? $c : $current[ $k ];
			} elseif ( isset( $enums[ $k ] ) ) {
				$out[ $k ] = in_array( $v, $enums[ $k ], true ) ? $v : $current[ $k ];
			} elseif ( 'contact_email' === $k ) {
				$out[ $k ] = sanitize_email( $v ) ?: '';
			} elseif ( 'policy_updated' === $k || 'a11y_tested_date' === $k ) {
				$out[ $k ] = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
			} elseif ( 'privacy_data' === $k && is_array( $v ) ) {
				$rows = array();
				foreach ( $v as $row ) {
					if ( ! is_array( $row ) || '' === trim( (string) ( $row['category'] ?? '' ) ) ) {
						continue;
					}
					$rows[] = array(
						'category'  => sanitize_text_field( $row['category'] ),
						'examples'  => sanitize_text_field( $row['examples'] ?? '' ),
						'source'    => sanitize_text_field( $row['source'] ?? '' ),
						'purpose'   => sanitize_text_field( $row['purpose'] ?? '' ),
						'basis'     => in_array( $row['basis'] ?? '', array( 'consent', 'contract', 'legal', 'interests', 'vital', 'public' ), true ) ? $row['basis'] : 'interests',
						'retention' => sanitize_text_field( $row['retention'] ?? '' ),
					);
				}
				$out[ $k ] = $rows;
			} elseif ( 'docs_meta' === $k && is_array( $v ) ) {
				$meta = is_array( $current['docs_meta'] ) ? $current['docs_meta'] : array();
				foreach ( $v as $type => $m ) {
					$type = sanitize_key( $type );
					$meta[ $type ] = array(
						'version' => sanitize_text_field( $m['version'] ?? '1' ) ?: '1',
						'updated' => preg_match( '/^\d{4}-\d{2}-\d{2}$/', $m['updated'] ?? '' ) ? $m['updated'] : '',
					);
				}
				$out[ $k ] = $meta;
			} elseif ( 'docs_overrides' === $k && is_array( $v ) ) {
				$ov = is_array( $current['docs_overrides'] ) ? $current['docs_overrides'] : array();
				foreach ( $v as $type => $sections ) {
					$type = sanitize_key( $type );
					$ov[ $type ] = array();
					foreach ( (array) $sections as $sk => $text ) {
						$text = trim( (string) $text );
						if ( '' !== $text ) {
							$ov[ $type ][ sanitize_key( $sk ) ] = wp_kses_post( $text );
						}
					}
				}
				$out[ $k ] = $ov;
			} elseif ( 'categories' === $k && is_array( $v ) ) {
				foreach ( $defaults['categories'] as $slug => $def ) {
					$row = isset( $v[ $slug ] ) && is_array( $v[ $slug ] ) ? $v[ $slug ] : array();
					$out['categories'][ $slug ] = array(
						'enabled'     => 'necessary' === $slug ? 1 : ( ! empty( $row['enabled'] ) ? 1 : 0 ),
						'default_on'  => 'necessary' === $slug ? 1 : ( ! empty( $row['default_on'] ) ? 1 : 0 ),
						'label'       => isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : $def['label'],
						'description' => isset( $row['description'] ) ? sanitize_textarea_field( $row['description'] ) : $def['description'],
					);
				}
			}
		}

		if ( '' === trim( (string) $out['policy_version'] ) ) {
			$out['policy_version'] = '1';
		}
		$out['gtm_id'] = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', $out['gtm_id'] ) );

		return $out;
	}
}
