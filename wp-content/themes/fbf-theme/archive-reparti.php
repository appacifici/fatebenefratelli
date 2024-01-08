<?php

get_header(); ?>

<div class="block-6 no-mar content-with-sidebar">

	<div class="block-6 bg-color-main">
		<div class="block-inner">
			<div class="tbl-bottom">
				<div class="tbl-td">
					<h1 class="page-h1"><?php post_type_archive_title(); ?></h1>
				</div>
				<?php if (get_option(OM_THEME_PREFIX . 'show_breadcrumbs') == 'true') { ?>
					<div class="tbl-td">
						<?php om_breadcrumbs(get_option(OM_THEME_PREFIX . 'breadcrumbs_caption')) ?>
					</div>
				<?php } ?>
			</div>
			<div class="clear page-h1-divider"></div>

			<?php
			$args = array(
				'post_type' => array(
					'reparti',
				),
				'posts_per_page' => -1,
				'order' => 'ASC',
				'orderby'   => 'meta_value',
				'meta_key'  => 'nome_reparto',
				'max_num_pages' => 1
			);

			$elenco_reparti = new WP_Query($args);
			if ($elenco_reparti->have_posts()) :
			?>
				<section class="archivio-elenco-reparti">
					<?php while ($elenco_reparti->have_posts()) : $elenco_reparti->the_post(); ?>
						
							<?php
							get_template_part('template-part/archivio-reparto');
							?>
						
					<?php endwhile; ?>
				</section>
			<?php endif; ?>

		</div>
	</div>

</div>


<div class="block-3 no-mar sidebar">
	<?php
	dynamic_sidebar('alt-sidebar-4');
	?>
</div>

<!-- /Content -->

<div class="clear anti-mar">&nbsp;</div>


<?php get_footer(); ?>