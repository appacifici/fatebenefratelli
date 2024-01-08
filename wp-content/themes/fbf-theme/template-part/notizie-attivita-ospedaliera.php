<div class="elenco-articoli-in-evidenza">
    <?php

    $elenco_articoli_in_evidenza = $elenco_articoli;

    if ($elenco_articoli_in_evidenza->have_posts())
        while ($elenco_articoli_in_evidenza->have_posts()) : $elenco_articoli_in_evidenza->the_post();
            $postId = get_the_ID();
            $sfondo = has_post_thumbnail() ? get_the_post_thumbnail_url(null, [1920, 1080]) : get_stylesheet_directory_uri() . "/img/sfondo-notizia-predefinito.png";
    ?>
        <div class="articolo-in-evidenza">
            <div style="background:url('<?= $sfondo; ?>')" class="immagine-articolo-in-evidenza"></div>
            <div class="articolo-in-evidenza-container">
                <div class="riga-articolo-in-evidenza">

                    <div class="post-meta">
                        <div class="post-date"><?= get_the_time(get_option('date_format')) ?></div>
                        <?php if (get_option(OM_THEME_PREFIX . 'post_hide_categories') != 'true' && $categories = get_the_category_list(', ')) { ?>
                            <div class="post-categories">
                                <?php echo  $categories ?>
                            </div>
                        <?php } ?>
                    </div>

                </div>

                <a href=<?php the_permalink() ?>>
                    <div class="titolo-articolo-in-evidenza">
                        <div class="post-title">
                            <h3><?= get_the_title() ?></h3>
                        </div>
                    </div>

                </a>
            </div>
        </div>
    <?php
        endwhile;
    wp_reset_query(); ?>
</div>