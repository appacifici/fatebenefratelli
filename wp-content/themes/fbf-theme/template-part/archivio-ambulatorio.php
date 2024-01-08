<?php
global $post;
$backup = $post;
$idAmbulatorio = get_the_ID();
$args = array(
    'post_type' => array('attivita-ospedaliere'),
    'posts_per_page' => 1,
    'order' => 'ASC',
    'max_num_pages' => 1,
    'meta_query' => array(
        array(
            'key' => 'ambulatori',
            'value' => $idAmbulatorio,
            'compare' => 'LIKE'
        )
    )
);

$attivitaOspedalieraAmbulatorio = new WP_Query($args);
if ($attivitaOspedalieraAmbulatorio->have_posts()) {
    $attivitaOspedalieraAmbulatorio->the_post();
    $nomeAttivita = get_field('nome_attivita');
    $urlAttivitaOspedaliera = get_the_permalink();
} else {
    $nomeAttivita = "";
    $urlAttivitaOspedaliera = null;
}

$post = $backup;
?>
<?php if ($urlAttivitaOspedaliera) : ?>
    <a href="<?php echo $urlAttivitaOspedaliera . "#riquadro-ambulatorio-" . get_the_ID(); ?>">

    <?php endif; ?>
    <article>
        <div class="nome-ambulatorio">
            <?= get_the_title(); ?>
        </div>
        <?php if ($nomeAttivita) : ?>
            <div class="nome-uo-reparto"">
                <?= $nomeAttivita; ?>
            </div>
        <?php endif; ?>
        <?php if (get_field('ubicazione')) : ?>
            <div class=" elemento-attivita-ospedaliera">
                <div class="icona-attivita-ospedaliera">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/041-hospital-5.svg" />
                </div>

                <div class="contenuto-elemento">
                    <div class="titolo-contenuto-elemento">Ubicazione</div>
                    <div class="nome-coordinatore riga-elemento">
                        <div><?php the_field('ubicazione'); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </article>
    <?php if ($urlAttivitaOspedaliera) : ?>
    </a>
<?php endif; ?>