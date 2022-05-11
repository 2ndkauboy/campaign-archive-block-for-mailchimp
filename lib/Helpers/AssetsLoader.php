<?php
/**
 * Class to register client-side assets (scripts and stylesheets) for the Gutenberg block.
 *
 * @package CABFM\Helpers
 */

namespace CABFM\Helpers;

/**
 * Class AssetsLoader
 */
class AssetsLoader {
	/**
	 * Registers all block assets so that they can be enqueued through Gutenberg in the corresponding context.
	 *
	 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );
	}

	/**
	 * Register the assets for all blocks.
	 */
	public function register_assets() {
		$block_editor_assets_path  = 'build/index.asset.php';
		$block_editor_scripts_path = 'build/index.js';
		$block_editor_style_path   = 'build/index.css';
		$style_assets_path         = 'build/style-index.asset.php';
		$style_scripts_path        = 'build/style-index.js';
		$style_style_path          = 'build/style-index.css';

		if ( file_exists( CABFM_PATH . $block_editor_assets_path ) ) {
			$block_editor_asset = require CABFM_PATH . $block_editor_assets_path;
		} else {
			$block_editor_asset = [
				'dependencies' => [
					'wp-i18n',
					'wp-element',
					'wp-blocks',
					'wp-components',
					'wp-editor',
					'wp-polyfill',
				],
				'version'      => CABFM_VERSION,
			];
		}

		if ( file_exists( CABFM_PATH . $style_assets_path ) ) {
			$style_asset = require CABFM_PATH . $style_assets_path;
		} else {
			$style_asset = [
				'dependencies' => [],
				'version'      => CABFM_VERSION,
			];
		}

		// Register the bundled block JS file.
		if ( file_exists( CABFM_PATH . $block_editor_scripts_path ) ) {
			wp_register_script(
				'campaign-archive-block-for-mailchimp-editor',
				CABFM_URL . $block_editor_scripts_path,
				$block_editor_asset['dependencies'],
				$block_editor_asset['version'],
				true
			);
		}

		// Register optional frontend and editor styles.
		if ( file_exists( CABFM_PATH . $style_scripts_path ) ) {
			wp_register_script(
				'campaign-archive-block-for-mailchimp',
				CABFM_URL . $style_scripts_path,
				$style_asset['dependencies'],
				$style_asset['version'],
				true
			);
		}

		// Register optional editor only styles.
		if ( file_exists( CABFM_PATH . $block_editor_style_path ) ) {
			wp_register_style(
				'campaign-archive-block-for-mailchimp-editor',
				CABFM_URL . $block_editor_style_path,
				[],
				$block_editor_asset['version']
			);
		}

		// Register optional frontend and editor styles.
		if ( file_exists( CABFM_PATH . $style_style_path ) ) {
			wp_register_style(
				'campaign-archive-block-for-mailchimp',
				CABFM_URL . $style_style_path,
				[],
				$style_asset['version']
			);
		}

		wp_set_script_translations( 'campaign-archive-block-for-mailchimp-editor', 'campaign-archive-block-for-mailchimp', plugin_dir_path( CABFM_FILE ) . 'languages' );
	}

	/**
	 * Enqueue the block editor assets.
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script( 'campaign-archive-block-for-mailchimp-editor' );
	}

	/**
	 * Enqueue the frontend and block editor assets.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'campaign-archive-block-for-mailchimp' );
	}
}
