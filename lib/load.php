<?php
/**
 * Main plugin file to load other classes
 *
 * @package CABFM
 */

namespace CABFM;

use CABFM\Blocks\CampaignArchive;
use CABFM\Helpers\AssetsLoader;
use CABFM\Helpers\MailchimpAPI;
use CABFM\Settings\MailchimpApiCredentials;

/**
 * Init function of the plugin
 */
function init() {
	// Construct all modules to initialize.
	$modules = [
		'blocks_campaign_archive' => new CampaignArchive(),
		'helpers_assets_loader'   => new AssetsLoader(),
		'helpers_mailchimp_api'   => new MailchimpAPI(),
		// 'settings_mailchimp_api_credentials' => new MailchimpApiCredentials(),
	];

	// Initialize all modules.
	foreach ( $modules as $module ) {
		if ( is_callable( [ $module, 'init' ] ) ) {
			call_user_func( [ $module, 'init' ] );
		}
	}
}

add_action( 'plugins_loaded', 'CABFM\init' );
