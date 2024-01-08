<?php
// Creating the widget 
class scarica_app extends WP_Widget {
  
function __construct() {
parent::__construct(
  
// Base ID of your widget
'scarica_app', 
  
// Widget name will appear in UI
__('Scarica app FBF', 'scarica_app'), 
  
// Widget description
array( 'description' => __( 'Pulsanti per il download delle app per le prenotazioni delle visite', 'wpb_widget_domain' ), ) 
);
}
  
// Creating widget front-end
  
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
  
// before and after widget arguments are defined by themes
echo $args['before_widget'];
echo "<br><br>";
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
  
// This is where you run the code and display the output
echo __( 'L\'APP per le Prenotazioni On-line è disponibile su Android e IOS', 'wpb_widget_domain' );
echo $args['after_widget'];

?>
<br>
<div class="contenitore-scarica-app">
<a href="https://play.google.com/store/apps/details?id=it.melograno.fbfprenota">
<img alt="Scarica Prenotazioni on line da Android" src="<?php echo get_stylesheet_directory_uri(); ?>/img/Scarica-su-play-store.png" /></a>
<a href="https://itunes.apple.com/us/app/prenotazione-visite-fbf/id1046502796?l=it&ls=1&mt=8">
<img alt="Scarica Prenotazioni on line-da IOS" src="<?php echo get_stylesheet_directory_uri(); ?>/img/Scarica-su-app-store.png" /></a>
</div>
<?php
}
          
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'wpb_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
      
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
 
// Class wpb_widget ends here
} 
 
 
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'scarica_app' );
}
add_action( 'widgets_init', 'wpb_load_widget' );