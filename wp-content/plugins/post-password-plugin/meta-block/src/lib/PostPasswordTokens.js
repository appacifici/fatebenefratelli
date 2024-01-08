import React from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import CopyUrlBlock from './CopyUrlBlock';
import { Notice } from '@wordpress/components';

const fetchTokenizedUrls = async ( postId ) => {
	const url = new URL(
		`${ window.location.origin }/wp-json/post-password-token/v1/post/${ postId }/tokens`
	);

	return apiFetch( {
		url: url.toString(),
	} ).catch( ( responseError ) => {
		if (
			responseError &&
			responseError.hasOwnProperty( 'error' ) &&
			responseError.hasOwnProperty( 'message' )
		) {
			console.error(
				`Could not load post password tokens. Server response: ${ responseError.message }`
			);
			return responseError;
		}

		// @TODO: is there a logging facility within Gutenberg?
		console.error( `Unknown error has occurred:` );
		console.error( responseError );
		return {
			error: 500,
			message: __(
				'An unknown error occurred while fetching password tokens',
				'post-password-token'
			),
		};
	} );
};

const PostPasswordTokens = ( props ) => {
	const [ tokenResponse, setTokens ] = useState( null );

	useEffect( () => {
		fetchTokenizedUrls( props.post.id ).then( ( response ) => {
			if (
				response.hasOwnProperty( 'shortUrl' ) ||
				response.hasOwnProperty( 'error' )
			) {
				setTokens( response );
			}
		} );
	}, [ props.post ] );

	const isLoaded = tokenResponse !== null;
	const isError =
		tokenResponse !== null && tokenResponse.hasOwnProperty( 'error' );

	return (
		<React.Fragment>
			{ isLoaded && isError && (
				<Notice status="error" isDismissible={ false }>
					{ __(
						'Post Password Token Error:',
						'post-password-token'
					) }{ ' ' }
					{ tokenResponse.message }
				</Notice>
			) }
			{ isLoaded && ! isError && (
				<div>
					<p>
						{ __(
							'Copy and share this secret Password Token URL to allow readers to see the content of this post',
							'post-password-token'
						) }
					</p>
					<p>
						<CopyUrlBlock url={ tokenResponse.shortUrl }>
							{ __( 'Short Url', 'post-password-token' ) }
						</CopyUrlBlock>
						<br />
						<CopyUrlBlock url={ tokenResponse.prettyUrl }>
							{ __( 'Permalink', 'post-password-token' ) }
						</CopyUrlBlock>
					</p>
				</div>
			) }
		</React.Fragment>
	);
};

export default PostPasswordTokens;
