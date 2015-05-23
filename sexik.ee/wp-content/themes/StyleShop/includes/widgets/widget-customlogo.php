<?php class CustomLogoWidget extends WP_Widget
{
    function CustomLogoWidget(){
		$widget_ops = array('description' => 'Displays Logo, Copyright and Additional Information');
		$control_ops = array('width' => 400, 'height' => 300);
		parent::WP_Widget(false,$name='ET Custom Logo Widget',$widget_ops,$control_ops);
    }

  /* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'About Me' : esc_html( $instance['title'] ) );
		$logoImagePath = empty($instance['logoImagePath']) ? '' : $instance['logoImagePath'];
		$copyrightInfo = empty($instance['copyrightInfo']) ? '' : $instance['copyrightInfo'];
		$textInfo = empty($instance['textInfo']) ? '' : $instance['textInfo'];

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
?>
<p id="footer-logo"><img alt="" src="<?php echo esc_attr( $logoImagePath ); ?>" /></p>
<p id="footer-copyright"><?php echo esc_html($copyrightInfo); ?></p>
<p><?php echo $textInfo; ?></p>

<?php
		echo $after_widget;
	}

  /*Saves the settings. */
    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['logoImagePath'] = esc_url_raw($new_instance['logoImagePath']);
		$instance['copyrightInfo'] = sanitize_text_field( $new_instance['copyrightInfo'] );
		$instance['textInfo'] = current_user_can('unfiltered_html') ? $new_instance['textInfo'] : stripslashes( wp_filter_post_kses( addslashes( $new_instance['textInfo'] ) ) );

		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('logoImagePath'=>'', 'copyrightInfo'=>'', 'textInfo'=>'','title' => '') );

		$title = $instance['title'];
		$logoImagePath = $instance['logoImagePath'];
		$copyrightInfo = $instance['copyrightInfo'];
		$textInfo = $instance['textInfo'];

		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr( $title ) . '" /></p>';
		# Logo Image
		echo '<p><label for="' . $this->get_field_id('logoImagePath') . '">' . 'Logo Image URL (174x37px):' . '</label><textarea cols="20" rows="2" class="widefat" id="' . $this->get_field_id('logoImagePath') . '" name="' . $this->get_field_name('logoImagePath') . '" >'. esc_textarea( $logoImagePath ) .'</textarea></p>';
		# Copyright
		echo '<p><label for="' . $this->get_field_id('copyrightInfo') . '">' . 'Copyright Text:' . '</label><input class="widefat" id="' . $this->get_field_id('copyrightInfo') . '" name="' . $this->get_field_name('copyrightInfo') . '" type="text" value="' . esc_html( $copyrightInfo ) . '" /></p>';
		# Text
		echo '<p><label for="' . $this->get_field_id('textInfo') . '">' . 'Text:' . '</label><textarea cols="20" rows="5" class="widefat" id="' . $this->get_field_id('textInfo') . '" name="' . $this->get_field_name('textInfo') . '" >'. esc_textarea( $textInfo ) .'</textarea></p>';
	}

}// end CustomLogoWidget class

function CustomLogoWidgetInit() {
  register_widget('CustomLogoWidget');
}

add_action('widgets_init', 'CustomLogoWidgetInit');

?>