<?php $ambulatorioID = get_query_var('ambulatorio');
?>
<div class="titolo-contenuto-elemento">Servizi Ambulatoriali - <?= get_the_title($ambulatorioID); ?></div>
<?php if (get_field('ubicazione', $ambulatorioID)) : ?>
    <div class="elemento-attivita-ospedaliera">
        <div class="icona-attivita-ospedaliera">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/037-hospital-2.svg" />
        </div>

        <div class="contenuto-elemento">
            <div class="titolo-contenuto-elemento">Ubicazione</div>
            <div class="riga-elemento">
                <div><?php the_field('ubicazione', $ambulatorioID); ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="prestazioni-ambulatoriali">
    <div class="prestazioni">
        <div class="titolo-prestazioni-ambulatoriali">Elenco Prestazioni</div>
        <ul>
            <?php

            $idPrestazioni = array_unique(get_field('prestazioni', $ambulatorioID));

            //            $idPrestazioniambulatorio = array_unique(array_merge($idPrestazioniInSolvenza, $idPrestazioniInConvenzione));


            foreach ($idPrestazioni as $idPrestazione) {
                $nomePrestazione = get_term_by('id', $idPrestazione, 'prestazioni-ambulatoriali');
            ?>
                <li class="prestazione-ambulatoriale">
                    <?= $nomePrestazione->name; ?>
                </li>
            <?php
            }

            ?>
        </ul>
    </div>
</div>