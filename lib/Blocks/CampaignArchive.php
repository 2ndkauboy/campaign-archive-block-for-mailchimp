<?php
/**
 * Server-side rendering of the `cabfm/campaign-archive` block.
 *
 * @package WordPress
 */

namespace CABFM\Blocks;

use CABFM\Helpers\MailchimpAPI;

/**
 * Class CampaignArchive
 */
class CampaignArchive {
	/**
	 * Initialize the class
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Renders the `cabfm/campaign-archive` block on server.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the block content with received rss items.
	 */
	public function render_block( $attributes ) {
		$query_args = [
			'status'     => 'sent',
			'count'      => $attributes['itemsToShow'],
			'sort_field' => 'send_time',
			'sort_dir'   => 'DESC',
		];

		$args = [
			'method' => 'GET',
			'body'   => $query_args,
		];

		$campaigns = MailchimpAPI::get_response_body( '/campaigns', $args );

		if ( is_wp_error( $campaigns ) ) {
			return '<div class="components-placeholder"><div class="notice notice-error"><strong>' . __( 'API Error:', 'campaign-archive-block-for-mailchimp' ) . '</strong> ' . $campaigns->get_error_message() . '</div></div>';
		}

		if ( ! isset( $campaigns['total_items'] ) ) {
			return '<div class="components-placeholder"><div class="notice notice-error">' . __( 'An error has occurred, which probably means the API is down. Try again later.', 'campaign-archive-block-for-mailchimp' ) . '</div></div>';
		}

		$list_items = '';
		foreach ( $campaigns['campaigns'] as $campaign ) {
			$title = 'title' === $attributes['campaignTitle'] ? $campaign['settings']['title'] : $campaign['settings']['subject_line'];
			$title = esc_html( trim( wp_strip_all_tags( $title ) ) );
			$link  = $campaign['long_archive_url'];
			$link  = esc_url( $link );
			if ( $link ) {
				$title = "<a href='{$link}'>{$title}</a>";
			}
			$title = "<div class='wp-block-campaign-archive-title'>{$title}</div>";

			$date = '';
			if ( $attributes['displayDate'] ) {
				$date = strtotime( $campaign['send_time'] );

				if ( $date ) {
					if ( $attributes['displayTime'] ) {
						$date = sprintf(
							'<time datetime="%1$s" class="wp-block-campaign-archive-publish-date">%2$s</time> ',
							date_i18n( DATE_W3C, $date ),
							sprintf(
							/* translators: 1: send date, 2: send time */
								__( '%1$s @ %2$s', 'campaign-archive-block-for-mailchimp' ),
								date_i18n( get_option( 'date_format' ), $date ),
								date_i18n( get_option( 'time_format' ), $date )
							)
						);
					} else {
						$date = sprintf(
							'<time datetime="%1$s" class="wp-block-campaign-archive-publish-date">%2$s</time> ',
							date_i18n( DATE_W3C, $date ),
							date_i18n( get_option( 'date_format' ), $date )
						);
					}
				}
			}

			$sender = '';
			if ( $attributes['displaySender'] ) {
				$sender = sprintf(
					'<span class="wp-block-campaign-archive-sender">%s</span>',
					sprintf(
					/* translators: %s: the sender's name. */
						__( 'by %s', 'campaign-archive-block-for-mailchimp' ),
						esc_html( $campaign['settings']['from_name'] )
					)
				);
			}

			$list_items .= "<li class='wp-block-campaign-archive'>{$title}{$date}{$sender}</li>";
		}

		$classnames = [];
		if ( $attributes['displayDate'] ) {
			$classnames[] = 'has-dates';
		}
		if ( $attributes['displaySender'] ) {
			$classnames[] = 'has-authors';
		}

		$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => implode( ' ', $classnames ) ] );

		$campaign_markup = sprintf( ' <ul %s>%s</ul> ', $wrapper_attributes, $list_items );

		/**
		 * Filters the rendered block markup.
		 *
		 * @param string $campaign_markup The rendered block markup.
		 * @param string $campaigns       The queried Mailchimp campaigns.
		 * @param string $attributes      The block attributes.
		 */
		return apply_filters( 'cabfm_helper_api_base', $campaign_markup, $campaigns, $attributes );
	}

	/**
	 * Registers the `cabfm/campaign-archive` block on server.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			CABFM_PATH . '/src/blocks/campaign-archive',
			array(
				'render_callback' => [ $this, 'render_block' ],
			)
		);
	}
}
