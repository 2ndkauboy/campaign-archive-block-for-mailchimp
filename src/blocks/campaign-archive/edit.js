/**
 * Internal dependencies
 */
import Inspector from './inspector';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import { useState } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

const Edit = ( props ) => {
	const blockProps = useBlockProps();
	const {
		attributes,
	} = props;

	const [ apiKeyObject, setApiKeyObject ] = useState( { key: '', valid: false, message: '', account_name: '' } );

	return (
		<>
			<Inspector
				{ ...props }
				apiKeyObject={ apiKeyObject }
				setApiKeyObject={ setApiKeyObject }
			/>
			<div { ...blockProps }>
				<Disabled>
					{ apiKeyObject.valid &&
						<ServerSideRender
							block="cabfm/campaign-archive"
							attributes={ attributes }
							apiKeyObject={ apiKeyObject }
						/>
					}
					{ ! apiKeyObject.valid &&
						<div className="components-placeholder">
							<div className="notice notice-warning">
								{ __( 'To use the Campaign Archive block on your site, you have to provide credentials for the Mailchimp API in the block settings.', 'campaign-archive-block-for-mailchimp' ) }
							</div>
						</div>
					}
				</Disabled>
			</div>
		</>
	);
};

export default Edit;
