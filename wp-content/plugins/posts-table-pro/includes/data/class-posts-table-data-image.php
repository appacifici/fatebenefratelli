<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the image column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Image extends Abstract_Posts_Table_Data {

	private $image_size;
	private $lightbox;

	public function __construct( $post, $links = '', $image_size = '', $lightbox = false ) {
		parent::__construct( $post, $links );

		$this->image_size	 = $image_size ? $image_size : 'thumbnail';
		$this->lightbox		 = $lightbox;
	}

	public function get_data() {
		$image			 = '';
		$attachment_id	 = get_post_thumbnail_id( $this->post );

		if ( $attachment_id ) {
			$wrapper_class	 = 'posts-table-image-wrapper';
			$wrapper_width	 = Posts_Table_Util::get_image_size_width( $this->image_size );

			$thumbnail_src	 = wp_get_attachment_image_src( $attachment_id, $this->image_size );
			$full_src		 = wp_get_attachment_image_src( $attachment_id, apply_filters( 'posts_table_image_full_size', 'full' ) );
			$atts			 = array(
				'title'						 => get_post_field( 'post_title', $attachment_id ),
				'alt'						 => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
				'data-caption'				 => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'					 => $full_src[0],
				'data-large_image'			 => $full_src[0],
				'data-large_image_width'	 => $full_src[1],
				'data-large_image_height'	 => $full_src[2],
				'class'						 => ''
			);

			// Caption fallback
			$atts['data-caption'] = empty( $atts['data-caption'] ) ? trim( esc_attr( strip_tags( $this->post->post_title ) ) ) : $atts['data-caption'];

			// Alt fallbacks
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['data-caption'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? $atts['title'] : $atts['alt'];
			$atts['alt'] = empty( $atts['alt'] ) ? trim( esc_attr( strip_tags( $this->post->post_title ) ) ) : $atts['alt'];

			// Get the image
			$image = wp_get_attachment_image( $attachment_id, $this->image_size, false, $atts );

			// Wrap image with lightbox markup or post link - lightbox takes priority over the 'links' option.
			if ( $this->lightbox ) {
				$image			 = '<a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>';
				$wrapper_class	 .= ' posts-table-gallery__image';
			} elseif ( array_intersect( array( 'all', 'image' ), $this->links ) ) {
				$image = Posts_Table_Util::format_post_link( $this->post, $image );
			}

			$image = '<div style="' . ( $wrapper_width ? esc_attr( sprintf( 'width:%upx;', $wrapper_width ) ) : '' ) . '" data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="' . esc_attr( $wrapper_class ) . '">' . $image . '</div>';
		}

		return apply_filters( 'posts_table_data_image', $image, $this->post );
	}

}