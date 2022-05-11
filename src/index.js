/**
 * WordPress dependencies
 */
const { registerBlockType } = wp.blocks;

// Register blocks.
import * as campaignArchive from './blocks/campaign-archive';

/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 */
const registerBlock = ( block ) => {
	if ( ! block ) {
		return;
	}

	const { metadata, settings } = block;

	registerBlockType(
		metadata,
		{
			...settings,
		}
	);
};

/**
 * Function to register blocks.
 */
export const registerCABFMBlocks = () => {
	[
		campaignArchive,
	].forEach( registerBlock );
};

registerCABFMBlocks();
