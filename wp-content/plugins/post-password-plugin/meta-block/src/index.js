import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import PostPasswordTokens from './lib/PostPasswordTokens';
import { PostNotPublished, PostNotProtected } from './lib/inactive-states';

const PostPasswordPluginPanel = () => {
	const currentPost = select( 'core/editor' ).getCurrentPost();
	const isPwProtected = currentPost.password?.length > 0;
	const isPublished = [ 'publish', 'future' ].includes( currentPost.status );

	return (
		<PluginDocumentSettingPanel
			name="post-password-token"
			title={ __( 'Post Password Token', 'post-password-token' ) }
			className="post-password-token-panel"
		>
			{ ! isPublished && <PostNotPublished /> }
			{ ! isPwProtected && <PostNotProtected /> }
			{ isPublished && isPwProtected && (
				<PostPasswordTokens post={ currentPost } />
			) }
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'post-password-token', {
	render: PostPasswordPluginPanel,
} );
