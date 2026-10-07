<?php
/**
 * Legal document engine: typed documents built from sections with {merge} tokens,
 * {if:flag}…{/if} conditionals, generated tables, and per-section admin overrides.
 *
 * Templates live in templates/docs/{type}.php and return an ordered array of sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Documents {

	public static function init() {
		add_shortcode( 'vu_document', array( __CLASS__, 'shortcode' ) );
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	/**
	 * Document types: slug => [ label, page setting key, default page title ].
	 */
	public static function types() {
		$types = array(
			'terms'         => array( 'label' => 'Website Terms of Use', 'page_key' => 'terms_page', 'title' => 'Terms of Use' ),
			'privacy'       => array( 'label' => 'Privacy Policy', 'page_key' => 'privacy_page', 'title' => 'Privacy Policy' ),
			'accessibility' => array( 'label' => 'Accessibility Statement', 'page_key' => 'accessibility_page', 'title' => 'Accessibility Statement' ),
			'cookies'       => array( 'label' => 'Cookie Policy', 'page_key' => 'cookie_page', 'title' => 'Cookie Policy' ),
		);
		return apply_filters( 'vul_document_types', $types );
	}

	public static function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type( VUL_DIR . 'blocks/document', array(
			'render_callback' => function ( $attrs ) {
				return self::render( $attrs['type'] ?? 'terms' );
			},
		) );
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'type' => 'terms' ), $atts, 'vu_document' );
		return self::render( sanitize_key( $atts['type'] ) );
	}

	/* ---------- Flags & tokens ---------- */

	/**
	 * Boolean flags available to {if:flag} blocks.
	 */
	public static function flags() {
		$s     = vul_get();
		$flags = array(
			'limited'       => in_array( $s['entity_type'], array( 'limited', 'llp', 'charity' ), true ),
			'sole'          => 'sole' === $s['entity_type'],
			'partnership'   => 'partnership' === $s['entity_type'],
			'vat'           => '' !== trim( (string) $s['vat_number'] ),
			'regulator'     => '' !== trim( (string) $s['regulator'] ),
			'body'          => '' !== trim( (string) $s['professional_body'] ),
			'ico'           => '' !== trim( (string) $s['ico_number'] ),
			'phone'         => '' !== trim( (string) $s['company_phone'] ),
			'address'       => '' !== trim( (string) $s['company_address'] ),
			'forms'         => (bool) $s['site_forms'],
			'newsletter'    => (bool) $s['site_newsletter'],
			'accounts'      => (bool) $s['site_accounts'],
			'ecommerce'     => (bool) $s['site_ecommerce'] || class_exists( 'WooCommerce' ),
			'ugc'           => (bool) $s['site_ugc'],
			'deeplinks'     => (bool) $s['site_deeplinks'],
			'advice'        => '' !== trim( (string) $s['site_advice_area'] ),
			'analytics'     => ! empty( $s['categories']['analytics']['enabled'] ),
			'marketing'     => ! empty( $s['categories']['marketing']['enabled'] ),
			'transfers'     => 'uk' !== $s['privacy_transfers'],
			'transfers_int' => 'international' === $s['privacy_transfers'],
			'automated'     => (bool) $s['privacy_automated'],
			'children'      => (bool) $s['privacy_children'],
			'cookies_page'  => (bool) $s['cookie_page'],
			'terms_page'    => (bool) $s['terms_page'],
			'privacy_page'  => (bool) $s['privacy_page'],
			'a11y_page'     => (bool) $s['accessibility_page'],
			'contact_page'  => (bool) $s['contact_page'],
			'a11y_issues'   => '' !== trim( (string) $s['a11y_known_issues'] ),
			'a11y_tested'   => '' !== trim( (string) $s['a11y_tested_date'] ),
			'a11y_full'     => 'full' === $s['a11y_status'],
			'a11y_partial'  => 'partial' === $s['a11y_status'],
			'a11y_non'      => 'non' === $s['a11y_status'],
			'scotland'      => 'scotland' === $s['jurisdiction'],
			'ni'            => 'northern-ireland' === $s['jurisdiction'],
		);
		return apply_filters( 'vul_document_flags', $flags );
	}

	/**
	 * Extra merge tokens beyond vul_merge().
	 */
	public static function tokens( $type ) {
		$s    = vul_get();
		$meta = self::meta( $type );
		$law  = array( 'england-wales' => 'England and Wales', 'scotland' => 'Scotland', 'northern-ireland' => 'Northern Ireland' );
		$map  = array(
			'{phone}'             => $s['company_phone'],
			'{vat_number}'        => $s['vat_number'],
			'{regulator}'         => $s['regulator'],
			'{professional_body}' => $s['professional_body'],
			'{jurisdiction}'      => $law[ $s['jurisdiction'] ] ?? 'England and Wales',
			'{registered_in}'     => $law[ $s['jurisdiction'] ] ?? 'England and Wales',
			'{advice_area}'       => $s['site_advice_area'],
			'{contact_url}'       => $s['contact_page'] ? get_permalink( (int) $s['contact_page'] ) : home_url( '/contact/' ),
			'{terms_url}'         => $s['terms_page'] ? get_permalink( (int) $s['terms_page'] ) : home_url( '/terms/' ),
			'{accessibility_url}' => $s['accessibility_page'] ? get_permalink( (int) $s['accessibility_page'] ) : home_url( '/accessibility/' ),
			'{site_host}'         => wp_parse_url( home_url(), PHP_URL_HOST ),
			'{doc_version}'       => $meta['version'],
			'{doc_updated}'       => $meta['updated'] ? wp_date( get_option( 'date_format' ), strtotime( $meta['updated'] ) ) : wp_date( get_option( 'date_format' ) ),
			'{retention_default}' => $s['privacy_retention_default'],
			'{transfer_safeguards}' => $s['privacy_transfer_safeguards'],
			'{marketing_channels}' => $s['privacy_marketing_channels'],
			'{wcag_level}'        => $s['a11y_wcag_level'],
			'{a11y_tested_date}'  => $s['a11y_tested_date'] ? wp_date( get_option( 'date_format' ), strtotime( $s['a11y_tested_date'] ) ) : '',
			'{a11y_tested_how}'   => $s['a11y_tested_how'],
			'{a11y_known_issues}' => self::lines_to_list( $s['a11y_known_issues'] ),
			'{data_table}'        => self::data_table(),
			'{retention_table}'   => self::retention_table(),
			'{processors_list}'   => self::processors_list(),
			'{marketing_optout}'  => $s['privacy_marketing_optout'],
			'{entity_description}' => self::entity_description(),
		);
		return apply_filters( 'vul_document_tokens', $map, $type );
	}

	private static function entity_description() {
		$s = vul_get();
		$name = $s['company_name'] ?: get_bloginfo( 'name' );
		$law  = array( 'england-wales' => 'England and Wales', 'scotland' => 'Scotland', 'northern-ireland' => 'Northern Ireland' );
		$in   = $law[ $s['jurisdiction'] ] ?? 'England and Wales';
		switch ( $s['entity_type'] ) {
			case 'limited':
				$d = sprintf( '%s, a company registered in %s under company number %s', $name, $in, $s['company_number'] ?: '[company number]' );
				break;
			case 'llp':
				$d = sprintf( '%s, a limited liability partnership registered in %s under number %s', $name, $in, $s['company_number'] ?: '[registration number]' );
				break;
			case 'charity':
				$d = sprintf( '%s, a registered charity (number %s)', $name, $s['company_number'] ?: '[charity number]' );
				break;
			case 'partnership':
				$d = sprintf( '%s, a partnership', $name );
				break;
			default:
				$d = sprintf( '%s, a sole trader', $name );
		}
		if ( $s['company_address'] ) {
			$d .= ( 'limited' === $s['entity_type'] || 'llp' === $s['entity_type'] ) ? ', whose registered office is at ' : ', whose business address is ';
			$d .= $s['company_address'];
		}
		return esc_html( $d );
	}

	/* ---------- Generated blocks ---------- */

	public static function data_rows() {
		$rows = vul_get( 'privacy_data' );
		return is_array( $rows ) ? array_values( array_filter( $rows, fn( $r ) => ! empty( $r['category'] ) ) ) : array();
	}

	private static function data_table() {
		$rows = self::data_rows();
		if ( ! $rows ) {
			return '<p><em>No data categories have been configured yet.</em></p>';
		}
		$out = '<table class="vul-policy__table vul-doc__table"><thead><tr><th>What we collect</th><th>How we collect it</th><th>Why we use it</th><th>Lawful basis</th></tr></thead><tbody>';
		foreach ( $rows as $r ) {
			$out .= sprintf(
				'<tr><td data-label="What we collect"><strong>%s</strong>%s</td><td data-label="How">%s</td><td data-label="Why">%s</td><td data-label="Lawful basis">%s</td></tr>',
				esc_html( $r['category'] ),
				! empty( $r['examples'] ) ? '<br><small>' . esc_html( $r['examples'] ) . '</small>' : '',
				esc_html( $r['source'] ?? '' ),
				esc_html( $r['purpose'] ?? '' ),
				esc_html( self::basis_label( $r['basis'] ?? '' ) )
			);
		}
		return $out . '</tbody></table>';
	}

	private static function retention_table() {
		$rows = self::data_rows();
		if ( ! $rows ) {
			return '';
		}
		$out = '<table class="vul-policy__table vul-doc__table"><thead><tr><th>Type of data</th><th>How long we keep it</th></tr></thead><tbody>';
		foreach ( $rows as $r ) {
			$out .= sprintf( '<tr><td data-label="Type of data">%s</td><td data-label="How long">%s</td></tr>', esc_html( $r['category'] ), esc_html( ! empty( $r['retention'] ) ? $r['retention'] : vul_get( 'privacy_retention_default' ) ) );
		}
		return $out . '</tbody></table>';
	}

	public static function basis_label( $key ) {
		$map = array(
			'consent'    => 'Consent (you can withdraw it at any time)',
			'contract'   => 'Performance of a contract with you',
			'legal'      => 'Compliance with a legal obligation',
			'interests'  => 'Our legitimate interests',
			'vital'      => 'Protection of vital interests',
			'public'     => 'Public task',
		);
		return $map[ $key ] ?? $key;
	}

	/**
	 * Processors: manual list (one per line "Name | purpose | location") merged with providers found in the cookie registry.
	 */
	public static function processors() {
		$list = array();
		foreach ( preg_split( '/\r?\n/', (string) vul_get( 'privacy_processors' ) ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = array_map( 'trim', explode( '|', $line ) );
			$list[ strtolower( $parts[0] ) ] = array( 'name' => $parts[0], 'purpose' => $parts[1] ?? '', 'location' => $parts[2] ?? '' );
		}
		if ( vul_get( 'privacy_processors_auto' ) ) {
			foreach ( VUL_Registry::cookies() as $c ) {
				if ( $c['provider'] && 'WordPress' !== $c['provider'] && ! isset( $list[ strtolower( $c['provider'] ) ] ) ) {
					$cats = vul_categories( true );
					$list[ strtolower( $c['provider'] ) ] = array( 'name' => $c['provider'], 'purpose' => ( $cats[ $c['category'] ]['label'] ?? $c['category'] ) . ' cookies', 'location' => '' );
				}
			}
		}
		return apply_filters( 'vul_document_processors', array_values( $list ) );
	}

	private static function processors_list() {
		$list = self::processors();
		if ( ! $list ) {
			return '<p>We do not currently share your personal data with any third-party service providers.</p>';
		}
		$out = '<ul class="vul-doc__list">';
		foreach ( $list as $p ) {
			$out .= '<li><strong>' . esc_html( $p['name'] ) . '</strong>';
			if ( $p['purpose'] ) {
				$out .= ': ' . esc_html( $p['purpose'] );
			}
			if ( $p['location'] ) {
				$out .= ' <span class="vul-muted">(' . esc_html( $p['location'] ) . ')</span>';
			}
			$out .= '</li>';
		}
		return $out . '</ul>';
	}

	private static function lines_to_list( $text ) {
		$lines = array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) $text ) ) );
		if ( ! $lines ) {
			return '';
		}
		return '<ul class="vul-doc__list"><li>' . implode( '</li><li>', array_map( 'esc_html', $lines ) ) . '</li></ul>';
	}

	/* ---------- Rendering ---------- */

	public static function meta( $type ) {
		$all = vul_get( 'docs_meta' );
		$m   = is_array( $all ) && isset( $all[ $type ] ) ? $all[ $type ] : array();
		return wp_parse_args( $m, array( 'version' => '1', 'updated' => '' ) );
	}

	public static function overrides( $type ) {
		$all = vul_get( 'docs_overrides' );
		return is_array( $all ) && isset( $all[ $type ] ) && is_array( $all[ $type ] ) ? $all[ $type ] : array();
	}

	/**
	 * Template sections for a type, before merge. [ key => [ heading, body ] ]
	 */
	public static function sections( $type ) {
		$file = VUL_DIR . 'templates/docs/' . $type . '.php';
		$sections = file_exists( $file ) ? include $file : array();
		return apply_filters( 'vul_document_sections', $sections, $type );
	}

	/**
	 * Resolve {if:flag}…{/if}, {ifnot:flag}…{/if}, then tokens.
	 */
	public static function compile( $text, $type ) {
		$flags = self::flags();
		// Nested conditionals: resolve innermost first, repeat until stable.
		$pattern = '/\{(if|ifnot):([a-z0-9_]+)\}((?:(?!\{if:|\{ifnot:).)*?)\{\/if\}/s';
		for ( $i = 0; $i < 6; $i++ ) {
			$new = preg_replace_callback( $pattern, function ( $m ) use ( $flags ) {
				$on = ! empty( $flags[ $m[2] ] );
				return ( 'if' === $m[1] ) === $on ? $m[3] : '';
			}, $text );
			if ( $new === $text ) {
				break;
			}
			$text = $new;
		}
		$text = strtr( $text, self::tokens( $type ) );
		$text = vul_merge( $text );
		// Tidy double spaces / empty paragraphs left by removed conditionals.
		$text = preg_replace( '/<p>\s*<\/p>/', '', $text );
		$text = preg_replace( '/[ \t]{2,}/', ' ', $text );
		return $text;
	}

	public static function render( $type ) {
		$types = self::types();
		if ( ! isset( $types[ $type ] ) ) {
			return '';
		}
		if ( 'cookies' === $type ) {
			return VUL_Policy::render();
		}
		$overrides = self::overrides( $type );
		$html      = '<div class="vul-doc vul-doc--' . esc_attr( $type ) . '">';
		$n         = 0;
		foreach ( self::sections( $type ) as $key => $sec ) {
			$body = ! empty( $overrides[ $key ] ) ? wpautop( $overrides[ $key ] ) : $sec['body'];
			$body = self::compile( $body, $type );
			if ( '' === trim( wp_strip_all_tags( $body ) ) ) {
				continue;
			}
			$html .= '<section class="vul-doc__section vul-doc__section--' . esc_attr( $key ) . '" id="' . esc_attr( $type . '-' . $key ) . '">';
			if ( ! empty( $sec['heading'] ) ) {
				$n++;
				$html .= '<h2>' . ( ! empty( $sec['numbered'] ) || ! isset( $sec['numbered'] ) ? $n . '. ' : '' ) . esc_html( self::compile( $sec['heading'], $type ) ) . '</h2>';
			}
			$html .= wp_kses_post( $body );
			$html .= '</section>';
		}
		$html .= '</div>';
		return apply_filters( 'vul_document_html', $html, $type );
	}

	/**
	 * Create (or return) the page for a document type, containing the block. Returns page ID.
	 */
	public static function ensure_page( $type ) {
		$types = self::types();
		if ( ! isset( $types[ $type ] ) ) {
			return 0;
		}
		if ( 'cookies' === $type ) {
			return VUL_Policy::ensure_page();
		}
		$key = $types[ $type ]['page_key'];
		$id  = (int) vul_get( $key );
		if ( $id && get_post( $id ) ) {
			return $id;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $types[ $type ]['title'],
			'post_name'    => sanitize_title( $types[ $type ]['title'] ),
			'post_content' => '<!-- wp:vu-legals/document {"type":"' . $type . '"} /-->',
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			$settings         = get_option( VUL_OPTION, array() );
			$settings[ $key ] = $id;
			update_option( VUL_OPTION, $settings );
			return $id;
		}
		return 0;
	}
}
