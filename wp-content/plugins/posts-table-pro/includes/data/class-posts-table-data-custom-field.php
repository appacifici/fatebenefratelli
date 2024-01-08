<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the post data for a custom field column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Custom_Field extends Abstract_Posts_Table_Data {

	private $field;
	private $image_size;
	private $date_format;
	private $is_date;
	private $acf_field_object;

	public function __construct( $post, $field, $links = '', $image_size = '', $date_format = '', $is_date = false ) {
		parent::__construct( $post, $links );

		$this->field		 = $field;
		$this->image_size	 = $image_size ? $image_size : 'thumbnail';
		$this->date_format	 = $date_format;
		$this->is_date		 = $is_date;

		$this->acf_field_object = Posts_Table_Util::get_acf_field_object( $this->field, $this->post->ID );
	}

	public function get_data() {
		$is_acf_date_picker = false;

		if ( $this->acf_field_object ) {
			// Advanced Custom Fields custom field
			$cf_value			 = $this->get_acf_value( $this->acf_field_object, $this->post->ID );
			$is_acf_date_picker	 = ! empty( $this->acf_field_obj['type'] ) && in_array( $this->acf_field_obj['type'], array( 'date_picker', 'date_time_picker' ) );
		} else {
			// Standard custom field
			$cf_value = get_post_meta( $this->post->ID, $this->field, false );
		}

		// Flatten field
		$cf_value = array_reduce( (array) $cf_value, array( $this, 'flatten_custom_field' ), '' );

		// Format as date if this is a date custom field and we have a date format.
		if ( $this->is_date && $this->date_format && ! $is_acf_date_picker ) {
			if ( $timestamp = Posts_Table_Util::strtotime( $cf_value, $this->date_format ) ) {
				$cf_value = date( $this->date_format, $timestamp );
			}
		}

		// Format as link if custom field is a URL
		if ( 0 === strpos( $cf_value, 'http' ) && $url = filter_var( $cf_value, FILTER_VALIDATE_URL ) ) {
			$link_text	 = str_replace( array( 'http://', 'https://' ), '', $cf_value );
			$cf_value	 = sprintf( '<a href="%1$s">%2$s</a>', apply_filters( 'posts_table_url_custom_field_link', $url, $this->field, $this->post ), apply_filters( 'posts_table_url_custom_field_text', $link_text, $this->field, $this->post ) );
		}

		/**
		 * @deprecated 1.4.8 - Replaced by 'posts_table_data_custom_field'.
		 */
		$cf_value = apply_filters( 'posts_table_custom_field_value', $cf_value, $this->post->ID, $this->field );

		// Filter the result.
		$cf_value	 = apply_filters( 'posts_table_data_custom_field', $cf_value, $this->field, $this->post );
		$cf_value	 = apply_filters( 'posts_table_data_custom_field_' . $this->field, $cf_value, $this->post );

		return $cf_value;
	}

	public function get_sort_data() {
		if ( $this->is_date ) {
			$date	 = get_post_meta( $this->post->ID, $this->field, true );
			$format	 = $this->date_format;

			if ( $this->acf_field_object && in_array( $this->acf_field_object['type'], array( 'date_picker', 'date_time_picker' ) ) ) {
				$format = $this->acf_field_object['return_format'];
			}

			// Format the hidden date column for sorting
			if ( $timestamp = Posts_Table_Util::strtotime( $date, $format ) ) {
				return $timestamp;
			}
		}
		return '';
	}

	private function get_acf_value( $field_obj, $post_id = false ) {
		if ( ! $field_obj || ! isset( $field_obj['value'] ) || '' === $field_obj['value'] || 'null' === $field_obj['value'] || empty( $field_obj['type'] ) ) {
			return '';
		}

		$cf_value = $field_obj['value'];

		switch ( $field_obj['type'] ) {
			case 'text':
			case 'number':
			case 'email':
			case 'password':
			case 'color_picker':
			case 'textarea':
			case 'wysiwyg':
			case 'google_map':
				$cf_value	 = get_field( $field_obj['name'], $post_id, true );
				break;
			case 'date_picker':
			case 'date_time_picker':
				if ( $timestamp	 = Posts_Table_Util::strtotime( $cf_value, $field_obj['return_format'] ) ) {
					// Default to 'date_format' option if specified, otherwise use the 'display format' for the date field
					$date_format = $this->date_format ? $this->date_format : $field_obj['display_format'];
					$cf_value	 = date( $date_format, $timestamp );
				}
				break;
			case 'time_picker':
				if ( $timestamp = Posts_Table_Util::strtotime( $cf_value, $field_obj['return_format'] ) ) {
					$cf_value = date( $field_obj['display_format'], $timestamp );
				}
				break;
			case 'radio':
				if ( ! empty( $field_obj['choices'] ) && ( is_int( $cf_value ) || is_string( $cf_value ) ) && isset( $field_obj['choices'][$cf_value] ) ) {
					$cf_value = $field_obj['choices'][$cf_value];
				}
				break;
			case 'select':
			case 'checkbox':
				if ( ! empty( $field_obj['choices'] ) && ( is_string( $cf_value ) || is_int( $cf_value ) || is_array( $cf_value ) ) ) {
					$labels = array();

					foreach ( (array) $cf_value as $value ) {
						if ( isset( $field_obj['choices'][$value] ) ) {
							$labels[] = $field_obj['choices'][$value];
						} else {
							$labels[] = $value;
						}
					}
					$cf_value = $labels;
				}
				break;
			case 'true_false':
				$cf_value	 = $cf_value ? __( 'True', 'posts-table-pro' ) : __( 'False', 'posts-table-pro' );
				break;
			case 'file':
				$cf_value	 = wp_get_attachment_link( $cf_value, $this->image_size, false, true );
				break;
			case 'image':
				$cf_value	 = wp_get_attachment_link( $cf_value, $this->image_size );
				break;
			case 'page_link':
			case 'post_object':
			case 'relationship':
				$cf_value	 = array_map( array( $this, 'get_post_title' ), (array) $cf_value );
				break;
			case 'taxonomy':
				$term_links	 = array();
				foreach ( (array) $cf_value as $term_id ) {
					if ( $term = get_term_by( 'id', $term_id, $field_obj['taxonomy'] ) ) {
						if ( array_intersect( array( 'all', 'terms' ), $this->links ) ) {
							$term_links[] = sprintf( '<a href="%1$s" rel="tag">%2$s</a>', esc_url( get_term_link( $term_id, $field_obj['taxonomy'] ) ), $term->name );
						} else {
							$term_links[] = $term->name;
						}
					}
				}
				$cf_value	 = $term_links;
				break;
			case 'user':
				$users		 = array();
				foreach ( (array) $cf_value as $user_id ) {
					if ( array_intersect( array( 'all', 'author' ), $this->links ) ) {
						$users[] = sprintf(
							'<a href="%1$s" rel="author">%2$s</a>', esc_url( get_author_posts_url( $user_id ) ), get_the_author_meta( 'display_name', $user_id )
						);
					} else {
						$users[] = get_the_author_meta( 'display_name', $user_id );
					}
				}
				$cf_value		 = $users;
				break;
			case 'repeater':
				$repeater_value	 = array();

				if ( have_rows( $field_obj['name'], $post_id ) ) {
					while ( have_rows( $field_obj['name'], $post_id ) ) {
						the_row();

						foreach ( $field_obj['sub_fields'] as $sub_field ) {
							$sub_field_value	 = $this->get_acf_value( get_sub_field_object( $sub_field['name'], false ), $post_id );
							$repeater_value[]	 = apply_filters( 'posts_table_acf_sub_field_value', $sub_field_value, $sub_field['name'], $field_obj['name'], $post_id );
						}
					}
				}
				$cf_value = apply_filters( 'posts_table_acf_repeater_field_value', $repeater_value );
				break;
			//@todo: Other layout field types?
		}

		return apply_filters( 'posts_table_acf_value', $cf_value, $field_obj, $post_id );
	}

	private function flatten_custom_field( $carry, $item ) {
		if ( is_array( $item ) ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field_row' );
			}
			$carry .= array_reduce( $item, array( $this, 'flatten_custom_field' ), '' );
		} elseif ( '' !== $item && false !== $item ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field' );
			}
			$carry .= $item;
		}
		return $carry;
	}

	private function get_post_title( $post_id ) {
		$title_data = new Posts_Table_Data_Title( get_post( $post_id ), $this->links );
		return $title_data->get_data();
	}

}