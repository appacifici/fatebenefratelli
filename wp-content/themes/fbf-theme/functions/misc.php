<?php


/*************************************************************************************
 * Archive Page Title
 *************************************************************************************/
 
function om_get_archive_page_titolo() {
	
	$out='';
	
	if (is_category()) { 
		$out = sprintf(__('Elenco %s', 'om_theme'), single_cat_title('',false));
	} elseif( is_tag() ) {
		$out = sprintf(__('Elenco %s', 'om_theme'), single_tag_title('',false));
	} elseif (is_day()) { 
		$out = __('Archive for', 'om_theme'); $out .= ' '.get_the_time('F jS, Y'); 
	} elseif (is_month()) { 
		$out = __('Archive for', 'om_theme'); $out .= ' '.get_the_time('F, Y'); 
	} elseif (is_year()) { 
		$out = __('Archive for', 'om_theme'); $out .= ' '.get_the_time('Y');
	} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
		$out = __('Blog Archives', 'om_theme');
	} else { 
		$blog = get_post(get_option('page_for_posts'));
		$out = $blog->post_title;
	}
 	
 	return $out;
}