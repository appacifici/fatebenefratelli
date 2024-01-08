<?php

// RIQUADRO PRENOTAZIONE
function riquadro_prenotazione()
{

	$out = "<div class=\"pulsanti-informazioni\">";

	if (get_option('fbf_settings')['fbf_link_prenotazioni_online']) {
		$out .= "<a href=\"" . get_option('fbf_settings')['fbf_link_prenotazioni_online'] . "\" class=\"pulsante-informazioni\">
			<img src=\"" . get_stylesheet_directory_uri() . "/img/fbf-icons/svg/053-doctor-online - white.svg\" />
			<div>Prenota on line</div>
			</a>";
	}

	if (isset(get_option('fbf_settings')['fbf_informazioni_sportello']) and get_option('fbf_settings')['fbf_informazioni_sportello'] != "") {
		$info = explode("|", get_option('fbf_settings')['fbf_informazioni_sportello']);

		$out .= "
			<div class=\"sportello-prenotazioni\">
			<div class=\"etichetta-sportello\">Sportello<br /><span class=\"etichetta-cup\">CUP</span></div>
			<div class=\"informazioni-sportello\">";
		foreach ($info as $i) {
			$out .= "<div>" . $i	. "</div>";
		}
		$out .= "</div></div>";
	}

	$out .= "
		<a href=\"tel:" . get_option('fbf_settings')['fbf_telefono_prenotazioni'] . "\">
		<div class=\"pulsante-chiama\">
		<div class=\"etichetta-chiama\">Prenotazioni telefoniche</div>
		<div class=\"numero-telefono\">" . get_option('fbf_settings')['fbf_telefono_prenotazioni'] . "</div>
		</div></a>";

	if (!empty(get_option('fbf_settings')['fbf_indicazioni_prenotazioni_telefono']) ) 
	$out.="<div class=\"informazioni-prenotazioni\">".get_option('fbf_settings')['fbf_indicazioni_prenotazioni_telefono']."</div>";
		$out .= "</div>";

	return $out;
}
add_shortcode('riquadro_prenotazione', 'riquadro_prenotazione');

// RIQUADRO REFERTI ON LINE
function riquadro_referti_online()
{

	$out = "<div class=\"referti-online\">";


	if (get_option('fbf_settings')['fbf_link_prenotazioni_online']) {
		$out .= "<a href=\"" . get_option('fbf_settings')['fbf_link_referti_online'] . "\" class=\"pulsante-referti\">
			<img src=\"" . get_stylesheet_directory_uri() . "/img/fbf-icons/referti.svg\" />
			<div>Referti on line</div>
			</a></div>
			";
	}

	return $out;
}
add_shortcode('riquadro_referti_online', 'riquadro_referti_online');



function riquadro_notizie_non_evidenza($atts)
{
	$numeroNotizie = isset($atts['numero-notizie']) ? $atts['numero-notizie'] : 4;
	$args = array(
			'post_type' => array('post'),
			'meta_key'   => 'articolo-in-evidenza',
			'meta_value' => 'true',
			'meta_compare' => '!=',
			'orderby' => 'date',
			'order'   => 'DESC',
			'posts_per_page' => $numeroNotizie
		     );

	if (isset($atts['categorie']))
		$args['category_name'] = $atts['categorie'];
	// array_push($args,'category_name' => $categorie;

	$q = new WP_Query($args);

	$out = '<div class="list-post-small">';
	if ($q->have_posts()) {
		?>
			<?php
			$i = 0;
		while ($q->have_posts()) {
			$q->the_post();
			//if ($i) $out .= '<hr />';
			$out .= '<div class="post-small">';
			$out .= '<div class="post-title"><h3><a href="' . get_permalink() . '">';
			$out .= '► ' . get_the_title();
			$out .= '</a></h3>';
			$out .= '</div>

				<div class="post-meta">
				<div class="post-date">' . get_the_time(get_option('date_format')) . '</div>
				</div>

				<div class="clear"></div>
				</div>';

			$i++;
		}
		?>
			<?php
	}
	$out .= '</div>';
	return $out;
}
add_shortcode('riquadro_notizie_non_evidenza', 'riquadro_notizie_non_evidenza');

function vita_ospedaliera($atts)
{
	$out = '<div class="vita-ospedaliera"><div><h2>Rivista Mensile dei Fatebenefratelli</h2></div><div class="contenitore-vita-ospedaliera">';
	?>
		<?php
		if (isset($atts['url_copertina']))
			$out .= '<a href="' . $atts['url_pubblicazione'] . '" class="copertina-vita-ospedaliera"><img src="' . $atts['url_copertina'] . '"></a>';
	$out .= '<div class="riquadro-periodo-rivista"><div class="periodo-rivista"><a href="' . $atts['url_pubblicazione'] . '"><b>Vita Ospedaliera</b> <br /> Mese: ' . $atts['periodo_pubblicazione'] . '</a></div>';
	if (get_option('fbf_settings')['fbf_url_archivio_vita_ospedaliera'])
		$out .= '<a id="archivio-vita-ospedaliera" target="_blank" href="' . get_option('fbf_settings')['fbf_url_archivio_vita_ospedaliera'] . '" class="button single-color text-bright size-medium">Archivio</a>';
	$out .= '</div></div></div>';
	return $out;
} add_shortcode('vita_ospedaliera', 'vita_ospedaliera');

function in_primo_piano($atts)
{
	$notiziaInPrimoPiano = get_post($atts['id_post']);
	$out = "";
	$out .= '<div class="titolo-primo-piano"><h2>' . $notiziaInPrimoPiano->post_title . '</h2></div>';
	$testo = $notiziaInPrimoPiano->post_excerpt ?: apply_filters('the:_content', $notiziaInPrimoPiano->post_content);

	if (get_the_post_thumbnail_url($atts['id_post']))
		$out .= '<div class="immagine-primo-piano" style="background: url(\'' . get_the_post_thumbnail_url($atts['id_post'], 'large') . '\') no-repeat center"></div>';
	$out .= '<div class="riassunto-primo-piano">' . $testo . '</div>';
	$out .= '<a class="button single-color text-bright size-medium" href="' . get_permalink($atts['id_post']) . '">Vai alla Notizia</a>';
	$out .= '<p></p>';

	return $out;
}
add_shortcode('in_primo_piano', 'in_primo_piano');

function contenitore_video($atts)
{
	$out='<div class="container-video-youtube">';
	$out.='<video class="video-youtube" src="'.$atts['url'].'" controls>';
	$out.='</video>';
	$out.='</div>';
	return $out;
}
add_shortcode('contenitore_video', 'contenitore_video');
