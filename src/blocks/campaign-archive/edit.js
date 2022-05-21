/**
 * Internal dependencies
 */
import Inspector from './inspector';

/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import { useState } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

const Edit = ( props ) => {
	const blockProps = useBlockProps();
	const {
		attributes,
	} = props;

	const [ apiKeyObject, setApiKeyObject ] = useState( { key: '', valid: false, message: '' } );

	return (
		<>
			<Inspector
				{ ...props }
				apiKeyObject={ apiKeyObject }
				setApiKeyObject={ setApiKeyObject }
			/>
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="cabfm/campaign-archive"
						attributes={ attributes }
						apiKeyObject={ apiKeyObject }
					/>
				</Disabled>
			</div>
		</>
	);
};

export default Edit;
