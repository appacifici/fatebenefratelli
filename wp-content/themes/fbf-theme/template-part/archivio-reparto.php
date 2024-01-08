    <?php
    global $post;
    $backup = $post;
    $idReparto = get_the_ID();
    $args = array(
        'post_type' => array('attivita-ospedaliere'),
        'posts_per_page' => 1,
        'order' => 'ASC',
        'max_num_pages' => 1,
        'meta_query' => array(
            array(
                'key' => 'reparti',
                'value' => $idReparto,
                'compare' => 'LIKE'
            )
        )
    );
    $attivitaOspedalieraReparto = new WP_Query($args);
    if ($attivitaOspedalieraReparto->have_posts()) {
        $attivitaOspedalieraReparto->the_post();
        $nomeAttivita = get_field('nome_attivita');
        $urlAttivitaOspedaliera = get_the_permalink();
    } else {
        $nomeAttivita = "";
        $urlAttivitaOspedaliera = null;
    }
    $post = $backup;
    ?>
    <?php if ($urlAttivitaOspedaliera) : ?>
        <a href="<?php echo $urlAttivitaOspedaliera . "#riquadro-reparto-" . get_the_ID(); ?>">
        <?php endif; ?>
        <article>
            <div class="nome-reparto">
                <?= get_the_title(); ?>
            </div>
            <?php if ($nomeAttivita) : ?>
                <div class="nome-uo-reparto">
                    <?= $nomeAttivita; ?>
                </div>
            <?php endif; ?>

            <?php if (get_field('coordinatore_infermieristico')) : ?>
                <div class="elemento-attivita-ospedaliera">
                    <div class="icona-attivita-ospedaliera">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/035-nurse.svg" />
                    </div>

                    <div class="contenuto-elemento">
                        <div class="titolo-contenuto-elemento">Coordinatore</div>
                        <div class="nome-coordinatore riga-elemento">
                            <div><?php the_field('coordinatore_infermieristico'); ?></div>
                        </div>
                        <div class="contatti-coordinatore riga-elemento">
                            <div><?php the_field('email_coordinatore_infermieristico'); ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (get_field('Nome Reparto Fisico') or get_field('ubicazione_reparto')) : ?>
                <div class="elemento-attivita-ospedaliera">
                    <div class="icona-attivita-ospedaliera">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/038-hospital-3.svg" />
                    </div>

                    <div class="contenuto-elemento">
                        <div class="titolo-contenuto-elemento">Ubicazione</div>
                        <div class="nome-santo riga-elemento">
                            <div><?php the_field('Nome Reparto Fisico'); ?></div>
                        </div>
                        <div class="ubicazione-reparto riga-elemento">
                            <div><?php the_field('ubicazione_reparto'); ?></div>
                        </div>
                    </div>
                </div>
            <?php endif;
            ?>
        </article>
        <?php if ($urlAttivitaOspedaliera) : ?>
        </a>
    <?php endif; ?>