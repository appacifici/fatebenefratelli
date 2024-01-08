<?php

get_header(); ?>

<div class="block-6 no-mar content-with-sidebar">

	<div class="block-6 bg-color-main">
		<div class="block-inner">
			<div class="tbl-bottom">
				<div class="tbl-td">
					<h1 class="page-h1">
					<?= isset(get_option( "fbf_settings")['fbf_nome_attivita_sanitaria'])?get_option( "fbf_settings")['fbf_nome_attivita_sanitaria']: "Attività Ospedaliere"; ?></h1>
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
					'attivita-ospedaliere',
				),
				'posts_per_page' => -1,
				'order' => 'ASC',
				'orderby'   => 'meta_value',
				'meta_key'  => 'nome_attivita',
				'max_num_pages' => 1
			);

			$elenco_attivita = new WP_Query($args);
			if ($elenco_attivita->have_posts()) :
			?>
				<section class="elenco-attivita-ospedaliere">
					<?php while ($elenco_attivita->have_posts()) : $elenco_attivita->the_post(); ?>
						<article>
							<?php
							get_template_part('template-part/archivio-attivita');

							?>
						</article>
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