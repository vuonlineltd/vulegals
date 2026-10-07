<?php
/**
 * Admin: menu, tabbed settings page, scan tool, consent log screen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Admin {

	const SLUG = 'vu-legals';

	public static function tabs() {
		return array(
			'general'      => 'General',
			'banner'       => 'Banner',
			'style'        => 'Style',
			'categories'   => 'Categories',
			'scripts'      => 'Script gating',
			'integrations' => 'Integrations',
			'geo'          => 'Geo',
			'documents'    => 'Documents',
		);
	}

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_post_vul_add_preset', array( __CLASS__, 'action_add_preset' ) );
		add_action( 'admin_post_vul_create_page', array( __CLASS__, 'action_create_page' ) );
		add_action( 'admin_post_vul_create_doc_page', array( __CLASS__, 'action_create_doc_page' ) );
		add_action( 'admin_post_vul_bump_version', array( __CLASS__, 'action_bump_version' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( VUL_FILE ), function ( $links ) {
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ) . '">Settings</a>' );
			return $links;
		} );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
	}

	public static function menu() {
		add_menu_page( 'Vu Legals', 'Vu Legals', 'manage_options', self::SLUG, array( __CLASS__, 'page_settings' ), 'dashicons-shield-alt', 81 );
		add_submenu_page( self::SLUG, 'Settings', 'Settings', 'manage_options', self::SLUG, array( __CLASS__, 'page_settings' ) );
		add_submenu_page( self::SLUG, 'Scan & presets', 'Scan & presets', 'manage_options', self::SLUG . '-scan', array( __CLASS__, 'page_scan' ) );
		add_submenu_page( self::SLUG, 'Consent log', 'Consent log', 'manage_options', self::SLUG . '-log', array( __CLASS__, 'page_log' ) );
	}

	public static function assets( $hook ) {
		if ( false === strpos( $hook, self::SLUG ) && ! in_array( get_current_screen()->post_type ?? '', array( VUL_Registry::COOKIE, VUL_Registry::SCRIPT ), true ) ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'vu-legals-admin', VUL_URL . 'admin/css/admin.css', array(), (string) filemtime( VUL_DIR . 'admin/css/admin.css' ) );
		wp_enqueue_script( 'vu-legals-admin', VUL_URL . 'admin/js/admin.js', array( 'jquery', 'wp-color-picker' ), (string) filemtime( VUL_DIR . 'admin/js/admin.js' ), true );
	}

	public static function notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( isset( $_GET['vul_msg'] ) && $screen && false !== strpos( $screen->id, self::SLUG ) ) {
			$msg = sanitize_text_field( wp_unslash( $_GET['vul_msg'] ) );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
		}
		if ( ! vul_get( 'cookie_page' ) && $screen && false !== strpos( $screen->id, self::SLUG ) ) {
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=vul_create_page' ), 'vul_create_page' );
			echo '<div class="notice notice-info"><p>No Cookie Policy page is set. <a href="' . esc_url( $url ) . '">Create one now</a> (adds a page containing the generated policy block) or pick an existing page under General.</p></div>';
		}
	}

	/* ---------- Settings page ---------- */

	public static function page_settings() {
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
		$tabs = self::tabs();
		if ( ! isset( $tabs[ $tab ] ) ) {
			$tab = 'general';
		}
		$s = vul_get();
		?>
		<div class="wrap vul-admin">
			<h1>Vu Legals <span class="vul-version">v<?php echo esc_html( VUL_VERSION ); ?></span></h1>
			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $k => $label ) : ?>
					<a class="nav-tab <?php echo $k === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SLUG . '&tab=' . $k ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>
			<form method="post" action="options.php">
				<?php settings_fields( 'vu_legals' ); ?>
				<input type="hidden" name="<?php echo esc_attr( VUL_OPTION ); ?>[_tab]" value="<?php echo esc_attr( $tab ); ?>">
				<?php include VUL_DIR . 'admin/views/tab-' . $tab . '.php'; ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/** Field name helper. */
	public static function n( $key ) {
		return VUL_OPTION . '[' . $key . ']';
	}

	/* ---------- Scan page ---------- */

	public static function page_scan() {
		$result = null;
		if ( isset( $_POST['vul_scan'] ) && check_admin_referer( 'vul_scan' ) ) {
			$result = VUL_Providers::scan();
		}
		$providers  = VUL_Providers::all();
		$registered = array_map( fn( $c ) => $c['provider'], VUL_Registry::cookies() );
		include VUL_DIR . 'admin/views/scan.php';
	}

	public static function action_add_preset() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'vul_add_preset' );
		$slug  = isset( $_GET['provider'] ) ? sanitize_key( $_GET['provider'] ) : '';
		$slugs = 'all' === $slug ? ( isset( $_GET['providers'] ) ? array_map( 'sanitize_key', explode( ',', wp_unslash( $_GET['providers'] ) ) ) : array() ) : array( $slug );
		$n     = 0;
		foreach ( $slugs as $p ) {
			$n += VUL_Providers::add_preset( $p );
		}
		wp_safe_redirect( add_query_arg( 'vul_msg', rawurlencode( "$n cookies added to the registry." ), admin_url( 'admin.php?page=' . self::SLUG . '-scan' ) ) );
		exit;
	}

	public static function action_create_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'vul_create_page' );
		$id = VUL_Policy::ensure_page();
		wp_safe_redirect( add_query_arg( 'vul_msg', rawurlencode( $id ? 'Cookie Policy page created.' : 'Could not create the page.' ), admin_url( 'admin.php?page=' . self::SLUG ) ) );
		exit;
	}

	public static function action_create_doc_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'vul_create_doc_page' );
		$type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '';
		$id   = VUL_Documents::ensure_page( $type );
		wp_safe_redirect( add_query_arg( 'vul_msg', rawurlencode( $id ? 'Page created: ' . get_the_title( $id ) . '.' : 'Could not create the page.' ), admin_url( 'admin.php?page=' . self::SLUG . '&tab=documents' ) ) );
		exit;
	}

	public static function action_bump_version() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'vul_bump_version' );
		$s                   = get_option( VUL_OPTION, array() );
		$s['policy_version'] = (string) ( (int) ( $s['policy_version'] ?? 1 ) + 1 );
		$s['policy_updated'] = wp_date( 'Y-m-d' );
		update_option( VUL_OPTION, $s );
		wp_safe_redirect( add_query_arg( 'vul_msg', rawurlencode( 'Policy version is now ' . $s['policy_version'] . '. Every visitor will be asked again.' ), admin_url( 'admin.php?page=' . self::SLUG ) ) );
		exit;
	}

	/* ---------- Log page ---------- */

	public static function page_log() {
		$rows  = VUL_Log::recent( 100 );
		$count = VUL_Log::count();
		$stats = VUL_Log::stats();
		include VUL_DIR . 'admin/views/log.php';
	}
}
