		<?php
			$footer_left = get_option( OM_THEME_PREFIX.'footer_text_left' );
			
			$icons_html='';
			if(get_option(OM_THEME_PREFIX.'socicons_position') != 'header') {
				$icons_html = om_get_social_icons_html();
			}

			$is_footer_sidebars = ( is_active_sidebar('footer-column-left') || is_active_sidebar('footer-column-center') || is_active_sidebar('footer-column-right') );
			$is_sub_footer = ($footer_left || $icons_html);
			if($is_footer_sidebars || $is_sub_footer) {
		?>
		
			<!-- Footer -->
			
			<footer>
			<div class="footer block-full bg-color-footer">
				<div class="eat-outer-margins">
					
					<?php if ($is_footer_sidebars) { ?>
						<div class="block-3">
							<div class="block-inner widgets-area">
								<?php get_sidebar('footer-column-left'); ?>
							</div>
						</div>
						
						<div class="block-3">
							<div class="block-inner widgets-area">
								<?php get_sidebar('footer-column-center'); ?>
							</div>
						</div>
						
						<div class="block-3">
							<div class="block-inner widgets-area">
								<?php get_sidebar('footer-column-right'); ?>
							</div>
						</div>
			
						<div class="clear"></div>
					<?php } ?>

					<?php if($is_footer_sidebars && $is_sub_footer) { ?>
						<div class="block-full"><div class="block-inner" style="padding-top:0;padding-bottom:0"><div class="sub-footer-divider"></div></div></div>
					<?php } ?>
					
					<?php if($is_sub_footer) { ?>
						<!-- SubFooter -->
						<div class="block-full sub-footer">
							<div class="block-inner">
								<div class="block-full">&copy; Copyright <?php echo date("Y"); ?> - <?php echo ($footer_left?$footer_left:'&nbsp;') ?></div>
								<div class="clear"></div>
							</div>
						</div>
						
						<!-- /SubFooter -->
					<?php } ?>
		
					
				</div>
			</div>
			</footer>
			
			<!-- /Footer -->

		<?php } ?>
		

	</div>
<script>
(function () {
					 var s = document.createElement('script');
					 s.type = 'text/javascript';
					 s.async = true;
					 s.src='https://www.googletagmanager.com/gtag/js?id=G-9XF2R1REP5' ;
					 var sc = document.getElementsByTagName('script')[0];
					 sc.parentNode.insertBefore(s, sc);
				   })();

					 window.dataLayer = window.dataLayer || [];
			  function gtag(){dataLayer.push(arguments);}
			  gtag('js', new Date());

			  gtag('config', 'G-9XF2R1REP5');	</script>
	
	<?php wp_footer(); ?>
	
	<?php echo get_option( OM_THEME_PREFIX . 'code_before_body' ) ?>
</div>	
</body>
</html>
