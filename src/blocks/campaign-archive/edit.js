/**
 * Internal dependencies
 */
import Inspector from './inspector';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { compose } from '@wordpress/compose';
import { useDispatch } from '@wordpress/data';
import {
	Button,
	Icon,
	Placeholder,
	ResizableBox,
	TextControl,
	withNotices,
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const Edit = ( props ) => {
	const blockProps = useBlockProps();
	const {
		attributes,
		setAttributes,
	} = props;

	const {
		hasApiKey,
	} = attributes;

	const [ apiKeyState, setApiKey ] = useState( '' );

	const { __unstableMarkNextChangeAsNotPersistent } = useDispatch(
		blockEditorStore
	);

	useEffect( () => {
		apiFetch( { path: '/wp/v2/settings' } ).then( ( res ) => {
			setApiKey( res.cabfm_api_key );
		} );
	}, [] );

	useEffect( () => {
		if ( !! apiKeyState && ! hasApiKey ) {
			// This side-effect should not create an undo level.
			__unstableMarkNextChangeAsNotPersistent();
			setAttributes( { hasApiKey: true } );
		}
	}, [ apiKeyState, hasApiKey ] );

	const updateApiKey = ( apiKey = apiKeyState ) => {
		apiKey = apiKey.trim();

		saveApiKey( apiKey );

		if ( apiKey === '' ) {
			setAttributes( { hasApiKey: false } );

			return;
		}
	};

	const saveApiKey = ( apiKey = apiKeyState ) => {
		setApiKey( apiKey );

		apiFetch( {
			data: { cabfm_api_key: apiKey },
			method: 'POST',
			path: '/wp/v2/settings',
		} );
	};

	return (
		<>
			<Inspector
				{ ...props }
				apiKey={ apiKeyState }
				updateApiKeyCallBack={ updateApiKey }
			/>
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
};

export default Edit;
