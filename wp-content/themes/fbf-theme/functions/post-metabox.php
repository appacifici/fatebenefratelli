<?php

function add_custom_meta_box()
{
    add_meta_box("articolo-meta-box", "Informazioni aggiuntive", "custom_meta_box_markup", "post", "normal");
}

add_action("add_meta_boxes", "add_custom_meta_box");

function custom_meta_box_markup($post)
{
?>
    <div>
        <table class="form-table">
            <tr>
                <th style="width:25%">
                    <label for="articolo-attivita-ospedaliera"><strong>Attività ospedaliera</strong></label>
                    <div class="how-to">Valorizzare per mostrare l'articolo nell'apposita sezione dell'attività ospedaliera</div>
                </th>
                <td>
                    <?php

                    $arrayAttivitaArticolo = get_post_meta($post->ID, 'articolo-attivita-ospedaliera', true);

                    if (!is_array($arrayAttivitaArticolo)) {
                        $arrayAttivitaArticolo = [$arrayAttivitaArticolo];
                    }

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
                    if ($elenco_attivita->have_posts())
                        while ($elenco_attivita->have_posts()) : $elenco_attivita->the_post();
                    ?>
                        <div class="checkboxAttivita"><input type="checkbox" name="articolo-attivita-ospedaliera[]" id="att<?= get_the_ID(); ?>" value="<?= get_the_ID(); ?>" <?php echo (in_array(get_the_ID(), $arrayAttivitaArticolo)) ? 'checked="checked"' : ''; ?> />
                            <label for="att<?= get_the_ID(); ?>">
                                <?php the_field('nome_attivita'); ?>
                            </label>
                        </div>

                    <?php endwhile;
                    ?>

                </td>
            </tr>
            <tr>
                <th style="width:25%">
                    <label for="articolo-in-evidenza">In Evidenza </label>
                    <div class="how-to">L'articolo viene mostrato nella sezione in evidenza della home page</div>
                </th>
                <td>
                    <?php
                    $checkbox_value = get_post_meta($post->ID, "articolo-in-evidenza", true);

                    if ($checkbox_value == "") {
                    ?>
                        <input name="articolo-in-evidenza" type="checkbox" value="true">
                    <?php
                    } else if ($checkbox_value == "true") {
                    ?>
                        <input name="articolo-in-evidenza" type="checkbox" value="true" checked>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
<?php
}

function save_custom_meta_box($post_id, $post)
{
    if (!current_user_can("edit_post", $post_id))
        return $post_id;

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;


    $slug = "post";
    if ($slug != $post->post_type)
        return $post_id;

    $meta_box_select_attivita_value = [];
    $meta_box_checkbox_value = "";

    if (isset($_POST['articolo-attivita-ospedaliera'])) {
        $meta_box_select_attivita_value = $_POST['articolo-attivita-ospedaliera'];
    }

    update_post_meta($post_id, 'articolo-attivita-ospedaliera', $meta_box_select_attivita_value);

    if (isset($_POST["articolo-in-evidenza"])) {
        $meta_box_checkbox_value = $_POST["articolo-in-evidenza"];
    }
    update_post_meta($post_id, "articolo-in-evidenza", $meta_box_checkbox_value);
}

add_action("save_post", "save_custom_meta_box", 10, 2);
