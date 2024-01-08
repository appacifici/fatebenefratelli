<?php
/**
 * Settings Page Doc Comment
 *
 * @category  Views
 * @package   gdpr-cookie-compliance
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="gdpr-cookie-compliance-header-section">
	<h2><?php esc_html_e( 'GDPR Cookie Compliance Plugin (CCPA ready)', 'gdpr-cookie-compliance' ); ?> <span class="gdpr-plugin-version"><?php echo 'v' . esc_attr( MOOVE_GDPR_VERSION ); ?></span></h2>
	<h4>
		<?php
			$content = __( 'This plugin is useful in preparing your site for cookie compliance, data protection and privacy regulations.', 'gdpr-cookie-compliance' );
			apply_filters( 'gdpr_cc_keephtml', $content, true );
		?>
		<br> 
	</h4>
</div>
<!--  .gdpr-header-section -->
<div class="wrap moove-clearfix" id="moove_form_checker_wrap">
	<h1></h1>
	<div id="moove-gdpr-setting-error-settings_updated" class="updated settings-error notice is-dismissible gdpr-cc-notice" style="display:none;">
		<p><strong><?php esc_html_e( 'Settings saved.', 'gdpr-cookie-compliance' ); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'gdpr-cookie-compliance' ); ?></span>
		</button>
	</div>

	<?php do_action( 'gdpr_premium_update_alert' ); ?>

	<div id="moove-gdpr-setting-error-settings_scripts_empty" class="error settings-error notice is-dismissible gdpr-cc-notice" style="display:none;">
		<p>
			<strong><?php esc_html_e( 'You need to insert the relevant script for the settings to be saved.', 'gdpr-cookie-compliance' ); ?></strong>
		</p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'gdpr-cookie-compliance' ); ?></span>
		</button>
	</div>

	<?php

	$gdpr_default_content = new Moove_GDPR_Content();
	wp_verify_nonce( 'gdpr_nonce', 'gdpr_cookie_compliance_nonce' );
	$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
	if ( isset( $current_tab ) && '' !== $current_tab ) :
		$active_tab = $current_tab;
	else :
		$active_tab = 'branding';
	endif; // end if.
	$option_name   = $gdpr_default_content->moove_gdpr_get_option_name();
	$modal_options = get_option( $option_name );
	$wpml_lang     = $gdpr_default_content->moove_gdpr_get_wpml_lang('label');
	if ( $wpml_lang ) :
		$_current_user       = wp_get_current_user();
		$show_language_alert = isset( $modal_options[ 'gdpr_hide_language_notice_' . $_current_user->ID ] ) && 1 === intval( $modal_options[ 'gdpr_hide_language_notice_' . $_current_user->ID ] ) ? 'display: none;' : 'display: inline-block;';
		?>
		<div class="gdpr-cookie-alert gdpr-cookie-alert-dark" style="<?php echo esc_attr( $show_language_alert ); ?>">
			<p>
				<?php esc_html_e( 'We have detected that you have a multi-language setup.', 'gdpr-cookie-compliance' ); ?>
				<?php /* translators: %s: version number */ ?>
				<?php printf( esc_html__( 'You are currently editing: %s version', 'gdpr-cookie-compliance' ), '<strong>' . esc_attr( gdpr_get_display_language_by_locale( str_replace( '_', '', $wpml_lang ) ) ) . '</strong>' ); ?>
				<?php do_action( 'gdpr_language_alert_bottom', $wpml_lang ); ?>
			<span class="gdpr-dismiss" title="<?php esc_html_e( 'dismiss notice', 'gdpr-cookie-compliance' ); ?>" data-nonce="<?php echo wp_create_nonce('gdpr_hide_language_nonce') ?>" data-adminajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-uid="<?php echo intval( $_current_user->ID ); ?>">×</span>
		</div>
		<!--  .gdpr-cookie-alert -->
	<?php endif; ?>
	<br />
	<div class="gdpr-tab-section-cnt <?php echo implode( ' ', apply_filters('gdpr_tab_section_cnt_class', array() ) ); ?>">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=branding' ) ); ?>" class="nav-tab <?php echo 'branding' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Branding', 'gdpr-cookie-compliance' ); ?>
			</a>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=banner-settings' ) ); ?>" class="nav-tab <?php echo 'banner-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Cookie Banner Settings', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=screen-settings' ) ); ?>" class="nav-tab <?php echo 'screen-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Cookie Settings Screen', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=floating-button' ) ); ?>" class="nav-tab <?php echo 'floating-button' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Floating Button', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=general-settings' ) ); ?>" class="nav-tab <?php echo 'general-settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'General Settings', 'gdpr-cookie-compliance' ); ?>
			</a>
			
			<?php
				$nav_label = isset( $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_privacy_overview_tab_title' . $wpml_lang ] : __( 'Privacy Overview', 'gdpr-cookie-compliance' );
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=privacy-overview' ) ); ?>" class="nav-tab <?php echo 'privacy-overview' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_attr( $nav_label ); ?>
			</a>
			<?php
			$nav_label = isset( $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_strictly_necessary_cookies_tab_title' . $wpml_lang ] : __( 'Strictly Necessary Cookies', 'gdpr-cookie-compliance' );
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=strictly-necessary-cookies' ) ); ?>" class="nav-tab <?php echo 'strictly-necessary-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_attr( $nav_label ); ?>
			</a>

			<?php
			$nav_label = isset( $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_performance_cookies_tab_title' . $wpml_lang ] : __( '3rd Party Cookies', 'gdpr-cookie-compliance' );
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=third-party-cookies' ) ); ?>" class="nav-tab <?php echo 'third-party-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_attr( $nav_label ); ?>
			</a>

			<?php
			$nav_label = isset( $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_advanced_cookies_tab_title' . $wpml_lang ] : __( 'Additional Cookies', 'gdpr-cookie-compliance' );
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=advanced-cookies' ) ); ?>" class="nav-tab <?php echo 'advanced-cookies' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_attr( $nav_label ); ?>
			</a>
			<?php
			$nav_label = isset( $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ) && $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] ? $modal_options[ 'moove_gdpr_cookie_policy_tab_nav_label' . $wpml_lang ] : __( 'Cookie Policy', 'gdpr-cookie-compliance' );
			?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=cookie-policy' ) ); ?>" class="nav-tab <?php echo 'cookie-policy' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_attr( $nav_label ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=help' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'help' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<span class="dashicons dashicons-book"></span>
				<?php esc_html_e( 'Documentation', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=video-tutorial' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'video-tutorial' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<span class="dashicons dashicons-format-video"></span>
				<?php esc_html_e( 'Video Tutorial', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=support' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'support' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<span class="dashicons dashicons-sos"></span>
				<?php esc_html_e( 'Support', 'gdpr-cookie-compliance' ); ?>
			</a>

			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=moove-gdpr&amp;tab=licence' ) ); ?>" class="nav-tab nav-tab-dark <?php echo 'licence' === $active_tab ? 'nav-tab-active' : ''; ?>">
				<span class="dashicons dashicons-admin-network"></span>
				<?php esc_html_e( 'Licence Manager', 'gdpr-cookie-compliance' ); ?>
			</a>

			<?php do_action( 'gdpr_settings_tab_nav_extensions', $active_tab ); ?>
		</h2>

		<div class="moove-gdpr-form-container <?php echo esc_attr( $active_tab ); ?>">
			<?php
				$view_cnt = new GDPR_View();
				$tab_data = $view_cnt->load( 'moove.admin.settings.' . $active_tab, array() );
				$content  = apply_filters( 'gdpr_settings_tab_content', $tab_data, $active_tab );
				apply_filters( 'gdpr_cc_keephtml', $content, true );
			?>
		</div>
		<!-- moove-form-container -->
	</div>
	<!--  .gdpr-tab-section-cnt -->
	<?php
		$view_cnt = new GDPR_View();
		$content  = $view_cnt->load( 'moove.admin.settings.plugin-boxes', array() );
		apply_filters( 'gdpr_cc_keephtml', $content, true );
	?>
	<div class="moove-clearfix"></div>
	<!--  .moove-clearfix -->
	<div class="moove-gdpr-settings-branding">
		<hr />
	</div>
	<!--  .moove-gdpr-settings-branding -->
</div>
<!-- .wrap -->


