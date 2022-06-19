<?php
/**
 * Server-side rendering of the `cabfm/campaign-archive` block.
 *
 * @package CABFM\REST
 */

namespace CABFM\REST;

use CABFM\Helpers\MailchimpAPI;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class CampaignFolders {
	/**
	 * Initialize the class
	 */
	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_campaigns_folders_rest_route' ] );
	}

	/**
	 * Register the route to get campaign folders from the Mailchimp API.
	 */
	public function register_campaigns_folders_rest_route() {
		register_rest_route(
			'cabfm/v1',
			'/campaign-folders',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_campaigns_folders' ],
				'permission_callback' => function () {
					return true || current_user_can( 'edit_posts' );
				},
			]
		);
	}

	/**
	 * Get all campaign folders from the Mailchimp API.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_campaigns_folders( $request ) {
		$response_body = MailchimpAPI::get_response_body( '/campaign-folders' );

		if ( is_wp_error( $response_body ) ) {
			return $response_body;
		}

		$campaign_folders = [];
		if ( isset( $response_body['folders'] ) ) {
			foreach ( $response_body['folders'] as $folder ) {
				$campaign_folders[] = [
					'id'   => $folder['id'],
					'name' => $folder['name'],
				];
			}
		}

		return new WP_REST_Response( $campaign_folders, 200 );
	}
}
