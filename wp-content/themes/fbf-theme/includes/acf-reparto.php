<?php
if( function_exists('acf_add_local_field_group') ):


acf_add_local_field_group(array(
	'key' => 'group_5e97702ec0490',
	'title' => 'Reparto',
	'fields' => array(
		array(
			'key' => 'field_5e97703a0116b',
			'label' => 'Nome Reparto',
			'name' => 'nome_reparto',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e9771a701172',
			'label' => 'Nome Reparto Fisico',
			'name' => 'Nome Reparto Fisico',
			'type' => 'text',
			'instructions' => 'Nome Del Santo',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e9770530116c',
			'label' => 'Coordinatore Infermieristico',
			'name' => 'coordinatore_infermieristico',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e97706a0116d',
			'label' => 'Telefono Coordinatore Infermieristico',
			'name' => 'telefono_coordinatore_infermieristico',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '30',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => 20,
		),
		array(
			'key' => 'field_5e9770890116e',
			'label' => 'Email Coordinatore Infermieristico',
			'name' => 'email_coordinatore_infermieristico',
			'type' => 'email',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_5e9770a70116f',
			'label' => 'Ubicazione Reparto',
			'name' => 'ubicazione_reparto',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '60',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e9770b701170',
			'label' => 'Orari di visita Lunedi sabato',
			'name' => 'orari_di_visita_lunedi_sabato',
			'type' => 'text',
			'instructions' => 'Separare più orari da punto e virgola. Gli orari Vanno scritti nel seguente formato 9:00-10:00',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '80',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e97717801171',
			'label' => 'Orari di Visita Domenica e Festivi',
			'name' => 'orari_di_visita_domenica_e_festivi',
			'type' => 'text',
			'instructions' => 'Separare più orari da punto e virgola. Gli orari Vanno scritti nel seguente formato 9:00-10:00',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5e9771cb01173',
			'label' => 'Foto Reparto',
			'name' => 'foto_reparto',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'id',
			'preview_size' => 'thumbnail-post-big',
			'library' => 'all',
			'min_width' => 400,
			'min_height' => 300,
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '0.5',
			'mime_types' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'reparti',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'permalink',
		1 => 'the_content',
		2 => 'excerpt',
		3 => 'discussion',
		4 => 'comments',
		5 => 'revisions',
		6 => 'slug',
		7 => 'format',
		8 => 'page_attributes',
		9 => 'featured_image',
		10 => 'categories',
		11 => 'tags',
		12 => 'send-trackbacks',
	),
	'active' => true,
	'description' => '',
));

endif;