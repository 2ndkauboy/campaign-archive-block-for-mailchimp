/**
 * WordPress dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { ENTER } from '@wordpress/keycodes';
import { InspectorControls } from '@wordpress/block-editor';
import { Button, ExternalLink, PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

const GET_KEY_URL = 'https://us1.admin.mailchimp.com/account/api/';
const HELP_URL = 'https://mailchimp.com/en/help/about-api-keys/';

const DEFAULT_MIN_ITEMS = 1;
const DEFAULT_MAX_ITEMS = 100;

const Inspector = ( props ) => {
	const {
		attributes,
		setAttributes,
		apiKey,
		updateApiKeyCallBack,
	} = props;
	const {
		itemsToShow,
		campaignTitle,
		displaySender,
		displayDate,
		displayTime,
	} = attributes;

	const [ apiKeyState, setApiKey ] = useState( apiKey );

	useEffect( () => {
		setApiKey( apiKey );
	}, [] );

	const updateApiKey = () => {
		updateApiKeyCallBack( apiKeyState );
	};

	const handleKeyDown = ( keyCode ) => {
		if ( keyCode !== ENTER ) {
			return;
		}

		updateApiKey();
	};

	return (
		<>
			<InspectorControls>
				{ !! apiKey &&
					<PanelBody
						initialOpen={ !! apiKey }
						title={ __( 'Settings', 'campaign-archive-block-for-mailchimp' ) }>
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
							checked={ !! displaySender }
							onChange={ () => setAttributes( { displaySender: ! displaySender } ) }
						/>
						<ToggleControl
							label={ __( 'Display date', 'campaign-archive-block-for-mailchimp' ) }
							checked={ !! displayDate }
							onChange={ () => setAttributes( { displayDate: ! displayDate } ) }
						/>
						{ displayDate && <ToggleControl
							label={ __( 'Display time', 'campaign-archive-block-for-mailchimp' ) }
							checked={ !! displayTime }
							onChange={ () => setAttributes( { displayTime: ! displayTime } ) }
						/> }
					</PanelBody>
				}
				<PanelBody
					initialOpen={ ! apiKey }
					title={ __( 'Mailchimp API key', 'campaign-archive-block-for-mailchimp' ) }
				>
					<p>{ __( 'To use the Campaign Archive block on your site, you have to provide credentials for the Mailchimp API in the settings below.', 'campaign-archive-block-for-mailchimp' ) }</p>
					{ apiKey === '' &&
						<p>
							<ExternalLink href={ GET_KEY_URL }>{ __( 'Get a key', 'campaign-archive-block-for-mailchimp' ) }</ExternalLink>|&nbsp;
							<ExternalLink href={ HELP_URL }>{ __( 'Need help?', 'campaign-archive-block-for-mailchimp' ) }</ExternalLink>
						</p>
					}
					<TextControl
						onChange={ ( value ) => setApiKey( value ) }
						onKeyDown={ ( { keyCode } ) => handleKeyDown( keyCode ) }
						placeholder={ __( 'Add Mailchimp API key…', 'campaign-archive-block-for-mailchimp' ) }
						value={ apiKeyState }
					/>
					<Button
						disabled={ ( apiKeyState === '' ) || ( apiKeyState === apiKey ) }
						isPrimary
						onClick={ updateApiKey }
					>
						{ ( apiKeyState === apiKey && apiKey !== '' ) ? __( 'Saved', 'campaign-archive-block-for-mailchimp' ) : __( 'Save', 'campaign-archive-block-for-mailchimp' ) }
					</Button>
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default Inspector;
