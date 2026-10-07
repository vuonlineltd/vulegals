<?php
/**
 * Generated Cookie Policy: shortcode + block, built live from the registry and settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Policy {

	public static function init() {
		add_shortcode( 'vu_cookie_policy', array( __CLASS__, 'shortcode' ) );
		add_shortcode( 'vu_legals', array( __CLASS__, 'shortcode_field' ) );
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	public static function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type( VUL_DIR . 'blocks/cookie-policy', array(
			'render_callback' => array( __CLASS__, 'render' ),
		) );
	}

	public static function shortcode( $atts ) {
		return self::render();
	}

	/**
	 * [vu_legals field="company_name"] — output a single merge value.
	 */
	public static function shortcode_field( $atts ) {
		$atts = shortcode_atts( array( 'field' => 'company' ), $atts, 'vu_legals' );
		$key  = '{' . preg_replace( '/[^a-z_]/', '', strtolower( $atts['field'] ) ) . '}';
		return esc_html( vul_merge( $key ) );
	}

	/**
	 * Section template (filterable). {tokens} are replaced via vul_merge().
	 */
	public static function sections() {
		$s = array(
			'intro' => array(
				'heading' => '',
				'body'    => '<p>This Cookie Policy explains how {company} ("we", "us") uses cookies and similar technologies on {site_url}. It should be read alongside our <a href="{privacy_url}">Privacy Policy</a>.</p>
<p>Last updated: {updated} (version {version}).</p>',
			),
			'what' => array(
				'heading' => 'What are cookies?',
				'body'    => '<p>Cookies are small text files placed on your device when you visit a website. They are widely used to make websites work, to make them work more efficiently, and to provide information to the site owner. Similar technologies such as local storage, pixels and tags do a comparable job and are covered by this policy.</p>
<p>Cookies set by us are "first-party" cookies. Cookies set by other organisations through our site (for example, an embedded video player or an analytics service) are "third-party" cookies.</p>',
			),
			'how' => array(
				'heading' => 'How we use cookies',
				'body'    => '<p>We group cookies into the categories below. Strictly necessary cookies are always active because the site cannot function without them. All other categories are off until you choose to switch them on.</p>
<p>You can change your choices at any time: {manage_link}.</p>',
			),
			'categories' => array(
				'heading' => 'The cookies we use',
				'body'    => '{category_tables}',
			),
			'settings' => array(
				'heading' => 'Change your cookie settings',
				'body'    => '<p>Use the switches below to change what you allow. Your choice is saved straight away and applies across the site.</p>{settings_panel}',
			),
			'control' => array(
				'heading' => 'Managing cookies in your browser',
				'body'    => '<p>As well as the controls on this site, most browsers let you refuse or delete cookies through their settings. Blocking all cookies may mean parts of this and other websites do not work properly. Guidance for common browsers is available from <a href="https://www.aboutcookies.org.uk" rel="noopener" target="_blank">aboutcookies.org.uk</a>.</p>',
			),
			'contact' => array(
				'heading' => 'Contact',
				'body'    => '<p>Questions about our use of cookies can be sent to <a href="mailto:{contact_email}">{contact_email}</a>{address_line}.</p>',
			),
		);
		return apply_filters( 'vul_policy_sections', $s );
	}

	public static function render() {
		$cats  = vul_categories();
		$html  = '<div class="vul-policy">';
		$extra = array(
			'{manage_link}'     => vul_preferences_link(),
			'{category_tables}' => self::category_tables( $cats ),
			'{address_line}'    => vul_get( 'company_address' ) ? ' or by post to ' . esc_html( vul_get( 'company_address' ) ) : '',
			'{settings_panel}'  => vul_get( 'policy_inline_panel' ) ? VUL_Frontend::inline_panel() : '',
		);

		foreach ( self::sections() as $key => $sec ) {
			if ( 'settings' === $key && ! vul_get( 'policy_inline_panel' ) ) {
				continue;
			}
			$html .= '<section class="vul-policy__section vul-policy__section--' . esc_attr( $key ) . '">';
			if ( ! empty( $sec['heading'] ) ) {
				$html .= '<h2>' . esc_html( vul_merge( $sec['heading'] ) ) . '</h2>';
			}
			$html .= strtr( vul_merge( $sec['body'] ), $extra );
			$html .= '</section>';
		}
		$html .= '</div>';
		return apply_filters( 'vul_policy_html', $html );
	}

	private static function category_tables( $cats ) {
		$out = '';
		foreach ( $cats as $slug => $cat ) {
			$cookies = VUL_Registry::cookies( $slug );
			$out    .= '<h3>' . esc_html( $cat['label'] ) . '</h3>';
			$out    .= '<p>' . esc_html( $cat['description'] ) . '</p>';
			if ( ! $cookies ) {
				$out .= '<p><em>We do not currently use any cookies in this category.</em></p>';
				continue;
			}
			$out .= '<table class="vul-policy__table"><thead><tr><th>Name</th><th>Provider</th><th>Purpose</th><th>Duration</th><th>Type</th></tr></thead><tbody>';
			$types = array( 'http' => 'HTTP cookie', 'local' => 'Local storage', 'session' => 'Session storage', 'pixel' => 'Pixel' );
			foreach ( $cookies as $c ) {
				$out .= sprintf(
					'<tr><td data-label="Name"><code>%s</code>%s</td><td data-label="Provider">%s</td><td data-label="Purpose">%s</td><td data-label="Duration">%s</td><td data-label="Type">%s</td></tr>',
					esc_html( $c['name'] ),
					$c['domain'] ? '<br><small>' . esc_html( $c['domain'] ) . '</small>' : '',
					esc_html( $c['provider'] ?: '—' ),
					esc_html( $c['purpose'] ),
					esc_html( $c['duration'] ?: '—' ),
					esc_html( $types[ $c['type'] ] ?? $c['type'] )
				);
			}
			$out .= '</tbody></table>';
		}
		return $out;
	}

	/**
	 * Create (or return existing) Cookie Policy page containing the block. Returns page ID.
	 */
	public static function ensure_page() {
		$id = (int) vul_get( 'cookie_page' );
		if ( $id && get_post( $id ) ) {
			return $id;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Cookie Policy',
			'post_name'    => 'cookie-policy',
			'post_content' => '<!-- wp:vu-legals/cookie-policy /-->',
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			$settings                = get_option( VUL_OPTION, array() );
			$settings['cookie_page'] = $id;
			update_option( VUL_OPTION, $settings );
			return $id;
		}
		return 0;
	}
}
