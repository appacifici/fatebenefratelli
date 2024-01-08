    <div class="riquadro-reparto" id="riquadro-reparto-<?= $idReparto; ?>"">
        <div class=" nome-reparto"><?= get_the_title($idReparto); ?></div>
    <?php if (get_field('coordinatore_infermieristico', $idReparto)) : ?>
        <div class="elemento-attivita-ospedaliera">
            <div class="icona-attivita-ospedaliera">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/035-nurse.svg" />
            </div>

            <div class="contenuto-elemento">
                <div class="titolo-contenuto-elemento">Coordinatore</div>
                <div class="nome-coordinatore riga-elemento">
                    <div><?php the_field('coordinatore_infermieristico', $idReparto); ?></div>
                </div>
                <div class="contatti-coordinatore riga-elemento">
                    <div><?php echo get_field('email_coordinatore_infermieristico',$idReparto)?"<a href=\"mailto:".get_field('email_coordinatore_infermieristico', $idReparto)."\">".get_field('email_coordinatore_infermieristico', $idReparto)."</a>":""; ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (get_field('Nome Reparto Fisico', $idReparto) or get_field('ubicazione_reparto', $idReparto)) : ?>
        <div class="elemento-attivita-ospedaliera">
            <div class="icona-attivita-ospedaliera">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/038-hospital-3.svg" />
            </div>

            <div class="contenuto-elemento">
                <div class="titolo-contenuto-elemento">Ubicazione</div>
                <div class="nome-santo riga-elemento">
                    <div><?php the_field('Nome Reparto Fisico', $idReparto); ?></div>
                </div>
                <div class="ubicazione-reparto riga-elemento">
                    <div><?php the_field('ubicazione_reparto', $idReparto); ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (get_field('telefono_coordinatore_infermieristico', $idReparto)) : ?>
        <div class="elemento-attivita-ospedaliera">
            <div class="icona-attivita-ospedaliera">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/046-telephone.svg" />
            </div>
            <div class="contenuto-elemento">
                <div class="titolo-contenuto-elemento">Recapiti</div>
                <div class="telefono-coordinatore-infermieristico riga-elemento">
                    <div><?php the_field('telefono_coordinatore_infermieristico', $idReparto); ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (get_field('orari_di_visita_lunedi_sabato', $idReparto) or get_field('orari_di_visita_domenica_e_festivi', $idReparto)) : ?>
        <div class="elemento-attivita-ospedaliera">
            <div class="icona-attivita-ospedaliera">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/048-clock.svg" />
            </div>
            <div class="contenuto-elemento">
                <div class="titolo-contenuto-elemento">Visite</div>
                <div class="orari-di-visita riga-elemento">
                    <?php if (get_field('orari_di_visita_lunedi_sabato', $idReparto)) : ?>
                        <div class="giorni-visita">Dal Lunedì al Sabato</div>
                        <div class="elenco-orari-di-visita">
                            <?php
                            $elencoOrari = explode(";", get_field('orari_di_visita_lunedi_sabato', $idReparto));
                            foreach ($elencoOrari as $orario) {
                            ?>
                                <div class="singolo-orario">
                                    <?= trim($orario) ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                </div>
            <?php endif; ?>
            <?php if (get_field('orari_di_visita_domenica_e_festivi', $idReparto)) : ?>
                <div class="orari-di-visita riga-elemento">
                    <div class="giorni-visita">Domenica e Festivi</div>
                    <div class="elenco-orari-di-visita">
                        <?php
                        $elencoOrari = explode(";", get_field('orari_di_visita_domenica_e_festivi', $idReparto));
                        foreach ($elencoOrari as $orario) {
                        ?>
                            <div class="singolo-orario">
                                <?= trim($orario) ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php
    $idFotoReparto = (get_field('foto_reparto', $idReparto)) ?: "";
    if ($idFotoReparto) {
    ?>
        <div class="elemento-attivita-ospedaliera">
            <?php
            echo wp_get_attachment_image($idFotoReparto, 'full');
            ?>
        </div>
    <?php } ?>
    </div>