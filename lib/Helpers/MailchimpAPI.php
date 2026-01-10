<?php
/**
 * Helper class to access the Mailchimp API
 *
 * @see      WooCommerce/Admin/WC_Helper_API
 *
 * @package  CABFM\Helpers
 */

namespace CABFM\Helpers;

use WP_Error;

/**
 * Class MailchimpAPI
 */
class MailchimpAPI {
	/**
	 * Base path for API routes.
	 *
	 * @var string
	 */
	public static $api_base;

	/**
	 * The authorization ID.
	 *
	 * @var string
	 */
	public static $server_prefix = '';

	/**
	 * The authorization key.
	 *
	 * @var string
	 */
	public static $api_key = '';

	/**
	 * Initialize the helper
	 */
	public function init() {
		add_action( 'init', [ $this, 'load' ] );
	}

	/**
	 * Load
	 *
	 * Allow devs to point the API base to a local API development or staging server.
	 * The URL can be changed on init before priority 10.
	 */
	public static function load() {
		self::set_api_key( get_option( 'cabfm_api_key' ) );
	}

	/**
	 * Set the API key.
	 *
	 * @param string $api_key The new API key.
	 */
	public static function set_api_key( $api_key ) {
		self::$api_key = $api_key;

		// Split the API key, as it has the "Server Prefix" as a suffix.
		$split_key = explode( '-', self::$api_key );
		// Set the "Server Prefix" to the suffix of the split key, to the old option or to "us1" as a fallback.
		self::$server_prefix = empty( $split_key[1] ) ? get_option( 'cabfm_server_prefix', 'us1' ) : $split_key[1];

		/**
		 * Filters the Mailchimp API base URL.
		 *
		 * @param string $api_base      The base URL for the Mailchimp API.
		 * @param string $server_prefix The Mailchimp server prefix (e.g. us11).
		 */
		self::$api_base = apply_filters( 'cabfm_helper_api_base', sprintf( 'https://%s.api.mailchimp.com/3.0', self::$server_prefix ), self::$server_prefix );
	}

	/**
	 * Perform an HTTP request to the Helper API.
	 *
	 * @param string $endpoint The endpoint to request.
	 * @param array  $args     Additional data for the request. Set authenticated to a truthy value to enable auth.
	 *
	 * @return array|WP_Error The response from wp_safe_remote_request()
	 */
	public static function request( $endpoint, $args = [] ) {
		$url = self::url( $endpoint );

		if ( empty( self::$api_key ) ) {
			return new WP_Error( 'cabfm_authentication', __( 'You need to set up the API credentials in the settings!', 'campaign-archive-block-for-mailchimp' ) );
		}

		$args['headers']['Authorization'] = 'Authorization ' . self::$api_key;

		/**
		 * Allow developers to filter the request args passed to wp_safe_remote_request().
		 * Useful to remove sslverify when working on a local api dev environment.
		 */
		$args = apply_filters( 'cabfm_helper_api_request_args', $args, $endpoint );

		return wp_safe_remote_request( $url, $args );
	}

	/**
	 * Wrapper for self::request().
	 *
	 * @param string $endpoint The helper API endpoint to request.
	 * @param array  $args     Arguments passed to wp_remote_request().
	 *
	 * @return array The response object from wp_safe_remote_request().
	 */
	public static function get( $endpoint, $args = [] ) {
		$args['method'] = 'GET';

		return self::request( $endpoint, $args );
	}

	/**
	 * Wrapper for self::request().
	 *
	 * @param string $endpoint The helper API endpoint to request.
	 * @param array  $args     Arguments passed to wp_remote_request().
	 *
	 * @return array The response object from wp_safe_remote_request().
	 */
	public static function post( $endpoint, $args = [] ) {
		$args['method'] = 'POST';

		return self::request( $endpoint, $args );
	}

	/**
	 * Using the API base, form a request URL from a given endpoint.
	 *
	 * @param string $endpoint The endpoint to request.
	 *
	 * @return string The absolute endpoint URL.
	 */
	public static function url( $endpoint ) {
		$endpoint = ltrim( $endpoint, '/' );
		$endpoint = sprintf( '%s/%s', self::$api_base, $endpoint );
		$endpoint = esc_url_raw( $endpoint );

		return $endpoint;
	}

	/**
	 * Execute the request to the ProvenExpert API and get the result body
	 *
	 * @param string $endpoint The endpoint to request.
	 * @param array  $args     Additional data for the request. Set authenticated to a truthy value to enable auth.
	 *
	 * @return array|WP_Error The response from wp_safe_remote_request()
	 */
	public static function get_response_body( $endpoint, $args = [] ) {
		if ( empty( $args ) ) {
			$args = [
				'method' => 'GET',
			];
		}

		// Sort the body arguments to get the same hash for the same values.
		ksort( $args['body'] );
		// Create a hash object including the endpoint and API key.
		$hash_object = [
			'endpoint' => $endpoint,
			'api_key'  => self::$api_key,
			'body'     => $args['body'],
		];
		// Get the caching hash using the hash object.
		$hash = md5( wp_json_encode( $hash_object ) );

		$cache_key     = '_cabfm_api_request_' . $hash;
		$response_body = get_transient( $cache_key );

		if ( false === $response_body ) {
			$request       = self::request( $endpoint, $args );
			$response_code = wp_remote_retrieve_response_code( $request );

			if ( is_wp_error( $request ) ) {
				return $request;
			}

			if ( 200 !== $response_code ) {
				$response_message = wp_remote_retrieve_response_message( $request );

				/* translators: %d: HTTP response code, %s: error message */
				$error_message = sprintf( __( 'Error receiving the data from the Mailchimp API (%1$d): %2$s', 'campaign-archive-block-for-mailchimp' ), $response_code, $response_message );

				return new WP_Error( 'cabfm_request', $error_message );
			} else {
				$response_body = json_decode( wp_remote_retrieve_body( $request ), true );
			}

			/**
			 * Filters the cache duration defined in minutes.
			 *
			 * @param int $cache_duration The current cache duration. Default is 60 minutes.
			 *
			 * @return int
			 */
			$cache_duration = apply_filters( 'cabfm_cache_minutes', 60 );
			set_transient( $cache_key, $response_body, $cache_duration * MINUTE_IN_SECONDS );
		}

		return $response_body;
	}
}
