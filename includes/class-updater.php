<?php
/**
 * Updates the plugin from GitHub releases.
 *
 * Define VUL_GITHUB_TOKEN in wp-config.php if the repository is private.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUL_Updater {

	const REPO      = 'vuonlineltd/vulegals';
	const ASSET     = 'vu-legals.zip';
	const TRANSIENT = 'vul_github_release';

	public static function init() {
		add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'check' ) );
		add_filter( 'plugins_api', array( __CLASS__, 'info' ), 10, 3 );
		add_filter( 'http_request_args', array( __CLASS__, 'auth_download' ), 10, 2 );
		add_action( 'upgrader_process_complete', array( __CLASS__, 'clear' ), 10, 0 );
	}

	private static function headers( $accept = 'application/vnd.github+json' ) {
		$headers = array( 'Accept' => $accept, 'User-Agent' => 'vu-legals-updater' );
		if ( defined( 'VUL_GITHUB_TOKEN' ) && VUL_GITHUB_TOKEN ) {
			$headers['Authorization'] = 'Bearer ' . VUL_GITHUB_TOKEN;
		}
		return $headers;
	}

	private static function release() {
		$cached = get_transient( self::TRANSIENT );
		if ( false !== $cached ) {
			return $cached ?: null;
		}

		$res  = wp_remote_get( 'https://api.github.com/repos/' . self::REPO . '/releases/latest', array( 'timeout' => 10, 'headers' => self::headers() ) );
		$data = ( ! is_wp_error( $res ) && 200 === wp_remote_retrieve_response_code( $res ) ) ? json_decode( wp_remote_retrieve_body( $res ), true ) : null;

		$release = null;
		if ( is_array( $data ) && ! empty( $data['tag_name'] ) ) {
			$package = '';
			foreach ( (array) ( $data['assets'] ?? array() ) as $asset ) {
				if ( self::ASSET === ( $asset['name'] ?? '' ) ) {
					// Private repos need the API URL (token-authenticated); public ones use the direct link.
					$package = defined( 'VUL_GITHUB_TOKEN' ) && VUL_GITHUB_TOKEN ? $asset['url'] : $asset['browser_download_url'];
					break;
				}
			}
			if ( $package ) {
				$release = array(
					'version' => ltrim( $data['tag_name'], 'vV' ),
					'package' => $package,
					'url'     => $data['html_url'] ?? 'https://github.com/' . self::REPO,
					'notes'   => $data['body'] ?? '',
				);
			}
		}

		set_transient( self::TRANSIENT, $release ?: 0, 6 * HOUR_IN_SECONDS );
		return $release;
	}

	public static function check( $transient ) {
		if ( ! is_object( $transient ) ) {
			return $transient;
		}
		$release = self::release();
		if ( ! $release || version_compare( $release['version'], VUL_VERSION, '<=' ) ) {
			return $transient;
		}
		$basename                        = plugin_basename( VUL_FILE );
		$transient->response[ $basename ] = (object) array(
			'slug'        => 'vu-legals',
			'plugin'      => $basename,
			'new_version' => $release['version'],
			'url'         => $release['url'],
			'package'     => $release['package'],
			'requires'    => '6.3',
			'requires_php'=> '8.0',
		);
		return $transient;
	}

	public static function info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || 'vu-legals' !== $args->slug ) {
			return $result;
		}
		$release = self::release();
		if ( ! $release ) {
			return $result;
		}
		return (object) array(
			'name'          => 'Vu Legals',
			'slug'          => 'vu-legals',
			'version'       => $release['version'],
			'author'        => '<a href="https://vudigital.co.uk">Vu Digital</a>',
			'homepage'      => $release['url'],
			'requires'      => '6.3',
			'requires_php'  => '8.0',
			'download_link' => $release['package'],
			'sections'      => array( 'changelog' => nl2br( esc_html( $release['notes'] ) ) ),
		);
	}

	/** Private-repo asset downloads need the token and an octet-stream Accept header. */
	public static function auth_download( $args, $url ) {
		if ( defined( 'VUL_GITHUB_TOKEN' ) && VUL_GITHUB_TOKEN && 0 === strpos( $url, 'https://api.github.com/repos/' . self::REPO . '/releases/assets/' ) ) {
			$args['headers'] = array_merge( (array) ( $args['headers'] ?? array() ), self::headers( 'application/octet-stream' ) );
		}
		return $args;
	}

	public static function clear() {
		delete_transient( self::TRANSIENT );
	}
}
