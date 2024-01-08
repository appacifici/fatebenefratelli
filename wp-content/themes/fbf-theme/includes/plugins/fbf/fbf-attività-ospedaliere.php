<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              marco-ilari
 * @since             1.0.1
 * @package           Fbf_Attività_Ospedaliere
 *
 * @wordpress-plugin
 * Plugin Name:       Attività Ospedaliere FBF
 * Description:       Plugin per la gestione delle attività ospedaliere
 * Version:           1.0.0
 * Author:            Marco Ilari
 * Author URI:        marco-ilari
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fbf-attività-ospedaliere
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('FBF_ATTIVITÀ_OSPEDALIERE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fbf-attività-ospedaliere-activator.php
 */
function activate_fbf_attività_ospedaliere()
{
	require_once plugin_dir_url(__FILE__) . 'includes/class-fbf-attività-ospedaliere-activator.php';
	Fbf_Attività_Ospedaliere_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fbf-attività-ospedaliere-deactivator.php
 */
function deactivate_fbf_attività_ospedaliere()
{
	require_once plugin_dir_url(__FILE__) . 'includes/class-fbf-attività-ospedaliere-deactivator.php';
	Fbf_Attività_Ospedaliere_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fbf_attività_ospedaliere');
register_deactivation_hook(__FILE__, 'deactivate_fbf_attività_ospedaliere');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fbf-attività-ospedaliere.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fbf_attività_ospedaliere()
{

	$plugin = new Fbf_Attività_Ospedaliere();
	$plugin->run();
}
run_fbf_attività_ospedaliere();


// Register Custom Post Type
function custom_post_type()
{

	$labels = array(
		'name'                  => _x('Attività Ospedaliere', 'Post Type General Name', 'text_domain'),
		'singular_name'         => _x('Attività Ospedaliera', 'Post Type Singular Name', 'text_domain'),
		'menu_name'             => __('Attività Ospedaliere', 'text_domain'),
		'name_admin_bar'        => __('Attività Ospedaliera', 'text_domain'),
		'archives'              => __('Archivio Attività', 'text_domain'),
		'attributes'            => __('Attività Ospedaliera', 'text_domain'),
		'parent_item_colon'     => __('Attività Padre', 'text_domain'),
		'all_items'             => __('Tutte le attività', 'text_domain'),
		'add_new_item'          => __('Aggiungi un\'attività Ospedaliera:', 'text_domain'),
		'add_new'               => __('Aggiungi Nuova', 'text_domain'),
		'new_item'              => __('Nuova Attività', 'text_domain'),
		'edit_item'             => __('Modifica Attività', 'text_domain'),
		'update_item'           => __('Aggiorna Attività', 'text_domain'),
		'view_item'             => __('Visualizza Attività', 'text_domain'),
		'view_items'            => __('Visualizza Attività', 'text_domain'),
		'search_items'          => __('Ricerca Attività', 'text_domain'),
		'not_found'             => __('Non Trovata', 'text_domain'),
		'not_found_in_trash'    => __('Non Trovata nel cestino', 'text_domain'),
		'featured_image'        => __('Immagine in evidenza', 'text_domain'),
		'set_featured_image'    => __('Imposta immagine in evidenza', 'text_domain'),
		'remove_featured_image' => __('Rimuovi immagine in evidenza', 'text_domain'),
		'use_featured_image'    => __('Utilizza immagine in evidenza', 'text_domain'),
		'insert_into_item'      => __('Inserisci nell\'attività', 'text_domain'),
		'uploaded_to_this_item' => __('Carica in questa attività', 'text_domain'),
		'items_list'            => __('Elenco di elementi', 'text_domain'),
		'items_list_navigation' => __('Elenco navigazione elmenti', 'text_domain'),
		'filter_items_list'     => __('Filtra elenco elementi', 'text_domain'),
	);
	$args = array(
		'label'                 => __('Attività Ospedaliera', 'text_domain'),
		'description'           => __('Attività Ospedaliere', 'text_domain'),
		'labels'                => $labels,
		'supports'              => array('title', 'revisions', 'custom-fields'),
		'taxonomies'            => array('medici', 'prestazioni-ambulatoriali'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-site-alt3',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type('attivita-ospedaliere', $args);


	$labels = array(
		'name'                  => _x('Reparti', 'Post Type General Name', 'text_domain'),
		'singular_name'         => _x('Reparto', 'Post Type Singular Name', 'text_domain'),
		'menu_name'             => __('Reparti', 'text_domain'),
		'name_admin_bar'        => __('Reparto', 'text_domain'),
		'archives'              => __('Archivio Reparto', 'text_domain'),
		'attributes'            => __('Reparti', 'text_domain'),
		'parent_item_colon'     => __('Reparto Padre', 'text_domain'),
		'all_items'             => __('Tutti i Reparti', 'text_domain'),
		'add_new_item'          => __('Aggiungi un reparto:', 'text_domain'),
		'add_new'               => __('Aggiungi Nuovo', 'text_domain'),
		'new_item'              => __('Nuova Reparto', 'text_domain'),
		'edit_item'             => __('Modifica Reparto', 'text_domain'),
		'update_item'           => __('Aggiorna Reparto', 'text_domain'),
		'view_item'             => __('Visualizza Reparto', 'text_domain'),
		'view_items'            => __('Visualizza Reparto', 'text_domain'),
		'search_items'          => __('Ricerca Reparto', 'text_domain'),
		'not_found'             => __('Non Trovato', 'text_domain'),
		'not_found_in_trash'    => __('Non Trovato nel cestino', 'text_domain'),
		'featured_image'        => __('Immagine in evidenza', 'text_domain'),
		'set_featured_image'    => __('Imposta immagine in evidenza', 'text_domain'),
		'remove_featured_image' => __('Rimuovi immagine in evidenza', 'text_domain'),
		'use_featured_image'    => __('Utilizza immagine in evidenza', 'text_domain'),
		'insert_into_item'      => __('Inserisci nel reparto', 'text_domain'),
		'uploaded_to_this_item' => __('Carica in questo reparto', 'text_domain'),
		'items_list'            => __('Elenco di elementi', 'text_domain'),
		'items_list_navigation' => __('Elenco navigazione elmenti', 'text_domain'),
		'filter_items_list'     => __('Filtra elenco elementi', 'text_domain'),
	);
	$args = array(
		'label'                 => __('Reparto', 'text_domain'),
		'description'           => __('Reparto', 'text_domain'),
		'labels'                => $labels,
		'supports'              => array('title', 'revisions', 'custom-fields'),
		'taxonomies'            => array('medici', 'prestazioni-ambulatoriali'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-site-alt3',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type('reparti', $args);


	$labels = array(
		'name'                  => _x('Ambulatori', 'Post Type General Name', 'text_domain'),
		'singular_name'         => _x('Ambulatorio', 'Post Type Singular Name', 'text_domain'),
		'menu_name'             => __('Ambulatori', 'text_domain'),
		'name_admin_bar'        => __('Ambulatori', 'text_domain'),
		'archives'              => __('Archivio Ambulatori', 'text_domain'),
		'attributes'            => __('Ambulatori', 'text_domain'),
		'parent_item_colon'     => __('Ambulatorio Padre', 'text_domain'),
		'all_items'             => __('Tutte gli ambulatori', 'text_domain'),
		'add_new_item'          => __('Aggiungi un ambulatorio:', 'text_domain'),
		'add_new'               => __('Aggiungi Nuovo', 'text_domain'),
		'new_item'              => __('Nuovo ambulatorio', 'text_domain'),
		'edit_item'             => __('Modifica Ambulatorio', 'text_domain'),
		'update_item'           => __('Aggiorna Ambulatorio', 'text_domain'),
		'view_item'             => __('Visualizza Ambulatorio', 'text_domain'),
		'view_items'            => __('Visualizza Ambulatorio', 'text_domain'),
		'search_items'          => __('Ricerca Ambulatorio', 'text_domain'),
		'not_found'             => __('Non Trovato', 'text_domain'),
		'not_found_in_trash'    => __('Non Trovato nel cestino', 'text_domain'),
		'featured_image'        => __('Immagine in evidenza', 'text_domain'),
		'set_featured_image'    => __('Imposta immagine in evidenza', 'text_domain'),
		'remove_featured_image' => __('Rimuovi immagine in evidenza', 'text_domain'),
		'use_featured_image'    => __('Utilizza immagine in evidenza', 'text_domain'),
		'insert_into_item'      => __('Inserisci in ambulatorio', 'text_domain'),
		'uploaded_to_this_item' => __('Carica in questo ambulatorio', 'text_domain'),
		'items_list'            => __('Elenco di elementi', 'text_domain'),
		'items_list_navigation' => __('Elenco navigazione elmenti', 'text_domain'),
		'filter_items_list'     => __('Filtra elenco elementi', 'text_domain'),
	);
	$args = array(
		'label'                 => __('Ambulatorio', 'text_domain'),
		'description'           => __('Ambulatorio', 'text_domain'),
		'labels'                => $labels,
		'supports'              => array('title', 'revisions', 'custom-fields'),
		'taxonomies'            => array('medici', 'prestazioni-ambulatoriali'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-site-alt3',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type('ambulatori', $args);

	$labels = array(
		'name'                  => _x('Servizi Afferenti', 'Post Type General Name', 'text_domain'),
		'singular_name'         => _x('Servizio Afferente', 'Post Type Singular Name', 'text_domain'),
		'menu_name'             => __('Servizi Afferenti', 'text_domain'),
		'name_admin_bar'        => __('Servizi Afferenti', 'text_domain'),
		'archives'              => __('Archivio Servizi Afferenti', 'text_domain'),
		'attributes'            => __('Servizi Afferenti', 'text_domain'),
		'parent_item_colon'     => __('Servizio Afferente Padre', 'text_domain'),
		'all_items'             => __('Tutte gli Servizi Afferenti', 'text_domain'),
		'add_new_item'          => __('Aggiungi un Servizio Afferente:', 'text_domain'),
		'add_new'               => __('Aggiungi Nuovo', 'text_domain'),
		'new_item'              => __('Nuovo Servizio Afferente', 'text_domain'),
		'edit_item'             => __('Modifica Servizio Afferente', 'text_domain'),
		'update_item'           => __('Aggiorna Servizio Afferente', 'text_domain'),
		'view_item'             => __('Visualizza Servizio Afferente', 'text_domain'),
		'view_items'            => __('Visualizza Servizio Afferente', 'text_domain'),
		'search_items'          => __('Ricerca Servizio Afferente', 'text_domain'),
		'not_found'             => __('Non Trovato', 'text_domain'),
		'not_found_in_trash'    => __('Non Trovato nel cestino', 'text_domain'),
		'featured_image'        => __('Immagine in evidenza', 'text_domain'),
		'set_featured_image'    => __('Imposta immagine in evidenza', 'text_domain'),
		'remove_featured_image' => __('Rimuovi immagine in evidenza', 'text_domain'),
		'use_featured_image'    => __('Utilizza immagine in evidenza', 'text_domain'),
		'insert_into_item'      => __('Inserisci in Servizio Afferente', 'text_domain'),
		'uploaded_to_this_item' => __('Carica in questo Servizio Afferente', 'text_domain'),
		'items_list'            => __('Elenco di elementi', 'text_domain'),
		'items_list_navigation' => __('Elenco navigazione elmenti', 'text_domain'),
		'filter_items_list'     => __('Filtra elenco elementi', 'text_domain'),
	);
	$args = array(
		'label'                 => __('Servizio Afferente', 'text_domain'),
		'description'           => __('Servizio Afferente', 'text_domain'),
		'labels'                => $labels,
		'supports'              => array('title', 'revisions', 'custom-fields'),
		'taxonomies'            => array('medici', 'prestazioni-ambulatoriali'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-site-alt3',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type('servizi-afferenti', $args);
}
add_action('init', 'custom_post_type', 0);




function create_topics_nonhierarchical_taxonomy()
{

	// Labels part for the GUI

	$labels = array(
		'name' => _x('Medici', 'taxonomy general name'),
		'singular_name' => _x('Medico', 'taxonomy singular name'),
		'search_items' =>  __('Cerca Medico'),
		'popular_items' => __('Medici famosi'),
		'all_items' => __('Tutti i medico'),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __('Modifica Medico'),
		'update_item' => __('Aggiorna Medico'),
		'add_new_item' => __('Aggiungi Nuovo Medico'),
		'new_item_name' => __('Nuovo nome medico'),
		'separate_items_with_commas' => __('Separare medici con la virgola'),
		'add_or_remove_items' => __('Aggiungi o rimuovi medici'),
		'choose_from_most_used' => __('Scegli fra i medici più impiegati'),
		'menu_name' => __('Medici'),
	);

	// Now register the non-hierarchical taxonomy like tag

	register_taxonomy('medici', 'post', array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array('slug' => 'topic'),
	));

	$labels = array(
		'name' => _x('Prestazioni Ambulatoriali', 'taxonomy general name'),
		'singular_name' => _x('Prestazione Ambulatoriale', 'taxonomy singular name'),
		'search_items' =>  __('Cerca Prestazione Ambulatoriale'),
		'popular_items' => __('Prestazioni ambulatoriali più importanti'),
		'all_items' => __('Tutte le prestazioni ambulatoriali'),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __('Modifica Prestazione Ambulatoriale'),
		'update_item' => __('Aggiorna Prestazione Ambulatoriale'),
		'add_new_item' => __('Aggiungi Nuova Prestazione Ambulatoriale'),
		'new_item_name' => __('Nuova Prestazione Ambulatoriale'),
		'separate_items_with_commas' => __('Separare le prestazioni con la virgola'),
		'add_or_remove_items' => __('Aggiungi o rimuovi prestazioni'),
		'choose_from_most_used' => __('Scegli fra le prestazioni'),
		'menu_name' => __('Prestazioni Ambulatoriali'),
	);

	// Now register the non-hierarchical taxonomy like tag

	register_taxonomy('prestazioni-ambulatoriali', 'post', array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array('slug' => 'topic'),
	));
}




add_action('init', 'create_topics_nonhierarchical_taxonomy', 0);


/* SETTINGS PAGE INFORMAZIONI CHE VERRANNO UTILIZZATE NEL TEMA */

function dbi_add_settings_page()
{
	add_options_page('Info fatebenefratelli', 'Info fatebenefratelli', 'manage_options', 'info-fatebenefratelli', 'dbi_render_plugin_settings_page');
}
add_action('admin_menu', 'dbi_add_settings_page');


function dbi_render_plugin_settings_page()
{
?>
	<h2>Informazioni Tema Fatebenefratelli</h2>
	<form action="options.php" method="post">
		<?php
		settings_fields('info-fatebenefratelli-options');
		do_settings_sections('info-fatebenefratelli-plugin'); ?>
		<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
	</form>
<?php
}




function fbf_settings_init()
{

	register_setting('info-fatebenefratelli-options', 'fbf_settings');

	add_settings_section(
		'fbf_section',
		__('Informazioni che vengono recuperate all\'interno del tema di wordpress fbf'),
		'',
		'info-fatebenefratelli-plugin'
	);

	add_settings_field(
		'fbf_telefono_prenotazioni',
		__('Telefono Prenotazioni'),
		'fbf_text_field_0_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_link_prenotazioni_online',
		__('URL Prenotazioni Online'),
		'fbf_text_field_1_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'',
		__('Indicazioni prenotazioni telefoniche (es. orario)'),
		'fbf_text_field_2_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_url_informazioni_ricovero',
		__('Url Informazioni ricovero'),
		'fbf_text_field_3_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);

	add_settings_field(
		'fbf_url_notizie',
		__('Url Notizie'),
		'fbf_text_field_4_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_colore_sede',
		__('Colore Sede'),
		'fbf_text_field_5_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_link_referti_online',
		__('Link Referti Online'),
		'fbf_text_field_6_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_url_archivio_vita_ospedaliera',
		__('URL Archivio Vita Ospedaliera'),
		'fbf_text_field_7_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_posizione_notizie_evidenza',
		__('Posizione Notizie in Evidenza'),
		'fbf_text_field_8_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_nome_attivita_sanitaria',
		__('Nome Attività Sanitaria'),
		'fbf_text_field_9_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_informazioni_sportello',
		__('Informazioni disponibilità sportello'),
		'fbf_text_field_10_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_link_area_personale',
		__('Link area personale'),
		'fbf_text_field_11_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
	add_settings_field(
		'fbf_portale_provincia',
		__('Portale della Provincia Romana'),
		'fbf_text_field_12_render',
		'info-fatebenefratelli-plugin',
		'fbf_section'
	);
}


function fbf_text_field_0_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_telefono_prenotazioni]' value='<?php echo $options['fbf_telefono_prenotazioni']; ?>' size="50">
<?php

}
function fbf_text_field_1_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_link_prenotazioni_online]' value='<?php echo $options['fbf_link_prenotazioni_online']; ?>' size="100">
<?php

}
function fbf_text_field_2_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_indicazioni_prenotazioni_telefono]' value='<?php echo $options['fbf_indicazioni_prenotazioni_telefono']; ?>'>
<?php

}
function fbf_text_field_3_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_url_informazioni_ricovero]' value='<?php echo $options['fbf_url_informazioni_ricovero']; ?>' size="100">
<?php

}

function fbf_text_field_4_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_url_notizie]' value='<?php echo $options['fbf_url_notizie']; ?>' size="100">
<?php

}

function fbf_text_field_5_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_colore_sede]' value='<?php echo $options['fbf_colore_sede']; ?>' size="50">
<?php

}


function fbf_text_field_6_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_link_referti_online]' value='<?php echo $options['fbf_link_referti_online']; ?>' size="100">
<?php

}


function fbf_text_field_7_render()
{

	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_url_archivio_vita_ospedaliera]' value='<?php echo $options['fbf_url_archivio_vita_ospedaliera']; ?>' size="100">
<?php

}
function fbf_text_field_8_render()
{

	$options = get_option('fbf_settings');
?>
	<select name='fbf_settings[fbf_posizione_notizie_evidenza]'>

		<option value="">Non Mostrare</option>
		<option value="sopra" <?= (isset($options['fbf_posizione_notizie_evidenza']) and $options['fbf_posizione_notizie_evidenza'] == "sopra") ? "selected" : ""; ?>>In alto nella Home Page</option>
		<option value="sotto" <?= (isset($options['fbf_posizione_notizie_evidenza']) and $options['fbf_posizione_notizie_evidenza'] == "sotto") ? "selected" : ""; ?>>In basso nella Home Page</option>
	</select>
<?php

}

function fbf_text_field_9_render()
{

	$options = get_option('fbf_settings');
?>
	<p>Lasciare Vuoto per mostrare "Attività Ospedaliere"</p>
	<input type='text' name='fbf_settings[fbf_nome_attivita_sanitaria]' value='<?php echo isset($options['fbf_nome_attivita_sanitaria']) ? $options['fbf_nome_attivita_sanitaria'] : ""; ?>' size="100">
<?php

}

function fbf_text_field_10_render()
{
	$options = get_option('fbf_settings');
?>
	<p>Utilizzere il simbolo | per andare a capo</p>
	<input type='text' name='fbf_settings[fbf_informazioni_sportello]' value='<?php echo isset($options['fbf_informazioni_sportello']) ? $options['fbf_informazioni_sportello'] : ""; ?>' size="50">
<?php

}
function fbf_text_field_11_render()
{
	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_link_area_personale]' value='<?php echo isset($options['fbf_link_area_personale']) ? $options['fbf_link_area_personale'] : ""; ?>' size="50">
<?php

}
function fbf_text_field_12_render()
{
	$options = get_option('fbf_settings');
?>
	<input type='text' name='fbf_settings[fbf_portale_provincia]' value='<?php echo (isset($options['fbf_portale_provincia']) ? $options['fbf_portale_provincia'] : ""); ?>' size="50">
<?php

}

add_action('admin_init', 'fbf_settings_init');
