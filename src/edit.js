/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, AlignmentControl, BlockControls } from '@wordpress/block-editor';

import { register, select, useSelect } from '@wordpress/data';

import { apiFetch } from '@wordpress/api-fetch';


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import announcementStore from './announcementsStore';

register(announcementStore);

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({attributes: {textAlign}, setAttributes}) {
	const blockProps = useBlockProps({
		className: textAlign ? 'has-text-align-' + textAlign : ''
	});

	const src = useSelect((select) => {
		return select('parish-announcements').getSrc();
	}, []);

	return (
		<>
			<div { ...blockProps }>
				{!src && __('Loading...')}
				{src && (
					<img src={src} alt={__('Actual announcement from the parish', 'parish-announcements')} />
				)}				
			</div>
		</>
	);
}
