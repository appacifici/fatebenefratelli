=== Post Password Token ===
Contributors: shawnparker,gordonbrander  
Donate link: https://top-frog.com/projects/post-password-token/
Tags: post, password, guest, pass, token, protected, hidden, access
Requires PHP: 7.4
Requires at least: 5.7
Tested up to: 6.2
Stable tag: 2.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The Post Password Token plugin allows readers to access protected posts without having to enter a password by creating secret token urls for the post. 

== Description ==

The Post Password Token plugin lets you issue secret urls that allow readers to access protected content without having to enter a password. It extends the default WordPress post password protection functionality by creating secret urls to the post that have an encoded token. This is similar to the guest pass functionality that can be found on Flickr.

= Who is it for? =

Sometimes you would like to share your blog posts with a specific group of people, but not with the wider world. For example, a family might want to blog about their adventures together for friends and family, but would rather not broadcast this to everyone. WordPress provides for this scenario by allowing you to password-protect a post. Unfortunately, we've found through experience that a lot of our friends never make it past the password form. Either they mis-type the password, are confused about what it is, or are simply scared off by an intimidating form.

The solution: give password-protected posts a secret url that can be shared with friends and family. The url allows your select audience to see the content without the confusion and hassle of an authentication form, while hiding the special content from search spiders and the wider-world. You can revoke secret urls at any time, so if a secret url gets to someone you don't want it to, you can simply invalidate it.

= The Details =

The encoded tokens are made by taking the post-name and post-password and encoding them together. The plugin's admin page also allows you to create a "salt", or a unique key that makes the resulting encoded token more secure. Please note that once the salt option is set, changing it will change the secret urls for all posts. Unless you want to invalidate all of your old secret urls, it is recommended that you set the salt and leave it.

== Installation ==

1. Upload the `post-password-token` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin through its Admin page to customize the parameters used to create the tokens
4. When you create a password protected post you'll have an extra post-meta box that shows the URL that you can distribute

== Frequently Asked Questions ==

= So, what do I have to do? =

Not much. After you install the plugin all you have to do is set a password on a post using WordPress' standard [Password Protection Functionality](http://codex.wordpress.org/Content_Visibility). After the password is set and you've saved the post there will be a new meta box on the post edit screen (in the center content area, below the post-content editor) that will present you with a full URL to the post including the token. Distribute this URL to give people automatic access to your post.

= Does the normal password functionality still work? =

Yes, this still functions as normal.

Accessing a password-protected post by its standard url will still show the expected password dialog, but when a reader accesses a password-protected post by its secret Password Token url, they will be automatically authenticated and be able to see the full content.

= Do I have to do anything special to generate tokens for my old password-protected content? =

No. Since the tokens aren't stored but instead generated when needed there's no need to to do anything but navigate to the post edit page for your protected post to retrieve your token.

= How long does a secret url last? =

The url itself lasts forever, unless you change it. Additionally, accessing a post by its secret url will set an authentication cookie for the user that lasts for 10 days.

= How to I add a page anchor to my url? =

If you want to deep link in to a page, you can manually add the page anchor id to the end of the url before sending out your link, ie: `https://example.com/?p=5&ppt=04aa063124b672a0086fc68549a470a153b8324b776c265ff50c16640301a1f0#page-anchor`

= Can I have a single token for all protected content? =

Not yet.

= Can I set the post visibility on a per post basis? =

Not yet.

= How do I revoke a secret url? =

If you need to revoke the secret urls for an individual post, you can simply change the post password. Once you save the post it will create new secret urls and invalidate the old ones: the old url will no longer automatically log readers in and they'll be asked to enter a password if they use that URL.

You can also revoke all secret urls site-wide (the "nuclear" option). To do so, go to the plugin admin page and change the password salt.

= Does this work with caching plugins such as WP Super Cache? =

Yes. The token in the URL triggers a unique cache. Be aware that this does create a potential security risk. Not huge, but potential. If you don't want these pages cached then use the settings in your caching plugin to keep urls with `ppt=` in the url from being cached.

= I found a bug. Where can I submit it? =

If you've run into unexpected behavior while using the plugin, please file a bug report at https://wordpress.org/support/plugin/post-password-plugin/.

== Screenshots ==

1. The Post Password Token plugin admin screen.

== Changelog ==

= 2.0.3 =
* Fixed bug where protected posts weren't being filtered from `get_posts`, thus showing up in `wp:navigation` blocks.
** `get_posts` _still_ has no fine grained filters, so this update feels a bit heavy.
* Deprecated support for WP < 5.7

= 2.0.2 =
* Fixed a bug where the PPT Meta Box code would error on internal configuration pages where the Post wasn't completely formed
* Fix donation button application ID

= 2.0.1 =
* Include vendor directory to support autoloading
** A new autoloading scheme will be used in the future to avoid committing the vendor directory

= 2.0.0 =
* Fixes and updates to support WordPress 5.9
* Fixes and updates to support PHP 7.4+
* Support `sha256` as a hashing algorithm (default for new installs, old installs can upgrade from md5)
* Moderinized plugin code

= 1.2.4 =
* Fixed a bug that would use the wrong plugins_url when plugin was installed automatically via the wordpress admin
* Show post short url in the Post edit interface
* Removing reference to WP Help Center in contextual help
* General code cleanup

= 1.2.3 =
* Updated Post Password cookie with new hashed password for WP >= 3.4

= 1.2.2 =
* Added exclude for proper exclusion of pages when `wp_list_pages` is called
* Deprecated support for WP < 3.0

= 1.2.1 =
* Properly applied user levels check to admin menu item

= 1.2 = 
* Tested compatible with WordPress 3.0b2
* Added security check around menu item. Only users with `manage_options` can view the settings page
* Consolidated options in to single `wp_options` entry
* Don't show tokens for non-published posts
* Added support for WordPress 3.0 custom post types
* Upped version requirement to 2.8 - I no longer want to test versions prior to 2.8 ;)

= 1.1.1 = 
* Added more documentation
* Added WordPress Help Center badge
* Tested up to WordPress 3.0b1

= 1.1 =
* Adding the ability to hide protected posts from general view
* rearranged the admin options to separate salt & general options saving
* updated documentation

= 1.0.2 =
* Minor fixes to related URLs and version numbers

= 1.0.1 =
* Moving plugin info page off of GitHub
* Unifying page names in admin page calls
* Tested up to 2.8.5
* Moving all information & bug submission links away from GitHub

= 1.0 =
* initial release

== Upgrade Notice ==

= 2.0.0 =
* Users can now select `sha256` as a hashing algorithm, however this will obsolete existing urls. If you need backward compatability then continue using md5 as your hashing algorithm.

= 1.2.2 =
* Properly hides pages in lists generated by `wp_list_pages`

= 1.2 = 
* Adds support for WordPress 3 custom post types. This allows admin users to restrict the ability to create tokens for posts, pages and any custom post types.
* Updates admin security options. No longer provides access to settings page to users who do not have permissions to update options and protects against CSRF attacks.

= 1.1 =
* Version 1.1 adds a new feature that allows users to hide all password protected posts from appearing on the blog. The posts will still be available by direct access using the URL provided in the admin.
