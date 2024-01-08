<?php

/* enqueue script for parent theme stylesheeet */
function childtheme_parent_styles()
{
    // enqueue style
    wp_enqueue_style('parent', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'childtheme_parent_styles');



// Aggiungere SVG a WordPress
function add_file_types_to_uploads($file_types)
{
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $new_filetypes['svgz'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes);
    return $file_types;
}
add_action('upload_mimes', 'add_file_types_to_uploads');
// AGGIUNGO PLUGIN ACF
// Define path and URL to the ACF plugin.
define('MY_ACF_PATH', get_stylesheet_directory() . '/includes/plugins/advanced-custom-fields/');
define('MY_ACF_URL', get_stylesheet_directory_uri() . '/includes/plugins/advanced-custom-fields/');

// Include the ACF plugin.
include_once(MY_ACF_PATH . 'acf.php');

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url($url)
{
    return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin($show_admin)
{
    return true;
}

// AGGIUNGO CUSTOM FIELD UTILIZZANDO IL PLUGIN ACF
require_once(__DIR__ . '/includes/acf-ambulatorio.php');
require_once(__DIR__ . '/includes/acf-attivita-ospedaliera.php');
require_once(__DIR__ . '/includes/acf-reparto.php');
require_once(__DIR__ . '/includes/acf-servizio-afferente.php');


// AGGIUNGO PLUGIN FBF
define('MY_FBF_PATH', get_stylesheet_directory() . '/includes/plugins/fbf/');
define('MY_FBF_URL', get_stylesheet_directory_uri() . '/includes/plugins/fbf/');

include_once(MY_FBF_PATH . 'fbf-attività-ospedaliere.php');


//LOAD PLUGIN FBF
function load_MyPlugin()
{
    if (!class_exists('Fbf_Attività_Ospedaliere')) {
        include_once(get_template_directory_uri() . '/plugins/fbf/index.php');
    }
}
add_action('after_setup_theme', 'load_MyPlugin');

//AGGIUNGO FILE FUNZIONE PER LA GESTIONE DEI BREADCRUMBS
require_once(__DIR__ . '/functions/breadcrumbs.php');

// Aggiunta di script js
function myprefix_enqueue_scripts()
{
    if (is_front_page()) {
        wp_enqueue_script('home-script', get_stylesheet_directory_uri() . '/js/home.js', array(), true, true);
    }
    if (is_single() && 'attivita-ospedaliere' == get_post_type()) {
        wp_enqueue_script('attivita-ospedaliera-script', get_stylesheet_directory_uri() . '/js/attivita-ospedaliera.js', array(), true, true);
    }
}
add_action('wp_enqueue_scripts', 'myprefix_enqueue_scripts');

//AGGIUNGO SHORTCODES PERSONALIZZATI
include(get_stylesheet_directory() . '/functions/shortcodes.php');

//AGGIUNGO WIDGET PER LO SCARICO DELLE APP
include(get_stylesheet_directory() . '/includes/widgets/scarica-app.php');

//AGGIUNGO METABOX PERSONALIZZATI PER GLI ARTICOLI
include(get_stylesheet_directory() . '/functions/post-metabox.php');

//AGGIUNGO METABOX PERSONALIZZATI PER GLI ARTICOLI
include(get_stylesheet_directory() . '/functions/styling.php');

//AGGIUNGO UN FILE PER RICHIAMARE FUNZIONI VARIE
include(get_stylesheet_directory() . '/functions/misc.php');

//AGGIUNTO RUOLO RECLUTATORE DA ASSOCIARE AGLI UTENTI PER IL CONTROLLO DEI CV INSERITI TRAMITE WP_FORMS
// remove_role( 'recruiter' );
function addRecruiterRole()
{
    add_role('recruiter', __('Recruiter'), array(
        'read' => true,
    ));
}

// RICERCA IN CUSTOM TYPE POST
function include_custom_post_types_in_search_results( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_search() ) {
		$query->set( 'post_type', array( 'post', 'page', 'attivita-ospedaliera', 'reparto', 'ambulatorio'));
	}
    return $query;
}
add_action( 'pre_get_posts', 'include_custom_post_types_in_search_results' );
