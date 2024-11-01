<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lhl-keyhandler.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.lehelmatyus.com/
 * @since      1.0.0
 *
 * @package    Perfecto_Portfolio
 * @subpackage Perfecto_Portfolio/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Perfecto_Portfolio
 * @subpackage Perfecto_Portfolio/admin
 * @author     Lehel Matyus <contact@lehelmatyus.com>
 */
class Sbe_Shortcodes {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
    private $version;

    private $license_key_handler;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
        $this->version = $version;
        // $this->license_key_handler = new Share_By_Email_Key_Handler($this->plugin_name, $this->version);

    }

    /**
	 * Provides default values Settings
	 *
	 * @return array
	 */
	public static function default_sbe_settings_gen_options() {
		$defaults = array(
            'sbe_share_by_email_on'            =>	'none',
            'sbe_share_by_email_link_display'  =>	'display_both',
            'sbe_license_key'	               =>	'',
            'sbe_license_key_valid_until'	   =>	'',
            'sbe_license_key_last_checked'	   =>	'',
		);
		return $defaults;
    }

    /**
	 * Provides default values Settings
	 *
	 * @return array
	 */
	public static function default_sbe_settings_em_options() {
		$defaults = array(
			'share_by_email_subject'	   =>	'[Blogname] | [Title]',
			'share_by_email_body'	       =>	'You may be interested in this article: <br> [excerpt] <br> [link]',
		);
		return $defaults;
	}

    /**
	 * Adds shortcodes to list content type
	 *
	 * @since    1.0.0
	 */
	public static function sbe_init_shortcodes() {
        add_shortcode( 'sbe-share-by-email', __CLASS__. '::do__share_by_email_shortcode' );
    }

    public static function do__share_by_email_shortcode( $atts ) {

        /**
         * Get options
         */

        $license_key_handler = new LHL_Key_Handler("Share_by_email","1.0.0");
        // $ia = $license_key_handler->is_active();
        $ia = true;

        // get general options
        $general_options = get_option( 'sbe_settings_general_options' );
        if( false ==  $general_options) {
			$general_options = self::default_sbe_settings_gen_options();
        }
        // get email options
        $email_options = get_option( 'sbe_settings_email_options' );
        if( false ==  $email_options) {
			$email_options = self::default_sbe_settings_em_options();
        }


        /**
         * Init variables
         */
        global $post;
        $the_title = '';
        if(!empty($post)){
            $the_title = get_the_title($post->ID);
        }
        $html_output = '';

        /**
         * Default values
         */

        $email_subject    = "[blogname] | [title]";
        $email_body       = "You may be interested in this article: %0D%0A[excerpt] %0D%0A[link]";

        if($ia){
            $email_subject    = $email_options['share_by_email_subject'];
            $email_body       = $email_options['share_by_email_body'];
        }

        $link_text = (!empty($general_options['link_text'])) ? $general_options['link_text'] : "Share by Email";
        $link_title = $link_text;
        $link_display_option = $general_options['sbe_share_by_email_link_display'];

        /**
         * Replace Tokens
         */

        /**
         * Subject Replace
         */
        $the_blogname = get_bloginfo();
        $search  = array('[blogname]','[title]');
        $replace = array($the_blogname, $the_title);

        $email_subject = str_ireplace($search, $replace, $email_subject);

        /**
         * Body Replace
         */
        $the_excerpt = get_the_excerpt();
        $the_link = get_the_permalink();
        $search  = array('[excerpt]','[link]');
        $replace = array($the_excerpt, $the_link);

        // Handle New lines
        $email_body = implode( "%0D%0A", array_map( 'sanitize_textarea_field', explode( "\n", $email_body ) ) );

        // replace
        $email_body = str_ireplace($search, $replace, $email_body);

        /**
         * Display options
         */

        /**
         * Text Options
         */
        // Clip Text if option is set to hide it
        $link_text_inline_style = '';
        if($link_display_option === 'display_icon_only'){

            // Hide Text
            $link_text_inline_style = 'position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            -webkit-clip-path: inset(50%);
            clip-path: inset(50%);
            border: 0;';

        }

        /**
         * Icon Options
         */
        $icon_color = '#307ec2';
        $show_link_icon = true;
        if($link_display_option === 'display_text_only'){
            // Hide Icon
            $show_link_icon = false;
        }

        $icon_refine_css = 'width: 25px; padding-top: 5px';
        $icon_refine_css = '';

        /**
         * Prepare output
         */
        $email_body = esc_html($email_body);
        $email_subject = esc_html($email_subject);
        $link_title = esc_html($link_title);
        
        $link_text_inline_style = esc_attr($link_text_inline_style);
        $link_text = esc_html($link_text);

        $html_output .= "<a class='sbe-share-link' style='text-decoration:none; cursor: pointer;' href='mailto:?subject={$email_subject}&amp;body={$email_body}' title='{$link_title}'>";

            $html_output .= "<span style='{$link_text_inline_style}'>{$link_text} </span>";

            if ($show_link_icon){
                $html_output .= "<span style='display:inline-block; width: 1.3em; {$icon_refine_css}'>";
                   $html_output .= '<?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 122.88 78.607" enable-background="new 0 0 122.88 78.607" xml:space="preserve"><g><path fill-rule="evenodd" fill="'. $icon_color .'" clip-rule="evenodd" d="M61.058,65.992l24.224-24.221l36.837,36.836H73.673h-25.23H0l36.836-36.836 L61.058,65.992L61.058,65.992z M1.401,0l59.656,59.654L120.714,0H1.401L1.401,0z M0,69.673l31.625-31.628L0,6.42V69.673L0,69.673z M122.88,72.698L88.227,38.045L122.88,3.393V72.698L122.88,72.698z"/></g></svg>';
                $html_output .= "</span>";
            }

        $html_output .= "</a>";

        return $html_output;
    }

}

// https://wordpress.stackexchange.com/questions/39918/wordpress-hooks-filters-insert-before-content-or-after-title
