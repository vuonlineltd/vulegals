<?php
/**
 * Cookie + script registry. Two private post types with meta boxes, no ACF required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Registry {

	const COOKIE = 'vul_cookie';
	const SCRIPT = 'vul_script';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_types' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_action( 'save_post_' . self::COOKIE, array( __CLASS__, 'save_cookie' ), 10, 2 );
		add_action( 'save_post_' . self::SCRIPT, array( __CLASS__, 'save_script' ), 10, 2 );
		add_filter( 'manage_' . self::COOKIE . '_posts_columns', array( __CLASS__, 'cookie_columns' ) );
		add_action( 'manage_' . self::COOKIE . '_posts_custom_column', array( __CLASS__, 'cookie_column' ), 10, 2 );
		add_filter( 'manage_' . self::SCRIPT . '_posts_columns', array( __CLASS__, 'script_columns' ) );
		add_action( 'manage_' . self::SCRIPT . '_posts_custom_column', array( __CLASS__, 'script_column' ), 10, 2 );
		add_action( 'save_post', array( __CLASS__, 'flush_cache' ) );
		add_action( 'deleted_post', array( __CLASS__, 'flush_cache' ) );
	}

	public static function register_types() {
		$common = array(
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'vu-legals',
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'supports'            => array( 'title' ),
		);

		register_post_type( self::COOKIE, $common + array(
			'labels' => array(
				'name'               => 'Cookies',
				'singular_name'      => 'Cookie',
				'add_new_item'       => 'Add cookie',
				'edit_item'          => 'Edit cookie',
				'not_found'          => 'No cookies registered yet. Use the presets under Vu Legals → Scan to add common ones.',
				'menu_name'          => 'Cookies',
			),
		) );

		register_post_type( self::SCRIPT, $common + array(
			'labels' => array(
				'name'          => 'Scripts',
				'singular_name' => 'Script',
				'add_new_item'  => 'Add script',
				'edit_item'     => 'Edit script',
				'not_found'     => 'No gated scripts yet. Paste tracking snippets here and they will only run once the visitor consents.',
				'menu_name'     => 'Scripts',
			),
		) );
	}

	/* ---------- Data access ---------- */

	public static function flush_cache() {
		delete_transient( 'vul_cookies' );
		delete_transient( 'vul_scripts' );
	}

	/**
	 * @return array[] each: id, name, category, provider, purpose, duration, type, domain
	 */
	public static function cookies( $category = null ) {
		$all = get_transient( 'vul_cookies' );
		if ( false === $all ) {
			$all   = array();
			$posts = get_posts( array(
				'post_type'      => self::COOKIE,
				'post_status'    => 'publish',
				'numberposts'    => 200,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'no_found_rows'  => true,
			) );
			foreach ( $posts as $p ) {
				$all[] = array(
					'id'       => $p->ID,
					'name'     => $p->post_title,
					'category' => get_post_meta( $p->ID, '_vul_category', true ) ?: 'necessary',
					'provider' => get_post_meta( $p->ID, '_vul_provider', true ),
					'purpose'  => get_post_meta( $p->ID, '_vul_purpose', true ),
					'duration' => get_post_meta( $p->ID, '_vul_duration', true ),
					'type'     => get_post_meta( $p->ID, '_vul_type', true ) ?: 'http',
					'domain'   => get_post_meta( $p->ID, '_vul_domain', true ),
				);
			}
			set_transient( 'vul_cookies', $all, DAY_IN_SECONDS );
		}
		if ( $category ) {
			$all = array_values( array_filter( $all, fn( $c ) => $c['category'] === $category ) );
		}
		return apply_filters( 'vul_cookies', $all, $category );
	}

	/**
	 * @return array[] each: id, name, category, position, code
	 */
	public static function scripts() {
		$all = get_transient( 'vul_scripts' );
		if ( false === $all ) {
			$all   = array();
			$posts = get_posts( array(
				'post_type'     => self::SCRIPT,
				'post_status'   => 'publish',
				'numberposts'   => 50,
				'orderby'       => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'no_found_rows' => true,
			) );
			foreach ( $posts as $p ) {
				$all[] = array(
					'id'       => $p->ID,
					'name'     => $p->post_title,
					'category' => get_post_meta( $p->ID, '_vul_category', true ) ?: 'analytics',
					'position' => get_post_meta( $p->ID, '_vul_position', true ) ?: 'head',
					'code'     => get_post_meta( $p->ID, '_vul_code', true ),
				);
			}
			set_transient( 'vul_scripts', $all, DAY_IN_SECONDS );
		}
		return apply_filters( 'vul_scripts', $all );
	}

	/**
	 * Insert a cookie if a cookie of that name + provider doesn't already exist. Returns post ID or 0.
	 */
	public static function add_cookie( array $c ) {
		$existing = get_posts( array(
			'post_type'     => self::COOKIE,
			'post_status'   => 'any',
			'title'         => $c['name'],
			'numberposts'   => 1,
			'no_found_rows' => true,
			'fields'        => 'ids',
		) );
		if ( $existing ) {
			return 0;
		}
		$id = wp_insert_post( wp_slash( array(
			'post_type'   => self::COOKIE,
			'post_status' => 'publish',
			'post_title'  => $c['name'],
		) ) );
		if ( ! $id || is_wp_error( $id ) ) {
			return 0;
		}
		foreach ( array( 'category', 'provider', 'purpose', 'duration', 'type', 'domain' ) as $k ) {
			update_post_meta( $id, '_vul_' . $k, $c[ $k ] ?? '' );
		}
		self::flush_cache();
		return $id;
	}

	/* ---------- Admin: meta boxes ---------- */

	public static function meta_boxes() {
		add_meta_box( 'vul_cookie_meta', 'Cookie details', array( __CLASS__, 'cookie_box' ), self::COOKIE, 'normal', 'high' );
		add_meta_box( 'vul_script_meta', 'Script', array( __CLASS__, 'script_box' ), self::SCRIPT, 'normal', 'high' );
	}

	private static function category_select( $name, $value, $exclude_necessary = false ) {
		echo '<select name="' . esc_attr( $name ) . '">';
		foreach ( vul_categories( true ) as $slug => $cat ) {
			if ( $exclude_necessary && 'necessary' === $slug ) {
				continue;
			}
			printf( '<option value="%s"%s>%s</option>', esc_attr( $slug ), selected( $value, $slug, false ), esc_html( $cat['label'] ) );
		}
		echo '</select>';
	}

	public static function cookie_box( $post ) {
		wp_nonce_field( 'vul_cookie_save', 'vul_cookie_nonce' );
		$m = fn( $k ) => get_post_meta( $post->ID, '_vul_' . $k, true );
		?>
		<p class="description">The post title above is the cookie name as it appears in the browser (e.g. <code>_ga</code>).</p>
		<table class="form-table">
			<tr><th><label>Category</label></th><td><?php self::category_select( 'vul[category]', $m( 'category' ) ?: 'necessary' ); ?></td></tr>
			<tr><th><label for="vul_provider">Provider</label></th><td><input type="text" class="regular-text" id="vul_provider" name="vul[provider]" value="<?php echo esc_attr( $m( 'provider' ) ); ?>" placeholder="e.g. Google Analytics"></td></tr>
			<tr><th><label for="vul_purpose">Purpose</label></th><td><textarea class="large-text" rows="3" id="vul_purpose" name="vul[purpose]"><?php echo esc_textarea( $m( 'purpose' ) ); ?></textarea></td></tr>
			<tr><th><label for="vul_duration">Duration</label></th><td><input type="text" class="regular-text" id="vul_duration" name="vul[duration]" value="<?php echo esc_attr( $m( 'duration' ) ); ?>" placeholder="e.g. 2 years, Session"></td></tr>
			<tr><th><label>Type</label></th><td>
				<select name="vul[type]">
					<?php foreach ( array( 'http' => 'HTTP cookie', 'local' => 'Local storage', 'session' => 'Session storage', 'pixel' => 'Pixel / beacon' ) as $k => $l ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $m( 'type' ) ?: 'http', $k ); ?>><?php echo esc_html( $l ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><label for="vul_domain">Domain</label></th><td><input type="text" class="regular-text" id="vul_domain" name="vul[domain]" value="<?php echo esc_attr( $m( 'domain' ) ); ?>" placeholder="e.g. .google.com (optional)"></td></tr>
		</table>
		<?php
	}

	public static function save_cookie( $post_id, $post ) {
		if ( ! isset( $_POST['vul_cookie_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vul_cookie_nonce'] ), 'vul_cookie_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		$in   = isset( $_POST['vul'] ) && is_array( $_POST['vul'] ) ? wp_unslash( $_POST['vul'] ) : array();
		$cats = array_keys( vul_categories( true ) );
		update_post_meta( $post_id, '_vul_category', in_array( $in['category'] ?? '', $cats, true ) ? $in['category'] : 'necessary' );
		update_post_meta( $post_id, '_vul_provider', sanitize_text_field( $in['provider'] ?? '' ) );
		update_post_meta( $post_id, '_vul_purpose', sanitize_textarea_field( $in['purpose'] ?? '' ) );
		update_post_meta( $post_id, '_vul_duration', sanitize_text_field( $in['duration'] ?? '' ) );
		update_post_meta( $post_id, '_vul_type', in_array( $in['type'] ?? '', array( 'http', 'local', 'session', 'pixel' ), true ) ? $in['type'] : 'http' );
		update_post_meta( $post_id, '_vul_domain', sanitize_text_field( $in['domain'] ?? '' ) );
		self::flush_cache();
	}

	public static function script_box( $post ) {
		wp_nonce_field( 'vul_script_save', 'vul_script_nonce' );
		$m = fn( $k ) => get_post_meta( $post->ID, '_vul_' . $k, true );
		?>
		<p class="description">Paste the full snippet including <code>&lt;script&gt;</code> tags. It is held back and only injected once the visitor consents to the chosen category. If you use Google Tag Manager with Consent Mode, you usually don't need this: load GTM from the Integrations tab instead and let it gate tags.</p>
		<table class="form-table">
			<tr><th><label>Category</label></th><td><?php self::category_select( 'vul[category]', $m( 'category' ) ?: 'analytics', true ); ?></td></tr>
			<tr><th><label>Position</label></th><td>
				<select name="vul[position]">
					<option value="head" <?php selected( $m( 'position' ) ?: 'head', 'head' ); ?>>Head</option>
					<option value="footer" <?php selected( $m( 'position' ), 'footer' ); ?>>Footer</option>
				</select>
			</td></tr>
			<tr><th><label for="vul_code">Code</label></th><td>
				<?php if ( current_user_can( 'unfiltered_html' ) ) : ?>
					<textarea class="large-text code" rows="12" id="vul_code" name="vul[code]" spellcheck="false"><?php echo esc_textarea( $m( 'code' ) ); ?></textarea>
				<?php else : ?>
					<pre><?php echo esc_html( $m( 'code' ) ); ?></pre>
					<p class="description">Only administrators can edit script code.</p>
				<?php endif; ?>
			</td></tr>
		</table>
		<?php
	}

	public static function save_script( $post_id, $post ) {
		if ( ! isset( $_POST['vul_script_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['vul_script_nonce'] ), 'vul_script_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		$in   = isset( $_POST['vul'] ) && is_array( $_POST['vul'] ) ? wp_unslash( $_POST['vul'] ) : array();
		$cats = array_keys( vul_categories( true ) );
		update_post_meta( $post_id, '_vul_category', in_array( $in['category'] ?? '', $cats, true ) && 'necessary' !== $in['category'] ? $in['category'] : 'analytics' );
		update_post_meta( $post_id, '_vul_position', 'footer' === ( $in['position'] ?? '' ) ? 'footer' : 'head' );
		if ( current_user_can( 'unfiltered_html' ) && isset( $in['code'] ) ) {
			update_post_meta( $post_id, '_vul_code', (string) $in['code'] );
		}
		self::flush_cache();
	}

	/* ---------- Admin: list columns ---------- */

	public static function cookie_columns( $cols ) {
		return array(
			'cb'       => $cols['cb'],
			'title'    => 'Cookie',
			'category' => 'Category',
			'provider' => 'Provider',
			'duration' => 'Duration',
			'type'     => 'Type',
		);
	}

	public static function cookie_column( $col, $id ) {
		$cats = vul_categories( true );
		switch ( $col ) {
			case 'category':
				$c = get_post_meta( $id, '_vul_category', true );
				echo esc_html( $cats[ $c ]['label'] ?? $c );
				break;
			case 'provider':
			case 'duration':
				echo esc_html( get_post_meta( $id, '_vul_' . $col, true ) );
				break;
			case 'type':
				echo esc_html( ucfirst( get_post_meta( $id, '_vul_type', true ) ?: 'http' ) );
				break;
		}
	}

	public static function script_columns( $cols ) {
		return array(
			'cb'       => $cols['cb'],
			'title'    => 'Script',
			'category' => 'Category',
			'position' => 'Position',
		);
	}

	public static function script_column( $col, $id ) {
		$cats = vul_categories( true );
		if ( 'category' === $col ) {
			$c = get_post_meta( $id, '_vul_category', true );
			echo esc_html( $cats[ $c ]['label'] ?? $c );
		} elseif ( 'position' === $col ) {
			echo esc_html( ucfirst( get_post_meta( $id, '_vul_position', true ) ?: 'head' ) );
		}
	}
}
