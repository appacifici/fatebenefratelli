import { __ } from '@wordpress/i18n';

export const PostNotProtected = () => {
	return (
		<p>
			{ __(
				'This post is not protected by a password.',
				'post-password-token'
			) }
		</p>
	);
};

export const PostNotPublished = () => {
	return (
		<p>
			{ __(
				'Tokens are only generated for Published and Password Protected posts.',
				'post-password-token'
			) }
		</p>
	);
};
