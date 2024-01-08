<?php
use PPT\PPTAdmin;
if (!$this) { return; }
?><dl>
	<dt>
		<p class="ppt-blurb">
			<?php _e('Copy and share this secret Password Token URL to allow readers to see the content of this post', 'post-password-token'); ?> 
			<a href="options-general.php?page=<?php echo PPTAdmin::ADMIN_PAGE_SLUG; ?>"><?php _e('Learn more', 'post-password-token'); ?> &raquo;</a>
		</p>
	</dt>
	<dd>
		
<?php
	global $post;
	$url = $this->get_ppt_permalink($post);

	echo <<<PRETTYURL
			<a href="$url">$url</a>
		PRETTYURL;

	if (get_option('permalink_structure') != '') {
		$shortUrl = $this->get_ppt_permalink($post, true);
		$or = __('or', 'post-password-token');
		echo <<<SHORTURL
				<br />
				$or <br />
				<a href="$shortUrl">$shortUrl</a>
			SHORTURL;
	}
?>
	</dd>
</dl>
