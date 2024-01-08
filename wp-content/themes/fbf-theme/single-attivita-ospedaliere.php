<?php

get_header(); ?>

<?php
//IMPOSTO IL POST FULL WIDTH
$post_sidebar_show = !get_field('reparti');
?>
<?php if ($post_sidebar_show) { ?>
	<div class="block-6 no-mar content-with-sidebar">
		<div class="block-full bg-color-main">
		<?php } else { ?>
			<div class="block-full bg-color-main content-without-sidebar">
			<?php } ?>

			<div class="block-inner">
				<?php
				if (current_user_can('edit_post', $post->ID))
					edit_post_link(__('edit', 'om_theme'), '<div class="edit-post-link">[', ']</div>');
				?>

				<article>

					<div class="tbl-bottom">
						<div class="tbl-td">
							<h1 class="page-h1"><?php
												$format = get_post_format();
												if ($format == 'quote')
													echo '&ldquo;' . get_post_meta($post->ID, OM_THEME_SHORT_PREFIX . 'quote', true);
												else
													the_title();
												?></h1>
							<?php if ($format == 'quote') { ?><div class="clear"></div>
								<p class="post-title-comment" style="margin:0 0 3px 0">&mdash; <?php the_title(); ?></p><?php } ?>
						</div>
						<?php if (get_option(OM_THEME_PREFIX . 'show_breadcrumbs') == 'true') { ?>
							<div class="tbl-td">
								<?php om_breadcrumbs(get_option(OM_THEME_PREFIX . 'breadcrumbs_caption')) ?>
							</div>
						<?php } ?>
					</div>
					<div class="clear page-h1-divider"></div>

					<?php if (have_posts()) : ?>

						<?php echo get_option(OM_THEME_PREFIX . 'code_after_post_h1'); ?>

						<?php while (have_posts()) : the_post(); ?>

							<!-- INIZIO PERSONALIZZAZIONE CONTENUTO DELL'ATTIVITÀ OSPEDALIERA -->

							<div class="post-content post-attivita-ospedaliera">
								<div class="dettaglio-attivita-ospedaliera">

									<?php if (get_field('nome_responsabile')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="icona-attivita-ospedaliera">
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/036-doctor-3.svg" />
											</div>

											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento"><?= get_field('tipo_attivita_ospedaliera') == "Unità Operativa Complessa" ? "Direttore" : "Responsabile" ?></div>
												<div class="nome-responsabile riga-elemento">
													<div><?php the_field('nome_responsabile'); ?></div>
													<?php if (get_field('curriculum_responsabile')) : ?>
														<div class="curriculum_responsabile">
															<a href="<?php the_field('curriculum_responsabile'); ?>" target="_blank">
																Curriculum
															</a>
														</div>
													<?php endif; ?>
												</div>
												<div style="clear:both;"></div>
												<div class="contatti-responsabile riga-elemento">
													<?php the_field('telefono_responsabile');
													echo " ";
													echo get_field('email_responsabile') ? "<a href=\"mailto:".get_field('email_responsabile')."\">".get_field('email_responsabile')."</a>" : ""; ?>
												</div>
											</div>
										</div>
									<?php endif; ?>
									<?php if (get_field('equipe_medica')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="icona-equipe icona-attivita-ospedaliera">
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/050-groups.svg" />
											</div>

											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento">Dirigenti Medici e Sanitari</div>
												<div class="elenco-equipe">
													<?php
													$elencoMedici = get_field('equipe_medica');


													foreach ($elencoMedici as $medico) {
														$infoMedico = get_term_by('id', $medico, 'medici');
													?>
														<div class="medico"><?= $infoMedico->name; ?></div>
													<?php
													}

													?></div>
											</div>
										</div>
									<?php endif; ?>
									<?php if (get_field('descrizione_attivita_ospedaliera')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento">Descrizione attività ospedaliera</div>
												<div class="testo-attivita-ospedaliera"><?php nl2br(the_field('descrizione_attivita_ospedaliera')); ?></div>
											</div>
										</div>
									<?php endif; ?>
									<?php if (get_field('informazioni_per_lutente')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento">Informazioni per l'utente</div>
												<div class="informazioni-utente"><?php nl2br(the_field('informazioni_per_lutente')); ?></div>
											</div>
										</div>
									<?php endif; ?>
									<?php if (get_field('materiale_informativo') or get_field('materiale_informativo_copia') or get_field('materiale_informativo_3')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento">Materiale informativo</div>
												<div class="materiale-informativo">

													<?php $labelMaterialeInformativo = array('materiale_informativo', 'materiale_informativo_copia', 'materiale_informativo_3');
													foreach ($labelMaterialeInformativo as $label) {
														if (get_field($label)) {
															$nomeDocumento = get_the_title(get_field($label));
													?>
															<div class="elemento-materiale-informativo">
																<a href="<?php echo wp_get_attachment_url(get_field($label)); ?>" target="_blank" title="<?= $nomeDocumento; ?>">
																	<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/003-medical-report.svg" />
																	<div class="nome-materiale-informativo"><?= $nomeDocumento; ?></div>
																</a>
															</div>
													<?php
														}
													}
													?>
												</div>
											</div>
										</div>
									<?php endif; ?>

									<?php
									$args = array(
										'post_type' => 'post',
										'meta_query' => array(
											array(
												'key' => 'articolo-attivita-ospedaliera',
												'value' => $post->ID,
												'compare' => 'LIKE'
											)
										),
										'orderby' => 'date',
										'order'   => 'DESC',
										'posts_per_page' => '4',
									);

									$elenco_articoli_in_home = new WP_Query($args);
									if ($elenco_articoli_in_home->have_posts()) :
									?>
										<div class="elemento-attivita-ospedaliera">
											<div class="contenuto-elemento">
												<div class="descrizione-attivita-ospedaliera">
													<?php
													set_query_var('elenco_articoli', $elenco_articoli_in_home);
													get_template_part('template-part/notizie-attivita-ospedaliera');
													?>
												</div>
											</div>
										</div>
									<?php endif; ?>

									<?php if (get_field('servizi_afferenti')) : ?>
										<div class="elemento-attivita-ospedaliera">
											<div class="contenuto-elemento">
												<div class="titolo-contenuto-elemento">Servizi Afferenti</div>
												<div class="servizi-afferenti">
													<?php $elencoServiziAfferenti = get_field('servizi_afferenti');
													foreach ($elencoServiziAfferenti as $servizioAfferente) {
													?>
														<div class="servizio-afferente" id="<?php echo $servizioAfferente->ID; ?>">
															<div class="icona-servizio-afferente">
																<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/attivita.svg" />
															</div>
															<div class="nome-servizio-afferente">
																<?php the_field('nome_servizio_afferente', $servizioAfferente); ?>
															</div>

														</div>
													<?php
													}
													?>
												</div>

												<?php foreach ($elencoServiziAfferenti as $servizioAfferente) { ?>
													<!-- RIQUADRO SERVIZIO CHE SI APRE AL CLICK -->
													<div class="dettaglio-servizio-afferente" id="servizio<?= $servizioAfferente->ID ?>">
														<div class="chiudi-servizio-afferente">X</div>

														<div class="titolo-servizio-afferente">
															<?php the_field('nome_servizio_afferente', $servizioAfferente); ?>
														</div>

														<?php if (get_field('nome_responsabile', $servizioAfferente)) : ?>
															<div class="elemento-attivita-ospedaliera">

																<div class="icona-attivita-ospedaliera">
																	<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/036-doctor-3.svg" />
																</div>

																<div class="contenuto-elemento">
																	<div class="titolo-contenuto-elemento">Responsabile</div>
																	<div class="nome-responsabile riga-elemento">
																		<div><?php the_field('nome_responsabile', $servizioAfferente); ?></div>
																		<?php if (get_field('curriculum_responsabile', $servizioAfferente)) : ?>
																			<div class="curriculum_responsabile">
																				<a href="<?php the_field('curriculum_responsabile', $servizioAfferente); ?>" target="_blank">
																					Curriculum
																				</a>
																			</div>
																		<?php endif; ?>
																	</div>
																	<div style="clear:both;"></div>
																	<div class="contatti-responsabile riga-elemento">
																		<?php the_field('telefono_responsabile', $servizioAfferente);
																		echo " ";
																		echo get_field('email_responsabile', $servizioAfferente) ?"<a href=\"mailto:".get_field('email_responsabile', $servizioAfferente)."\">".get_field('email_responsabile', $servizioAfferente)."</a>" : ""; ?>
																	</div>
																</div>
															</div>
														<?php endif; ?>

														<?php if (get_field('descrizione_servizio_afferente', $servizioAfferente)) : ?>
															<div class="elemento-attivita-ospedaliera">
																<div class="contenuto-elemento">
																	<div class="titolo-contenuto-elemento">Descrizione servizio</div>
																	<div class="descrizione-attivita-ospedaliera descrizione-servizio-afferente"><?php nl2br(the_field('descrizione_servizio_afferente', $servizioAfferente)) ?></div>
																</div>
															</div>
														<?php endif; ?>
														<?php if (get_field('informazione_per_lutente', $servizioAfferente)) : ?>
															<div class="elemento-attivita-ospedaliera">
																<div class="contenuto-elemento">
																	<div class="titolo-contenuto-elemento">Informazioni per l'utente</div>
																	<div class="informazioni-utente"><?php nl2br(the_field('informazione_per_lutente', $servizioAfferente)); ?></div>
																</div>
															</div>
														<?php endif; ?>
														<?php if (get_field('materiale_informativo', $servizioAfferente) or get_field('materiale_informativo_2', $servizioAfferente) or get_field('materiale_informativo_3', $servizioAfferente)) : ?>
															<div class="elemento-attivita-ospedaliera">
																<div class="contenuto-elemento">
																	<div class="titolo-contenuto-elemento">Materiale informativo</div>
																	<div class="materiale-informativo">

																		<?php $labelMaterialeInformativo = array('materiale_informativo', 'materiale_informativo_2', 'materiale_informativo_3');
																		foreach ($labelMaterialeInformativo as $label) {
																			if (get_field($label, $servizioAfferente)) {
																				$nomeDocumento = get_the_title(get_field($label, $servizioAfferente));
																		?>
																				<div class="elemento-materiale-informativo">
																					<a href="<?php echo wp_get_attachment_url(get_field($label, $servizioAfferente)); ?>" target="_blank" title="<?= $nomeDocumento; ?>">
																						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/documenti.svg" />
																						<div class="nome-materiale-informativo"><?= $nomeDocumento; ?></div>
																					</a>
																				</div>
																		<?php
																			}
																		}
																		?>
																	</div>
																</div>
															</div>
														<?php endif; ?>
													</div>
													<!-- FINE RIQUADRO SERVIZIO AFFERENTE -->
												<?php
												}
												?>
											</div>
										</div>
									<?php endif; ?>


									<?php
									$elencoAmbulatori = get_field('ambulatori');
									if ($elencoAmbulatori) {
									?>
										<div class="elenco-riquadri-ambulatorio">
											<?php

											foreach ($elencoAmbulatori as $ambulatorio) {
											?>
												<div class="elemento-attivita-ospedaliera ambulatorio-attivita-ospedaliera" id="riquadro-ambulatorio-<?= $ambulatorio; ?>">
													<div class="contenuto-elemento">
														<?php
														set_query_var('ambulatorio', $ambulatorio);
														get_template_part('template-part/elemento-ambulatorio');

														?>
													</div>
												</div>
											<?php }

											?>
										</div>
									<?php
										wp_reset_query();
										get_template_part('template-part/informazioni-prenotazione');
									} ?>
								</div>

								<?php
								$elencoReparti = get_field('reparti');
								if ($elencoReparti) {
								?>

									<div class="elenco-reparti">
										<div class="dettaglio-reparto">
											<?php
											foreach ($elencoReparti as $idReparto) {
												/* INIZIO RIQUADRO SINGOLO REPARTO */
												set_query_var('idReparto', $idReparto);
												get_template_part('template-part/elemento-reparto');
												/* FINE RIQUADRO SINGOLO REPARTO */
											}
											?>
										</div>
										<?php
										if (get_option('fbf_settings')['fbf_url_informazioni_ricovero']) {
										?>
											<div class="informazioni">
												<a href="<?= get_option('fbf_settings')['fbf_url_informazioni_ricovero'] ?>" class="pulsante-informazioni">
													<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/052-info-white.svg" />
													<div>Informazioni per il ricovero</div>
												</a>
											</div>
										<?php
										}
										?>
									</div>
								<?php } ?>

							</div>
							<!-- FINE PERSONALIZZAZIONE CONTENUTO DELL'ATTIVITÀ OSPEDALIERA -->


						<?php endwhile; ?>


						<?php echo get_option(OM_THEME_PREFIX . 'code_after_post_content'); ?>



					<?php else : ?>

						<h2><?php _e('Error 404 - Not Found', 'om_theme') ?></h2>

						<p><?php _e('Sorry, but you are looking for something that isn\'t here.', 'om_theme') ?></p>

					<?php endif; ?>

				</article>

			</div>

			</div>

			<?php
			$fb_comments = false;
			if (function_exists('om_facebook_comments') && get_option(OM_THEME_PREFIX . 'fb_comments_posts') == 'true') {
				if (get_option(OM_THEME_PREFIX . 'fb_comments_position') == 'after')
					$fb_comments = 'after';
				else
					$fb_comments = 'before';
			}
			?>

			<?php if ($fb_comments == 'before') {
				om_facebook_comments();
			} ?>

			<?php if (get_option(OM_THEME_PREFIX . 'hide_comments_post') != 'true') : ?>
				<?php comments_template('', true); ?>
			<?php endif; ?>

			<?php if ($fb_comments == 'after') {
				om_facebook_comments();
			} ?>

			<?php if ($post_sidebar_show) { ?>


		</div>

		<div class="block-3 no-mar sidebar">
			<?php
				// alternative sidebar
				$alt_sidebar = intval(get_post_meta($post->ID, OM_THEME_SHORT_PREFIX . 'sidebar', true));
				if ($alt_sidebar && $alt_sidebar <= intval(get_option(OM_THEME_PREFIX . "sidebars_num"))) {
					if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('alt-sidebar-' . $alt_sidebar));
					dynamic_sidebar("Main Alternative Sidebar 4");
				} else {
					dynamic_sidebar("Main Alternative Sidebar 4");
				}
			?>
		</div>
	<?php } ?>


	<!-- /Content -->

	<div class="clear anti-mar">&nbsp;</div>



	<?php get_footer(); ?>