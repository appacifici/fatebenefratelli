<!-- Attività ospedaliere  -->
<a href="<?= the_permalink(); ?>">
     <div class="icona-attivita-ospedaliera">

          <?php
          switch (get_field('tipo_attivita_ospedaliera')) {
               case 'Struttura Semplice Dipartimentale':
                    $nomeImmagine = "uos.svg";
                    break;
               case 'Unità Operativa Complessa':
                    $nomeImmagine = "uoc.svg";
                    break;
               default:
               $nomeImmagine = "attivita.svg";
                    break;
          }
          ?>
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/<?= $nomeImmagine; ?>" />
     </div>
     <div class="titolo-attivita-ospedaliera">
          <div class="tipo-attivita-ospedaliera"><?= the_field('tipo_attivita_ospedaliera') ?></div>
          <div class="nome-attivita-ospedaliera"><?= the_field('nome_attivita') ?></div>
     </div>
</a>

<!-- /Attività ospedaliere -->