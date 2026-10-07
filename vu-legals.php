<?php
/**
 * Plugin Name:       Vu Legals
 * Plugin URI:        https://vudigital.co.uk
 * Description:       Cookie consent, cookie registry, script gating, Google Consent Mode v2 and a generated Cookie Policy. Styled from your own CSS variables. No licence, no branding.
 * Version:           1.1.2
 * Requires at least: 6.3
 * Requires PHP:      8.0
 * Author:            Vu Digital
 * Author URI:        https://vudigital.co.uk
 * License:           GPL-2.0-or-later
 * Text Domain:       vu-legals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VUL_VERSION', '1.1.2' );
define( 'VUL_FILE', __FILE__ );
define( 'VUL_DIR', plugin_dir_path( __FILE__ ) );
define( 'VUL_URL', plugin_dir_url( __FILE__ ) );
define( 'VUL_OPTION', 'vu_legals_settings' );
define( 'VUL_COOKIE', 'vu_consent' );

require_once VUL_DIR . 'includes/helpers.php';
require_once VUL_DIR . 'includes/class-settings.php';
require_once VUL_DIR . 'includes/class-registry.php';
require_once VUL_DIR . 'includes/class-providers.php';
require_once VUL_DIR . 'includes/class-geo.php';
require_once VUL_DIR . 'includes/class-consent.php';
require_once VUL_DIR . 'includes/class-frontend.php';
require_once VUL_DIR . 'includes/class-policy.php';
require_once VUL_DIR . 'includes/class-log.php';
require_once VUL_DIR . 'includes/class-gravity-forms.php';
require_once VUL_DIR . 'includes/class-documents.php';
require_once VUL_DIR . 'includes/class-compat.php';
require_once VUL_DIR . 'includes/class-admin.php';

register_activation_hook( __FILE__, array( 'VUL_Log', 'install' ) );
register_activation_hook( __FILE__, array( 'VUL_Settings', 'install_defaults' ) );
register_activation_hook( __FILE__, 'flush_rewrite_rules' );

add_action( 'plugins_loaded', function () {
	VUL_Settings::init();
	VUL_Registry::init();
	VUL_Geo::init();
	VUL_Consent::init();
	VUL_Frontend::init();
	VUL_Policy::init();
	VUL_Log::init();
	VUL_Gravity_Forms::init();
	VUL_Compat::init();
	VUL_Documents::init();
	if ( is_admin() ) {
		VUL_Admin::init();
	}
} );
