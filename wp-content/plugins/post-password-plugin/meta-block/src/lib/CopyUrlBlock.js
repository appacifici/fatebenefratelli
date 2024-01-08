import React from 'react';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Button, ClipboardButton } from '@wordpress/components';
import './editor.scss';

/**
 * ClipboardButton storybook:
 * https://wp-storybook.netlify.app/?path=/story/components-clipboardbutton--basic
 */

const CopyButton = ( { copyText } ) => {
	const [ hasCopied, setCopied ] = useState( false );

	return (
		<ClipboardButton
			text={ copyText }
			isTertiary={ true }
			onCopy={ () => setCopied( true ) }
			onFinishCopy={ () => setCopied( false ) }
		>
			{ hasCopied
				? __( 'Copied!', 'post-password-token' )
				: __( 'Copy', 'post-passord-token' ) }
		</ClipboardButton>
	);
};

const CopyUrlBlock = ( { children, url } ) => {
	const [ hidden, setVisibility ] = useState( true );

	const toggleText = hidden
		? __( 'Show', 'post-password-token' ) + ' \u2193'
		: __( 'Hide', 'post-password-token' ) + ' \u2191';

	const toggle = ( state ) => {
		setVisibility( ! state );
	};

	return (
		<React.Fragment>
			<b>{ children }</b>:&nbsp;
			<CopyButton copyText={ url } />
			<Button
				isTertiary={ true }
				onClick={ () => {
					toggle( hidden );
				} }
			>
				{ toggleText }
			</Button>
			<span className={ hidden ? 'hidden' : '' }>{ url }</span>
		</React.Fragment>
	);
};

export default CopyUrlBlock;
