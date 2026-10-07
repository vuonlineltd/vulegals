<?php
/**
 * Consent state, script gating, embed blocking, Consent Mode v2 bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Consent {

	private static $state_cache = false;

	public static function init() {
		// Consent Mode default + GTM as early as possible in <head>.
		add_action( 'wp_head', array( __CLASS__, 'head_bootstrap' ), 0 );
		// Gated custom scripts.
		add_action( 'wp_head', array( __CLASS__, 'print_scripts_head' ), 20 );
		add_action( 'wp_footer', array( __CLASS__, 'print_scripts_footer' ), 5 );
		// Enqueued handles + provider matching.
		add_filter( 'script_loader_tag', array( __CLASS__, 'filter_script_tag' ), 20, 3 );
		// Embeds in content.
		add_filter( 'the_content', array( __CLASS__, 'filter_embeds' ), 99 );
		add_filter( 'embed_oembed_html', array( __CLASS__, 'filter_embeds' ), 99 );
		add_filter( 'widget_block_content', array( __CLASS__, 'filter_embeds' ), 99 );
		add_filter( 'widget_text_content', array( __CLASS__, 'filter_embeds' ), 99 );
		// Whole-page safety net for scripts injected outside WP hooks (page builders, snippet plugins).
		add_action( 'template_redirect', array( __CLASS__, 'maybe_buffer' ), 1 );
	}

	/* ---------- State ---------- */

	/**
	 * @return array|null  { v, t, id, cats: [slug => bool] }
	 */
	public static function state() {
		if ( false !== self::$state_cache ) {
			return self::$state_cache;
		}
		self::$state_cache = null;
		if ( empty( $_COOKIE[ VUL_COOKIE ] ) ) {
			return null;
		}
		$raw  = rawurldecode( wp_unslash( $_COOKIE[ VUL_COOKIE ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) || empty( $data['c'] ) || ! is_array( $data['c'] ) ) {
			return null;
		}
		if ( (string) ( $data['v'] ?? '' ) !== (string) vul_get( 'policy_version' ) ) {
			return null; // Policy changed: treat as no consent, banner re-shows.
		}
		$cats = array( 'necessary' => true );
		foreach ( vul_categories() as $slug => $cat ) {
			if ( 'necessary' !== $slug ) {
				$cats[ $slug ] = ! empty( $data['c'][ $slug ] );
			}
		}
		self::$state_cache = array(
			'v'    => (string) $data['v'],
			't'    => (int) ( $data['t'] ?? 0 ),
			'id'   => preg_replace( '/[^a-f0-9\-]/i', '', (string) ( $data['id'] ?? '' ) ),
			'cats' => $cats,
		);
		return self::$state_cache;
	}

	/**
	 * Gating is never bypassed server-side: the HTML must be identical for every visitor so
	 * full-page caches can't leak an ungated page. Implied-consent regions are handled in JS,
	 * which activates everything on load.
	 */
	public static function bypass() {
		return false;
	}

	/* ---------- Consent Mode + GTM ---------- */

	public static function head_bootstrap() {
		if ( is_admin() || is_feed() ) {
			return;
		}
		// Defaults are ALWAYS denied so the HTML is identical for every visitor and safe to
		// full-page cache. Stored consent is replayed as gtag('consent','update') by the JS
		// before GTM's wait_for_update window closes.
		echo "\n<!-- Vu Legals -->\n";
		if ( vul_get( 'consent_mode' ) ) {
			$defaults = array(
				'ad_storage'              => 'denied',
				'ad_user_data'            => 'denied',
				'ad_personalization'      => 'denied',
				'analytics_storage'       => 'denied',
				'functionality_storage'   => 'denied',
				'personalization_storage' => 'denied',
				'security_storage'        => 'granted',
				'wait_for_update'         => (int) vul_get( 'consent_mode_wait' ),
			);
			$defaults = apply_filters( 'vul_consent_mode_defaults', $defaults );
			?>
<script data-vul="consent-mode">
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',<?php echo wp_json_encode( $defaults ); ?>);
<?php if ( vul_get( 'url_passthrough' ) ) : ?>
gtag('set','url_passthrough',true);
<?php endif; ?>
gtag('set','ads_data_redaction',true);
</script>
			<?php
		}

		$gtm = vul_get( 'gtm_id' );
		if ( vul_get( 'gtm_load' ) && $gtm ) {
			?>
<script data-vul="gtm">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm ); ?>');</script>
			<?php
		}
	}

	/* ---------- Gating markup ---------- */

	/**
	 * Convert <script> tags in $html into inert, consent-gated tags.
	 */
	public static function gate_html( $html, $category ) {
		if ( 'necessary' === $category || self::bypass() ) {
			return $html;
		}
		return preg_replace_callback(
			'/<script\b([^>]*)>/i',
			function ( $m ) use ( $category ) {
				$attrs = $m[1];
				if ( false !== stripos( $attrs, 'data-vul-consent' ) ) {
					return $m[0];
				}
				// Keep original type (module etc.) for re-injection.
				if ( preg_match( '/\stype=["\']([^"\']*)["\']/i', $attrs, $t ) ) {
					$attrs = preg_replace( '/\stype=["\'][^"\']*["\']/i', '', $attrs );
					if ( 'text/plain' !== strtolower( $t[1] ) ) {
						$attrs .= ' data-vul-type="' . esc_attr( $t[1] ) . '"';
					}
				}
				$attrs = preg_replace( '/\ssrc=(["\'])/i', ' data-vul-src=$1', $attrs );
				return '<script type="text/plain" data-vul-consent="' . esc_attr( $category ) . '"' . $attrs . '>';
			},
			$html
		);
	}

	/**
	 * Wrap a matching iframe in a placeholder that only loads on consent.
	 */
	public static function gate_iframe( $iframe_html, $category ) {
		if ( self::bypass() ) {
			return $iframe_html;
		}
		$cats  = vul_categories();
		$label = $cats[ $category ]['label'] ?? $category;
		$inert = preg_replace( '/\ssrc=(["\'])/i', ' data-vul-src=$1', $iframe_html, 1 );
		$text  = str_replace( '{category}', strtolower( $label ), vul_get( 'text_embed_blocked' ) );
		return sprintf(
			'<div class="vul-embed" data-vul-embed="%1$s">%2$s<div class="vul-embed__placeholder"><p>%3$s</p><button type="button" class="vul-btn vul-btn--primary" data-vul-accept="%1$s">%4$s</button> <a href="#" data-vul-open>%5$s</a></div></div>',
			esc_attr( $category ),
			$inert,
			esc_html( $text ),
			esc_html( vul_get( 'text_embed_button' ) ),
			esc_html( vul_get( 'text_manage' ) )
		);
	}

	/**
	 * Handle map from settings: [ handle => category ].
	 */
	private static function handle_map() {
		static $map = null;
		if ( null === $map ) {
			$map = array();
			foreach ( preg_split( '/\r?\n/', (string) vul_get( 'handle_map' ) ) as $line ) {
				$line = trim( $line );
				if ( '' === $line || '#' === $line[0] || false === strpos( $line, ':' ) ) {
					continue;
				}
				[ $h, $c ] = array_map( 'trim', explode( ':', $line, 2 ) );
				if ( $h && $c && 'necessary' !== $c ) {
					$map[ $h ] = $c;
				}
			}
			$map = apply_filters( 'vul_handle_map', $map );
		}
		return $map;
	}

	/**
	 * Custom URL patterns from settings: [ pattern => category ].
	 */
	private static function pattern_map() {
		static $map = null;
		if ( null === $map ) {
			$map = array();
			foreach ( preg_split( '/\r?\n/', (string) vul_get( 'block_patterns' ) ) as $line ) {
				$line = trim( $line );
				if ( '' === $line || '#' === $line[0] || false === strpos( $line, ':' ) ) {
					continue;
				}
				$pos = strrpos( $line, ':' );
				$p   = trim( substr( $line, 0, $pos ) );
				$c   = trim( substr( $line, $pos + 1 ) );
				if ( $p && in_array( $c, array( 'functional', 'analytics', 'marketing' ), true ) ) {
					$map[ $p ] = $c;
				}
			}
		}
		return $map;
	}

	/**
	 * Category a URL or inline code should be gated to, or null.
	 */
	public static function category_for( $haystack ) {
		foreach ( self::pattern_map() as $pattern => $cat ) {
			if ( false !== stripos( $haystack, $pattern ) ) {
				return $cat;
			}
		}
		$cat = VUL_Providers::category_for_url( $haystack );
		return apply_filters( 'vul_category_for', $cat, $haystack );
	}

	public static function filter_script_tag( $tag, $handle, $src ) {
		if ( is_admin() || self::bypass() ) {
			return $tag;
		}
		$map = self::handle_map();
		$cat = $map[ $handle ] ?? ( vul_get( 'auto_block' ) ? self::category_for( (string) $src ) : null );
		return $cat ? self::gate_html( $tag, $cat ) : $tag;
	}

	public static function filter_embeds( $content ) {
		if ( is_admin() || ! vul_get( 'block_embeds' ) || false === stripos( $content, '<iframe' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'/<iframe\b[^>]*\ssrc=["\']([^"\']+)["\'][^>]*>.*?<\/iframe>/is',
			function ( $m ) {
				$cat = self::category_for( $m[1] );
				if ( ! $cat ) {
					return $m[0];
				}
				return self::gate_iframe( $m[0], $cat );
			},
			$content
		);
	}

	/* ---------- Registered scripts ---------- */

	public static function print_scripts_head() {
		self::print_scripts( 'head' );
	}

	public static function print_scripts_footer() {
		self::print_scripts( 'footer' );
	}

	private static function print_scripts( $position ) {
		if ( is_admin() ) {
			return;
		}
		foreach ( VUL_Registry::scripts() as $s ) {
			if ( $s['position'] !== $position || '' === trim( (string) $s['code'] ) ) {
				continue;
			}
			echo "\n<!-- Vu Legals: " . esc_html( $s['name'] ) . " -->\n";
			echo self::gate_html( $s['code'], $s['category'] ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- admin-authored snippet, gated.
		}
	}

	/* ---------- Output buffer safety net ---------- */

	public static function maybe_buffer() {
		if ( is_admin() || is_feed() || ! vul_get( 'auto_block' ) || self::bypass() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( isset( $_GET['vul_scan'] ) ) {
			return;
		}
		ob_start( array( __CLASS__, 'buffer_callback' ) );
	}

	public static function buffer_callback( $html ) {
		if ( false === stripos( $html, '<script' ) ) {
			return $html;
		}
		// External scripts by src.
		$html = preg_replace_callback(
			'/<script\b(?![^>]*data-vul)[^>]*\ssrc=["\']([^"\']+)["\'][^>]*><\/script>/i',
			function ( $m ) {
				$cat = self::category_for( $m[1] );
				return $cat ? self::gate_html( $m[0], $cat ) : $m[0];
			},
			$html
		);
		// Inline snippets that match a known provider (gtag('config', ...), fbq(...) etc.).
		$html = preg_replace_callback(
			'/<script\b(?![^>]*data-vul)(?![^>]*\ssrc=)[^>]*>(.*?)<\/script>/is',
			function ( $m ) {
				if ( '' === trim( $m[1] ) ) {
					return $m[0];
				}
				$cat = self::category_for( $m[1] );
				return $cat ? self::gate_html( $m[0], $cat ) : $m[0];
			},
			$html
		);
		return $html;
	}
}
