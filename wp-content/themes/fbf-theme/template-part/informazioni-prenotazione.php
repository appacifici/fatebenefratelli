<div class="informazioni">
	<?php if (get_option('fbf_settings')['fbf_link_prenotazioni_online']) { ?>
		<a href="<?= get_option('fbf_settings')['fbf_link_prenotazioni_online'] ?>" class="pulsante-informazioni">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/fbf-icons/svg/053-doctor-online - white.svg" />
			<div>Prenota on line</div>
		</a>
	<?php } ?>


	<?php if (isset(get_option('fbf_settings')['fbf_informazioni_sportello']) and get_option('fbf_settings')['fbf_informazioni_sportello']) {
		$info = explode("|", get_option('fbf_settings')['fbf_informazioni_sportello'])
	?>
		<div class="sportello-prenotazioni">
			<div class="etichetta-sportello">Sportello<br /><span class="etichetta-cup">CUP</span></div>
			<div class="informazioni-sportello">
				<?php foreach ($info as $i) {
					echo "<div>" . $i	. "</div>";
				}
				?>
			</div>
		</div>
	<?php }	?>
	<?php if (get_option('fbf_settings')['fbf_telefono_prenotazioni']) {	?>
		<a href="tel:<?= get_option('fbf_settings')['fbf_telefono_prenotazioni'] ?>">
			<div class=" pulsante-chiama">
				<div class="etichetta-chiama">Chiama il numero</div>
				<div class="numero-telefono"><?= get_option('fbf_settings')['fbf_telefono_prenotazioni'] ?></div>
			</div>
		</a>
	<?php }	?>

	<?php if (get_option('fbf_settings')['fbf_indicazioni_prenotazioni_telefono']) {	?>
		<div class="informazioni-prenotazioni"><?= get_option('fbf_settings')['fbf_indicazioni_prenotazioni_telefono'] ?></div>
	<?php }	?>
</div>