/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Disabled,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

const DEFAULT_MIN_ITEMS = 1;
const DEFAULT_MAX_ITEMS = 100;

export default function CampaignArchiveEdit( { attributes, setAttributes } ) {
	const {
		itemsToShow,
		campaignTitle,
		displaySender,
		displayDate,
		displayTime,
	} = attributes;

	function toggleAttribute( propName ) {
		return () => {
			const value = attributes[ propName ];

			setAttributes( { [ propName ]: ! value } );
		};
	}

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'campaign-archive-block-for-mailchimp' ) }>
					<RangeControl
						label={ __( 'Number of items', 'campaign-archive-block-for-mailchimp' ) }
						value={ itemsToShow }
						onChange={ ( value ) =>
							setAttributes( { itemsToShow: value } )
						}
						min={ DEFAULT_MIN_ITEMS }
						max={ DEFAULT_MAX_ITEMS }
						required
					/>
					<SelectControl
						label={ __( 'Campaign title', 'campaign-archive-block-for-mailchimp' ) }
						value={ campaignTitle }
						options={ [
							{ label: _x( 'Subject', 'campaign-title', 'campaign-archive-block-for-mailchimp' ), value: 'subject' },
							{ label: _x( 'Title', 'campaign-title', 'campaign-archive-block-for-mailchimp' ), value: 'title' },
						] }
						onChange={ newCampaignTitle => setAttributes( { campaignTitle: newCampaignTitle } ) }
					/>
					<ToggleControl
						label={ __( 'Display sender', 'campaign-archive-block-for-mailchimp' ) }
						checked={ displaySender }
						onChange={ toggleAttribute( 'displaySender' ) }
					/>
					<ToggleControl
						label={ __( 'Display date', 'campaign-archive-block-for-mailchimp' ) }
						checked={ displayDate }
						onChange={ toggleAttribute( 'displayDate' ) }
					/>
					{ displayDate && <ToggleControl
						label={ __( 'Display time', 'campaign-archive-block-for-mailchimp' ) }
						checked={ displayTime }
						onChange={ toggleAttribute( 'displayTime' ) }
					/> }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="cabfm/campaign-archive"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
