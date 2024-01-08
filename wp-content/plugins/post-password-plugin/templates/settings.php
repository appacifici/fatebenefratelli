<?php

use PPT\PPTAdmin;

/**
 * Requires/Assumes being loaded via PPTAdmin
 */
$options = $this->wpFuncs->get_option();

$hide_protected = $options['hide_protected'] ? 'checked="checked"' : "";
$ppt_enable_post = in_array('post', $options['enable']) ? 'checked="checked" ' : '';
$ppt_enable_page = in_array('page', $options['enable']) ? 'checked="checked" ' : '';
$salt = !empty($options['salt']) ? htmlspecialchars($options['salt']) : '';

$md5_enabled = $options['hash_algo'] == PPTAdmin::OBSOLETE_HASH_ALGO ? 'selected' : '';
$sha256_enabled = $options['hash_algo'] == PPTAdmin::DEFAULT_HASH_ALGO ? 'selected' : '';

$isOptionsSuccess = isset($_GET['options-success']) && $_GET['options-success'] == 1;
$isSaltSuccess = isset($_GET['salt-success']) && $_GET['salt-success'] == 1;
$isAlgoSuccess = isset($_GET['algo-success']) && $_GET['algo-success'] == 1;
?>
<div id="ppt-wrap" class="wrap">
<?php
	if ($isOptionsSuccess || $isSaltSuccess || $isAlgoSuccess) { ?>
		<div class="notice notice-success is-dismissible">
			<p>Settings successfully updated.</p>
		</div>
	<?php }
?>
    <h2><?php _e('Post Password Token', 'post-password-token'); ?></h2>
    <p><?php _e('Issue secret Password token urls that allow readers to access password-protected posts without having to type in a password. This is similar to Flickr’s "Guest Pass" functionality.', 'post-password-token'); ?></p>
    <p><?php _e('After password protecting a page or post the url with token will be displayed in a meta-box below the post-content area on the post/page edit screen.', 'post-password-token'); ?></p>
    <p><?php _e('Accessing a password-protected post by its url will still show the standard password dialog, but if a reader accesses a password-protected post by its <strong>secret Password Token url</strong>, they will be automatically authenticated and be able to see the full content. Accessing the post by its secret url will also set an authentication cookie for the user that lasts for 10 days.', 'post-password-token'); ?></p>
    
    <hr class="ppt-hr" />

    <h3><?php _e('Plugin Options', 'post-password-token'); ?></h3>
        <form method="post" id="ppt-options-form" name="ppt-options-form">
            <div class="ppt-form-box ppt-rounded">
                <h3><?php _e('Protected Post Visibility', 'post-password-token'); ?></h3>
                <p><?php _e('Protected posts can be hidden from general view. Protected posts will be hidden from everyone everywhere posts are shown and only displayed when directly accessed via the permalink.', 'post-password-token'); ?></p>
                <p class="ppt-inset">
                    <input type="checkbox" name="ppt_hide_protected" id="ppt_hide_protected" value="1" <?php echo $hide_protected; ?> />
                    <label for="ppt_hide_protected"><?php _e('Hide Protected Posts', 'post-password-token'); ?></label>
                </p>
                <h3><?php _e('Post Type Support', 'post-password-token'); ?></h3>
                <p><?php _e('This feature can be enabled or disabled for Pages, Posts, and custom post types.', 'post-password-token'); ?></p>
                <div class="ppt-post-type-options ppt-inset">
                    <p>
                        <input type="checkbox" name="ppt_enable[post]" id="ppt_enable_post" value="1" <?php echo $ppt_enable_post; ?> />
                        <label for="ppt_enable_post"><?php _e('Posts', 'post-password-token'); ?></label>
                    </p>
                    <p>
                        <input type="checkbox" name="ppt_enable[page]" id="ppt_enable_page" value="1" <?php echo $ppt_enable_page; ?> />
                        <label for="ppt_enable_page"><?php _e('Pages', 'post-password-token'); ?></label>
                    </p>
<?php
	/**
	 * @var array<string>
	 */
	$post_types = get_post_types([], 'names');
	foreach ($post_types as $type) {
		/**
		 * @var WP_Post_Type $o
		 */
		$o = get_post_type_object($type);
		if ($o->_builtin != true) {
			$checked = in_array($o->name, $options['enable']) ? 'checked="checked" ' : '';
			echo <<<CHECKBOX
					<p>
						<input type="checkbox" name="ppt_enable[{$o->name}]" id="ppt_enable_{$o->name}" value="1" {$checked}/>
						<label for="ppt_enable_{$o->name}">{$o->name}</label>
					</p>
				CHECKBOX;
		}
	}
?>
			</div>
			<p>
				<?php echo wp_nonce_field('ppt_update_settings', '_wpnonce', true, false); ?>
				<button class="button-primary" type="submit" id="ppt-save-options" name="ppt-save-options"><?php _e('Save Options', 'post-password-token'); ?></button>
			</p>
		</div>
	</form>

	<h3><?php _e('Advanced Options', 'post-password-token'); ?></h3>
	<div class="ppt-danger-box ppt-rounded">	
		<form id="token-profile" name="token-profile" method="post">
			<h3><?php _e('Password Salt', 'post-password-token'); ?></h3>
			<p class="help">
				<?php _e('A "salt" is a secret code key that is used when creating tokens, making them more secure. We recommend that you <strong>set this once and leave it</strong>', 'post-password-token'); ?></strong>.
			</p>
			<p>
				<label for="ppt_salt"><?php _e('Salt', 'post-password-token'); ?></label>
				<input class="advanced-option-input" type="text" size="50" id="ppt_salt" name="ppt_salt" value="<?php echo $salt; ?>" />
				<span class="hide-if-no-js"><a class="unlock ppt-unlock" href="#token-profile" title="<?php _e('Locked: click to make changes.', 'post-password-token'); ?>"><?php _e('edit', 'post-password-token'); ?></a></span>
			</p>
			<div class="advanced-option-control ppt-hide">
				<p><?php _e('<strong>Warning</strong>: changing the salt will modify all <em>Password Token URLs</em> site-wide: readers will no longer be able to use old <em>Password Token URLs</em> to view protected content.', 'post-password-token'); ?></p>
				<p>
					<?php wp_nonce_field('ppt_update_salt', '_wpnonce', true, true); ?>
					<button type="submit" class="button-primary" id="ppt-save-salt" name="ppt-save-salt"><?php _e('Save salt, change tokens for all posts', 'post-password-token'); ?></button> <span class="hide-if-no-js"><?php _e('or', 'post-password-token'); ?> <a class="lock-cancel" href="#token-profile"><?php _e('cancel', 'post-password-token'); ?></a></span>
				</p>
			</div>
		</form>
	</div>
	<br/>
	<div class="ppt-danger-box ppt-rounded">
		<form id="hashing-profile" name="hashing-profile" method="post">
			<h3><?php _e('Hashing Algorithm', 'post-password-token'); ?></h3>
			<p><?php _e('The hashing algorithm is what generates the unique password token.', 'post-password-token'); ?></p>
			<p><?php _e('Cryptography is a complex subject, so the short of it is "newer is better". However, <b>upgrading the hashing algorithm will obsolete your old urls</b>, so if you need to maintain backwards compatability, then you should leave this alone.', 'post-password-token'); ?></p>
			<p><?php _e('Recommended Hash Algorith is', 'post-password-token'); ?> <code><?php echo PPTAdmin::DEFAULT_HASH_ALGO; ?></code>.<p>
			<p>
				<label for="ppt_algo"><?php _e('Hash Algorithm', 'post-password-token'); ?></label>
				<select class="advanced-option-input" name="ppt_algo" id="ppt_algo">
					<option <?php echo $sha256_enabled; ?> value="<?php echo PPTAdmin::DEFAULT_HASH_ALGO; ?>">sha256</option>
					<option <?php echo $md5_enabled; ?> value="<?php echo PPTAdmin::OBSOLETE_HASH_ALGO; ?>">md5</option>
				</select>
				<span class="hide-if-no-js"><a class="unlock ppt-unlock" href="#hashing-profile" title="<?php _e('Locked: click to make changes.', 'post-password-token'); ?>"><?php _e('edit', 'post-password-token'); ?></a></span>
			</p>
			<div class="advanced-option-control ppt-hide">
				<p><?php _e('<strong>Warning</strong>: changing the algorithm will modify all <em>Password Token URLs</em> site-wide: readers will no longer be able to use old <em>Password Token URLs</em> to view protected content.', 'post-password-token'); ?></p>
				<p>
					<?php wp_nonce_field('ppt_update_algo', '_wpnonce', true, true); ?>
					<button type="submit" class="button-primary" id="ppt-save-algorithm" name="ppt-save-algorithm"><?php _e('Save algorithm, change tokens for all posts', 'post-password-token'); ?></button> <span class="hide-if-no-js"><?php _e('or', 'post-password-token'); ?> <a class="lock-cancel" href="#hashing-profile"><?php _e('cancel', 'post-password-token'); ?></a></span>
				</p>
			</div>
		</form>
	</div>

	<hr class="ppt-hr" />
			
	<h3><?php _e('How do I revoke the <em>Password Token URL</em> for a post?', 'post-password-token'); ?></h3>
	
	<p><?php _e('Just change the password on the post. This will change the token, meaning the old link will no longer authenticate readers.', 'post-password-token'); ?></p>
	
	<p><?php _e('If you need to revoke all <em>Password Token URL</em>s everywhere (the nuclear option), you can change the Password Salt above. This will create new tokens for all protected posts and invalidate all old tokens.', 'post-password-token'); ?></p>
	
	<h3><?php _e('A note about Caching', 'post-password-token'); ?></h3>
	<p><?php _e('If your site uses caching (like WP-Super-Cache) these pages will be cached. While this isn’t really a security threat of galactic proportions (not any more so that what this plugin does already and, really, you wouldn’t be using WordPress if national security was at stake) you can stop the pages from being cached if you prefer by adding <code>ppt=</code> to the caching exclusions list in WP-Super-Cache to keep the pages from being cached.', 'post-password-token'); ?></p>
	
	<hr class="ppt-hr" />
	
	<div id="donate">
		<h3><?php _e('Please Donate', 'post-password-token'); ?></h3>
		<p><?php _e('Donations buy donuts. Donuts help keep us motivated. When we’re motivated we make and update plugins. Please help keep us motivated to make more useful plugins.', 'post-password-token'); ?></p>
		<div id="paypal">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="K5N9RTTYNEKRA">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>					
</div>
