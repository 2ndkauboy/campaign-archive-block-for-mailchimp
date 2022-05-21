/**
 * WordPress dependencies
 */
import { __, _x, sprintf } from '@wordpress/i18n';
import { ENTER } from '@wordpress/keycodes';
import { InspectorControls } from '@wordpress/block-editor';
import { Button, ExternalLink, PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const GET_KEY_URL = 'https://us1.admin.mailchimp.com/account/api/';
const HELP_URL = 'https://mailchimp.com/en/help/about-api-keys/';

const DEFAULT_MIN_ITEMS = 1;
const DEFAULT_MAX_ITEMS = 100;

const Inspector = ( props ) => {
	const {
		attributes,
		setAttributes,
		apiKeyObject,
		setApiKeyObject,
	} = props;

	const {
		itemsToShow,
		campaignTitle,
		displaySender,
		displayDate,
		displayTime,
	} = attributes;

	const [ apiKeyInspectorState, setApiKeyInspectorState ] = useState( apiKeyObject.key );

	const handleKeyDown = ( keyCode ) => {
		if ( keyCode !== ENTER ) {
			return;
		}

		updateApiKey();
	};

	useEffect( () => {
		apiFetch( { path: '/wp/v2/settings' } ).then( ( res ) => {
			setApiKeyObject( {
				key: res.cabfm_api_key,
				valid: res.cabfm_api_credentials_validation_result,
				message: res.cabfm_api_credentials_validation_message,
				account_name: res.cabfm_api_credentials_account_name,
			} );
		} );
	}, [] );

	const updateApiKey = () => {
		saveApiKey( apiKeyInspectorState.trim() );
	};

	const saveApiKey = ( apiKeyValue ) => {
		apiFetch( {
			data: { cabfm_api_key: apiKeyValue },
			method: 'POST',
			path: '/wp/v2/settings',
		} ).then( ( res ) => {
			setApiKeyObject( {
				key: res.cabfm_api_key,
				valid: res.cabfm_api_credentials_validation_result,
				message: res.cabfm_api_credentials_validation_message,
				account_name: res.cabfm_api_credentials_account_name,
			} );
		} );
	};

	const removeApiKey = () => {
		saveApiKey( '' );
	};

	return (
		<>
			<InspectorControls>
				{ apiKeyObject.valid &&
					<PanelBody
						initialOpen={ apiKeyObject.valid }
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
					title={ __( 'Mailchimp API key', 'campaign-archive-block-for-mailchimp' ) }
				>
					{ ! apiKeyObject.valid &&
						<>
							<p>{ __( 'To use the Campaign Archive block on your site, you have to provide credentials for the Mailchimp API in the settings below.', 'campaign-archive-block-for-mailchimp' ) }</p>
							<p>
								<ExternalLink href={ GET_KEY_URL }>{ __( 'Get an API key', 'campaign-archive-block-for-mailchimp' ) }</ExternalLink>&nbsp;|&nbsp;
								<ExternalLink href={ HELP_URL }>{ __( 'How to get a key?', 'campaign-archive-block-for-mailchimp' ) }</ExternalLink>
							</p>
							<TextControl
								label={ __( 'Mailchimp API key', 'campaign-archive-block-for-mailchimp' ) }
								hideLabelFromVision="true"
								placeholder={ __( 'Add Mailchimp API key…', 'campaign-archive-block-for-mailchimp' ) }
								onChange={ ( value ) => setApiKeyInspectorState( value ) }
								onKeyDown={ ( { keyCode } ) => handleKeyDown( keyCode ) }
								value={ apiKeyInspectorState }
							/>
							{ '' !== apiKeyObject.message && ! apiKeyObject.valid &&
								<div className="cabfm-credentials-validation-error">
									<div className="components-notice is-error">
										<div className="components-notice__content">
											{
												sprintf(
													// translators: the API error message.
													__( 'There was an error connecting to your Mailchimp account: %s', 'campaign-archive-block-for-mailchimp' ),
													apiKeyObject.message
												)
											}
										</div>
									</div>
								</div>
							}
							<Button
								disabled={ ( '' === apiKeyInspectorState ) }
								isPrimary
								onClick={ updateApiKey }
							>
								{ __( 'Save', 'campaign-archive-block-for-mailchimp' ) }
							</Button>
						</>
					}
					{ apiKeyObject.valid &&
						<>
							<div className="cabfm-credentials-status">
								<div className="components-notice is-success">
									<div className="components-notice__content">
										{ __( 'You have been successfully connected to a Mailchimp account:', 'campaign-archive-block-for-mailchimp' ) }
									</div>
								</div>
							</div>
							<p className="cabfm-acount-name-current">
								<b>{ __( 'Account:', 'campaign-archive-block-for-mailchimp' ) }</b><br/>
								{ apiKeyObject.account_name }
							</p>
							<p className="cabfm-api-key-current">
								<b>{ __( 'API key:', 'campaign-archive-block-for-mailchimp' ) }</b><br/>
								{ apiKeyObject.key }
							</p>
							<Button
								className="components-button is-destructive"
								disabled={ ! apiKeyObject.key }
								isSecondary
								onClick={ removeApiKey }
							>
								{ __( 'Remove API key', 'campaign-archive-block-for-mailchimp' ) }
							</Button>
						</>
					}
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default Inspector;
