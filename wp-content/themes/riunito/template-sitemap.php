<?php
/*
Template Name: Sitemap
*/

get_header(); ?>

		<div class="block-full bg-color-main content-without-sidebar">
			<div class="block-inner">
				<?php
          if ( current_user_can( 'edit_post', $post->ID ) )
      	    edit_post_link( __('edit', 'om_theme'), '<div class="edit-post-link">[', ']</div>' );
    		?>
				<div class="tbl-bottom">
					<div class="tbl-td">
						<h1 class="page-h1"><?php the_title(); ?></h1>
					</div>
					<?php if(get_option(OM_THEME_PREFIX . 'show_breadcrumbs') == 'true') { ?>
						<div class="tbl-td">
							<?php om_breadcrumbs(get_option(OM_THEME_PREFIX . 'breadcrumbs_caption')) ?>
						</div>
					<?php } ?>
				</div>
				<div class="clear page-h1-divider"></div>
      		
          <?php echo get_option(OM_THEME_PREFIX . 'code_after_page_h1'); ?>
          
          <div class="sitemap">

						<div class="one-half">
	
					
						
						<?php $list=wp_list_pages('title_li=&echo=0'); ?>
						<?php if($list) : ?>
							<h3><?php _e('Pages','om_theme'); ?></h3>
							<ul>
								<?php echo $list ?>
							</ul>
						<?php endif; ?>
						
						</div>
						
						<div class="one-third">
	
							<?php $list=get_posts('numberposts=-1&orderby=title&order=ASC'); ?>
							<?php if(!empty($list)) : ?>
								<h3><?php _e('Posts','om_theme'); ?></h3>
								<ul>
									<?php
										foreach($list as $item) {
											echo '<li><a href="'. get_permalink($item->ID) .'">'.$item->post_title.'</a></li>';
										}
									?>
								</ul>
							<?php endif; ?>			
	
							
							
							<?php $list=wp_get_archives('type=monthly&echo=0'); ?>
							<?php if($list) : ?>
								<h3><?php _e('Monthly Archives','om_theme'); ?></h3>
								<ul>
									<?php echo $list ?>
								</ul>
							<?php endif; ?>
							
																
						</div>
							
						
						<div class="clear"></div>
						
					</div>
					
					
					<?php echo get_option(OM_THEME_PREFIX . 'code_after_page_content'); ?>
					
			</div>
		</div>
		
		<?php if(get_option(OM_THEME_PREFIX . 'hide_comments_pages') != 'true') : ?>
			<?php comments_template('',true); ?>
		<?php endif; ?>		
		
		<!-- /Content -->
		
		<div class="clear anti-mar">&nbsp;</div>

<?php get_footer(); ?>