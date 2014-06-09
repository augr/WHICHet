<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: WHICHet Widget Testing
Plugin URI: http://stuntandgimmicks.com/
Description: A simple way to a/b split test and optimize HTML widget content.
Author: Alexandre Mouravskiy
Version: 0.1.2
Author URI: http://stuntandgimmicks.com/
*/
/* Start Adding Functions Below this Line */


class WHICHet_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'whichet_widget_text', 'description' => __('A simple way to a/b split test and optimize HTML widget content.'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('wch', __('WHICHet'), $widget_ops, $control_ops);
	}
	
	public static function WCH_install () {
		/** Installer function to initialize tracking database */
		global $wpdb;
		$table_name = $wpdb->prefix . "whichet";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			WCH_id tinytext NOT NULL,
			WCH_A_text text NOT NULL,
			WCH_A_link VARCHAR(55) NOT NULL,
			WCH_A_display_count smallint NOT NULL,
			WCH_A_click_count smallint NOT NULL,
			WCH_B_text text NOT NULL,
			WCH_B_link VARCHAR(55) NOT NULL,
			WCH_B_display_count smallint NOT NULL,
			WCH_B_click_count smallint NOT NULL,
			UNIQUE KEY id (id)
    );";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function WCH_install () {
		/** Uninstaller function to initialize tracking database */

	}

	function widget( $args, $instance ) {
		extract($args);

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		/**
		 * Filter the content of the Text widget.
		 *
		 * @since 2.3.0
		 *
		 * @param string    $widget_text The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$category = apply_filters( 'widget_text', empty( $instance['category'] ) ? 'no_category_set' : $instance['category'], $instance );
		$text_A = apply_filters( 'widget_text', empty( $instance['text_A'] ) ? '' : $instance['text_A'], $instance );
		$text_B = apply_filters( 'widget_text', empty( $instance['text_B'] ) ? '' : $instance['text_B'], $instance );
		$link_A = apply_filters( 'widget_text', empty( $instance['link_A'] ) ? '' : $instance['link_A'], $instance );
		$link_B = apply_filters( 'widget_text', empty( $instance['link_B'] ) ? '' : $instance['link_B'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
		if ($_COOKIE["active_var"] == 0) {
			?>
				<script>window.onload = GA_tag('<?php echo $category; ?>', 'Variation A Total Count', <?php echo '1'; ?>); </script>
				<div class="textwidget"><a href="<?php echo $link_A; ?>" onclick="GA_tag('<?php echo $category; ?>', 'Variation A Engagement', <?php echo '0'; ?>)"><?php echo $text_A; ?></a></div>
			<?php 
		} else { 
			?>
				<script>window.onload = GA_tag('<?php echo $category; ?>', 'Variation B Total Count', <?php echo '1'; ?>); </script>
				<div class="textwidget"><a href="<?php echo $link_B; ?>" onclick="GA_tag('<?php echo $category; ?>', 'Variation B Engagement', <?php echo '0'; ?>)"><?php echo $text_B; ?></a></div>
			<?php 
		}
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['text_A'] =  $new_instance['text_A'];
			$instance['link_A'] =  $new_instance['link_A'];
			$instance['text_B'] =  $new_instance['text_B'];
			$instance['link_B'] =  $new_instance['link_B'];
			$instance['category'] =  $new_instance['category'];
		} else {
			$instance['text_A'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text_A']) ) ); // wp_filter_post_kses() expects slashed
			$instance['link_A'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['link_A']) ) );
			$instance['text_B'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text_B']) ) );
			$instance['link_B'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['link_B']) ) );
			$instance['category'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['category']) ) );
		}
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category' => '', 'text_A' => '', 'link_A' => '', 'text_B' => '', 'link_B' => '' ) );
		$title = strip_tags($instance['title']);
		$category = esc_textarea($instance['category']);
		$text_A = esc_textarea($instance['text_A']);
		$text_B = esc_textarea($instance['text_B']);
		$link_A = esc_textarea($instance['link_A']);
		$link_B = esc_textarea($instance['link_B']);
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Event Label:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo esc_attr($category); ?>" /></p>
		
			<p><label for="<?php echo $this->get_field_id('text_A'); ?>"><?php _e('Variation A Code:'); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text_A'); ?>" name="<?php echo $this->get_field_name('text_A'); ?>"><?php echo $text_A; ?></textarea>
			<label for="<?php echo $this->get_field_id('link_A'); ?>"><?php _e('Variant A Link:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_A'); ?>" name="<?php echo $this->get_field_name('link_A'); ?>" type="text" value="<?php echo esc_attr($link_A); ?>" /></p>
			<p><label for="<?php echo $this->get_field_id('text_B'); ?>"><?php _e('Variation B Code:'); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text_B'); ?>" name="<?php echo $this->get_field_name('text_B'); ?>"><?php echo $text_B; ?></textarea>
			<label for="<?php echo $this->get_field_id('link_B'); ?>"><?php _e('Variant B Link:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_B'); ?>" name="<?php echo $this->get_field_name('link_B'); ?>" type="text" value="<?php echo esc_attr($link_B); ?>" /></p>
		<?php
	}
	
	function log_me($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
	}
}

// Register and load the widget
function whichet_load_widget() {
	register_widget( 'WHICHet_widget' );
	wp_enqueue_script( 'whichet-script', plugins_url( 'whichet-js.js', __FILE__ ) );
}

function active_var() {
	if (!isset($_COOKIE["active_var"])){
		$path = parse_url(get_option('siteurl'), PHP_URL_PATH);
		$host = parse_url(get_option('siteurl'), PHP_URL_HOST);
		$expiry = strtotime('+1 month');
		$the_var = mt_rand(0,1);
		setcookie('active_var', $the_var, $expiry, '/', $host);
	}
}


add_action('init', 'active_var');
add_action( 'widgets_init', 'whichet_load_widget' );
register_activation_hook( __FILE__, array('WHICHet_Widget', 'WCH_install' );
register_deactivation_hook( __FILE__, array('WHICHet_Widget', 'WCH_uninstall' );
/* Stop Adding Functions Below this Line */
?>
