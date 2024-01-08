<?php
get_header(); ?>


		<div class="block-6 no-mar content-with-sidebar">
			<div class="block-6 bg-color-main">
				<div class="block-inner">
					<div class="tbl-bottom">
						<div class="tbl-td">
							<h1 class="page-h1"><?php _e('Search', 'om_theme') ?></h1>
						</div>
						<?php if(get_option(OM_THEME_PREFIX . 'show_breadcrumbs') == 'true') { ?>
							<div class="tbl-td">
								<?php om_breadcrumbs(get_option(OM_THEME_PREFIX . 'breadcrumbs_caption')) ?>
							</div>
						<?php } ?>
					</div>
					<div class="clear page-h1-divider"></div>
<?php 
							// set page to load all returned results
							global $query_string;
							query_posts( $query_string . '&posts_per_page=-1' );
							if( have_posts() ) : 
							?>
								<p><?php printf( __('Search Results for: &ldquo;%s&rdquo;', 'om_theme'), get_search_query()); ?></p>

                <div class="search_attivita">
                    <h2><?php _e('Attività Ospedaliere', 'om_theme'); ?></h2>
                <div class="elenco-attivita-ospedaliere">
               			<?php 
                    rewind_posts();
                    $found = false;
                    while( have_posts() ) {
                    	the_post();
	                    if( $post->post_type == 'attivita-ospedaliere' ) {
												$found=true;
							get_template_part('template-part/archivio-attivita');
												// printf('<li><h4><a href="%1$s">%2$s</a></h4><p>%3$s</p></li>', get_permalink(), get_the_title(), get_the_excerpt()); 
	                    }
                    }
                    if( !$found ) { printf('<li>%s</li>', __('No pages match the search terms', 'om_theme')); }
                		?>
                </div>
                </div>

                <div class="search_reparti">
                    <h2><?php _e('Reparti', 'om_theme'); ?></h2>
                <div class="archivio-elenco-reparti">

               			<?php 
                    rewind_posts();
                    $found = false;
                    while( have_posts() ) {
                    	the_post();
	                    if( $post->post_type == 'reparti' ) {
							get_template_part('template-part/archivio-reparto');
												$found=true;
												// printf('<li><h4><a href="%1$s">%2$s</a></h4><p>%3$s</p></li>', get_permalink(), get_the_title(), get_the_excerpt()); 
	                    }
                    }
                    if( !$found ) { printf('<li>%s</li>', __('No pages match the search terms', 'om_theme')); }
                		?>
                </div>
                </div>
				
                <div class="search_ambulatori">
                    <h2><?php _e('Ambulatori', 'om_theme'); ?></h2>
                <div class="archivio-elenco-ambulatori">

               			<?php 
                    rewind_posts();
                    $found = false;
                    while( have_posts() ) {
                    	the_post();
	                    if( $post->post_type == 'ambulatori' ) {
							get_template_part('template-part/archivio-ambulatorio');
												$found=true;
												// printf('<li><h4><a href="%1$s">%2$s</a></h4><p>%3$s</p></li>', get_permalink(), get_the_title(), get_the_excerpt()); 
	                    }
                    }
                    if( !$found ) { printf('<li>%s</li>', __('No pages match the search terms', 'om_theme')); }
                		?>
                </div>
                </div>

                <div class="search_posts">
                    <h2><?php _e('Posts', 'om_theme'); ?></h2>
                    <ul>
										<?php 
										$found = false;
										while( have_posts() ) {
											the_post(); 
							        if( $post->post_type == 'post' ) {
							            $found=true;
							            printf('<li><h4><a href="%1$s">%2$s</a></h4><p>%3$s</p></li>', get_permalink(), get_the_title(), get_the_excerpt()); 
							        }
								    }
								    if( !$found ) { printf('<li>%s</li>', __('No posts match the search terms', 'om_theme')); }
										?>
                    </ul>
                </div>
                
                <div class="search_pages">
                    <h2><?php _e('Pages', 'om_theme'); ?></h2>
                    <ul>
               			<?php 
                    rewind_posts();
                    $found = false;
                    while( have_posts() ) {
                    	the_post();
	                    if( $post->post_type == 'page' ) {
												$found=true;
												printf('<li><h4><a href="%1$s">%2$s</a></h4><p>%3$s</p></li>', get_permalink(), get_the_title(), get_the_excerpt()); 
	                    }
                    }
                    if( !$found ) { printf('<li>%s</li>', __('No pages match the search terms', 'om_theme')); }
                		?>
                    </ul>
                </div>

        				<?php else : ?>
	
									<p><?php printf( __('Your search for <em>"%s"</em> did not match any entries','om_theme'), get_search_query() ); ?></p>

	        				<?php get_search_form(); ?>
	        				<p><?php _e('Suggestions:','om_theme') ?></p>
	        				<ul>
	        					<li><?php _e('Make sure all words are spelled correctly.', 'om_theme') ?></li>
	        					<li><?php _e('Try different keywords.', 'om_theme') ?></li>
	        					<li><?php _e('Try more general keywords.', 'om_theme') ?></li>
	        				</ul>
	
  			      	<?php endif; ?>
	      							
				</div>
			</div>

		</div>


		<div class="block-3 no-mar sidebar">
			<?php	get_sidebar(); ?>
		</div>
		
		<!-- /Content -->
		
		<div class="clear anti-mar">&nbsp;</div>
		

		
<?php get_footer(); ?>