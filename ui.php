<?php

function sc_render_login_form_social_connect( $args = NULL ) {

	if( $args == NULL )
		$display_label = true;
	elseif ( is_array( $args ) )
		extract( $args );

	if( !isset( $images_url ) )
		$images_url = SOCIAL_CONNECT_PLUGIN_URL . '/media/img/';

	$twitter_enabled = get_option( 'social_connect_twitter_enabled' ) && get_option( 'social_connect_twitter_consumer_key' ) && get_option( 'social_connect_twitter_consumer_secret' );
	$facebook_enabled = get_option( 'social_connect_facebook_enabled', 1 ) && get_option( 'social_connect_facebook_api_key' ) && get_option( 'social_connect_facebook_secret_key' );
	$google_enabled = get_option( 'social_connect_google_enabled', 1 );
	$yahoo_enabled = get_option( 'social_connect_yahoo_enabled', 1 );
	$wordpress_enabled = get_option( 'social_connect_wordpress_enabled', 1 );
	?>
	<div class="social_connect_ui <?php if( strpos( $_SERVER['REQUEST_URI'], 'wp-signup.php' ) ) echo 'mu_signup'; ?>">
		<?php if( $display_label !== false ) : ?>
			<div style="margin-bottom: 3px;"><label><?php _e( 'Connect with', 'social_connect' ); ?>:</label></div>
		<?php endif; ?>
		<div class="social_connect_form" title="Social Connect">
			<?php if( $facebook_enabled ) : ?>
				<a href="javascript:void(0);" title="Facebook" class="social_connect_login_facebook"><img alt="Facebook" src="<?php echo $images_url . 'facebook_32.png' ?>" /></a>
			<?php endif; ?>
			<?php if( $twitter_enabled ) : ?>
				<a href="javascript:void(0);" title="Twitter" class="social_connect_login_twitter"><img alt="Twitter" src="<?php echo $images_url . 'twitter_32.png' ?>" /></a>
			<?php endif; ?>
			<?php if( $google_enabled ) : ?>
				<a href="javascript:void(0);" title="Google" class="social_connect_login_google"><img alt="Google" src="<?php echo $images_url . 'google_32.png' ?>" /></a>
			<?php endif; ?>
			<?php if( $yahoo_enabled ) : ?>
				<a href="javascript:void(0);" title="Yahoo" class="social_connect_login_yahoo"><img alt="Yahoo" src="<?php echo $images_url . 'yahoo_32.png' ?>" /></a>
			<?php endif; ?>
			<?php if( $wordpress_enabled ) : ?>
				<a href="javascript:void(0);" title="WordPress.com" class="social_connect_login_wordpress"><img alt="Wordpress.com" src="<?php echo $images_url . 'wordpress_32.png' ?>" /></a>
			<?php endif; ?>
		</div>

		<?php
	$social_connect_provider = isset( $_COOKIE['social_connect_current_provider']) ? $_COOKIE['social_connect_current_provider'] : '';

?>
	<div id="social_connect_facebook_auth">
		<input type="hidden" name="client_id" value="<?php echo get_option( 'social_connect_facebook_api_key' ); ?>" />
		<input type="hidden" name="redirect_uri" value="<?php echo urlencode( SOCIAL_CONNECT_PLUGIN_URL . '/facebook/callback.php' ); ?>" />
	</div>
	<div id="social_connect_twitter_auth"><input type="hidden" name="redirect_uri" value="<?php echo( SOCIAL_CONNECT_PLUGIN_URL . '/twitter/connect.php' ); ?>" /></div>
	<div id="social_connect_google_auth"><input type="hidden" name="redirect_uri" value="<?php echo( SOCIAL_CONNECT_PLUGIN_URL . '/google/connect.php' ); ?>" /></div>
	<div id="social_connect_yahoo_auth"><input type="hidden" name="redirect_uri" value="<?php echo( SOCIAL_CONNECT_PLUGIN_URL . '/yahoo/connect.php' ); ?>" /></div>
	<div id="social_connect_wordpress_auth"><input type="hidden" name="redirect_uri" value="<?php echo( SOCIAL_CONNECT_PLUGIN_URL . '/wordpress/connect.php' ); ?>" /></div>

	<div class="social_connect_wordpress_form" title="WordPress">
		<p><?php _e( 'Enter your WordPress.com blog URL', 'social_connect' ); ?></p><br/>
		<p>
			<span>http://</span><input class="wordpress_blog_url" size="15" value=""/><span>.wordpress.com</span> <br/><br/>
			<a href="javascript:void(0);" class="social_connect_wordpress_proceed"><?php _e( 'Proceed', 'social_connect' ); ?></a>
		</p>
	</div>
</div> <!-- End of social_connect_ui div -->
<?php
}
add_action( 'login_form',          'sc_render_login_form_social_connect' );
add_action( 'register_form',       'sc_render_login_form_social_connect' );
add_action( 'after_signup_form',   'sc_render_login_form_social_connect' );
add_action( 'social_connect_form', 'sc_render_login_form_social_connect' );


function sc_social_connect_add_comment_meta( $comment_id ) {
	$social_connect_comment_via_provider = isset( $_POST['social_connect_comment_via_provider']) ? $_POST['social_connect_comment_via_provider'] : '';
	if( $social_connect_comment_via_provider != '' ) {
		update_comment_meta( $comment_id, 'social_connect_comment_via_provider', $social_connect_comment_via_provider );
	}
}
add_action( 'comment_post', 'sc_social_connect_add_comment_meta' );


function sc_social_connect_render_comment_meta( $link ) {
	global $comment;
	$images_url = SOCIAL_CONNECT_PLUGIN_URL . '/media/img/';
	$social_connect_comment_via_provider = get_comment_meta( $comment->comment_ID, 'social_connect_comment_via_provider', true );
	if( $social_connect_comment_via_provider && current_user_can( 'manage_options' )) {
		return $link . '&nbsp;<img class="social_connect_comment_via_provider" alt="'.$social_connect_comment_via_provider.'" src="' . $images_url . $social_connect_comment_via_provider . '_16.png"  />';
	} else {
		return $link;
	}
}
add_action( 'get_comment_author_link', 'sc_social_connect_render_comment_meta' );


function sc_render_comment_form_social_connect() {
	if( comments_open() && !is_user_logged_in()) {
		sc_render_login_form_social_connect();
	}
}
add_action( 'comment_form_top', 'sc_render_comment_form_social_connect' );


function sc_render_login_page_uri(){
	?>
	<input type="hidden" id="social_connect_login_form_uri" value="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" />
	<?php
}
add_action( 'wp_footer', 'sc_render_login_page_uri' );


/**
 * SocialConnectWidget Class
 */
class SocialConnectWidget extends WP_Widget {
	/** constructor */
	function SocialConnectWidget() {
		parent::WP_Widget(
			'social_connect', //unique id
			'Social Connect', //title displayed at admin panel
			//Additional parameters
			array( 
				'description' => __( 'Login or register with Facebook, Twitter, Yahoo, Google or a Wordpress.com account', 'social_connect' ))
			);
	}

	/** This is rendered widget content */
	function widget( $args, $instance ) {
		extract( $args );
		
		if($instance['hide_for_logged_in']==1 && is_user_logged_in()) return;
		
		echo $before_widget;

		if( !empty( $instance['title'] ) ){
			$title = apply_filters( 'widget_title', $instance[ 'title' ] );
			echo $before_title . $title . $after_title;
		}

		if( !empty( $instance['before_widget_content'] ) ){
			echo $instance['before_widget_content'];
		}

		sc_render_login_form_social_connect( array( 'display_label' => false ) );

		if( !empty( $instance['after_widget_content'] ) ){
			echo $instance['after_widget_content'];
		}

		echo $after_widget;
	}

	/** Everything which should happen when user edit widget at admin panel */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['before_widget_content'] = $new_instance['before_widget_content'];
		$instance['after_widget_content'] = $new_instance['after_widget_content'];
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];

		return $instance;
	}

	/** Widget edit form at admin panel */
	function form( $instance ) {
		/* Set up default widget settings. */
		$defaults = array( 'title' => '', 'before_widget_content' => '', 'after_widget_content' => '' );

		foreach( $instance as $key => $value ) 
			$instance[ $key ] = esc_attr( $value );

		$instance = wp_parse_args( (array)$instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'social_connect' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			<label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'social_connect' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" />
			<label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'social_connect' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" />
			<br /><br /><label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'social_connect' ); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value="1" <?php if($instance['hide_for_logged_in']==1) echo 'checked="checked"'; ?> />
		</p>
		<?php
}

}
add_action( 'widgets_init', create_function( '', 'return register_widget( "SocialConnectWidget" );' ));


function sc_social_connect_shortcode_handler( $args ) {
	if( !is_user_logged_in()) {
		sc_render_login_form_social_connect();
	}
}
add_shortcode( 'social_connect', 'sc_social_connect_shortcode_handler' );
