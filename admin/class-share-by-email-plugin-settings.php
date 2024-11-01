<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lhl-keyhandler.php';

class Share_by_Email_Admin_Settings {

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

    /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $sbe_settings_email_options    The settings for the modal.
	 */
    private $sbe_settings_general_options;

    /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $sbe_settings_email_options    The settings for the modal.
	 */
    private $sbe_settings_email_options;


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
        $this->license_key_handler = new LHL_Key_Handler($this->plugin_name, $this->version);

    }

    /**
	 * This function introduces the theme options into the 'Settings' menu and into a top-level
	 * 'Perfecto Portfolio' menu.
	 */
	public function setup_plugin_options_menu() {
        add_submenu_page(
            'options-general.php',
			'Share By Email Settings', 					// The title to be displayed in the browser window for this page.
			'Share By Email',					        // The text to be displayed for this menu item
            'manage_options',					            // Which type of users can see this menu item
            'share_by_email_options',			        // The unique ID - that is, the slug - for this menu item
			array( $this, 'render_settings_page_content')	// The name of the function to call when rendering this menu's page
		);
    }

    /**---------------------------------------------------------------------
     * Default Options
     ---------------------------------------------------------------------*/

    public function default_general_options() {
		$defaults = array(
            'sbe_share_by_email_on'            =>	'none',
            'sbe_share_by_email_link_display'  =>	'display_both',
            'sbe_license_key'	               =>	'',
            'sbe_license_key_valid_until'	   =>	'',
            'sbe_license_key_last_checked'	   =>	'',
		);
		return $defaults;
    }

    public function default_email_options() {
		$defaults = array(
			'share_by_email_subject'	   =>	'[blogname] | [title]',
			'share_by_email_body'	       =>	'You may be interested in this article: [excerpt] - [link]',
		);
		return $defaults;
	}


    /**---------------------------------------------------------------------
     * Settings fields for General Options
     ---------------------------------------------------------------------*/

	/**
	 * Initializes the theme's activated options
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_general_options(  ) {

        if( false == get_option( 'sbe_settings_general_options' ) ) {
			$default_array = $this->default_general_options();
			update_option( 'sbe_settings_general_options', $default_array );
        }

        /**
         * Add Section
         */
        add_settings_section(
            'sbe_general_section',
            '<span class="dashicons dashicons-admin-generic"></span> ' . __( 'Display Settings', 'share-by-email' ),
            array( $this, 'general_options_callback'),
            'sbe_settings_general_options'
        );

        /**
         * Add option to Section
         */

        add_settings_field(
            'sbe_share_by_email_link_display',
            __( 'Display share link as', 'share-by-email' ),
            array( $this, 'sbe_share_by_email_link_display_render'),
            'sbe_settings_general_options',
            'sbe_general_section'
        );

        /**
         * Register Section
         */
        register_setting(
			'sbe_settings_general_options',
			'sbe_settings_general_options',
			array( $this, 'validate_general_options')
        );

    }

    /**
     * The Callback to assist with extra text
     */
    public function general_options_callback() {
		echo '<p>' . esc_html__( '', 'share-by-email' ) . '</p>';
        ?>

    <?php
    }

    /**
     * Validator Callback to assist in validation
     */
    public function validate_general_options( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} // end if
		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_general_options', $output, $input );
    }

    /**---------------------------------------------------------------------
     * Settings fields
     ---------------------------------------------------------------------*/

    /**
	 * Initializes the theme's activated options
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
    public function initialize_email_options( ){

        // delete_option('sbe_settings_email_options');

        if( false == get_option( 'sbe_settings_email_options' ) ) {
			$default_array = $this->default_email_options();
			update_option( 'sbe_settings_email_options', $default_array );
        }

        /**
         * Add Section
         */
        add_settings_section(
            'sbe_term_modal_section',
            __( 'Email Settings', 'share-by-email' ),
            array( $this, 'term_modal_options_callback'),
            'sbe_settings_email_options'
        );

        /**
         * Add option to Section
         */

        add_settings_field(
            'share_by_email_subject',
            __( 'Email Subject', 'share-by-email' ),
            array( $this, 'share_by_email_subject_render'),
            'sbe_settings_email_options',
            'sbe_term_modal_section'
        );

        add_settings_field(
            'share_by_email_body',
            __( 'Email Body', 'share-by-email' ),
            array( $this, 'share_by_email_body_render'),
            'sbe_settings_email_options',
            'sbe_term_modal_section'
        );

        /**
         * Register Section
         */
        register_setting(
			'sbe_settings_email_options',
			'sbe_settings_email_options',
			array( $this, 'validate_term_modal_options')
        );
    }

        /**
     * The Callback to assist with extra text
     */
    public function term_modal_options_callback() {
		echo '<p>' . __( 'What should show in the users default email client once he/she clicks the "Share by Email" icon.', 'share-by-email' ) . '</p>';
    }


    /**
     * Validator Callback to assist in validation
     */
    public function validate_term_modal_options( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} // end if
		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_term_modal_options', $output, $input );
    }



    /**---------------------------------------------------------------------
     * Render the actual page
     ---------------------------------------------------------------------*/

    /**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {

        $this->sbe_settings_email_options = get_option( 'sbe_settings_email_options' );
        $this->sbe_settings_general_options = get_option( 'sbe_settings_general_options' );

        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

            <h2><?php esc_html_e( 'Share By Email - Settings', 'terms_popup_on_user_login' ); ?></h2>

            <?php settings_errors(); ?>

            <?php if( isset( $_GET[ 'tab' ] ) ) {

				$active_tab = sanitize_key($_GET[ 'tab' ]);

			} else if( $active_tab == 'email_options' ) {

                $active_tab = 'email_options';

			} else if( $active_tab == 'uptimeghost' ) {

                $active_tab = 'uptimeghost';

             } else {
				$active_tab = 'general_options';
			}

            ?>

            <h2 class="nav-tab-wrapper">
				<a href="?page=share_by_email_options&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'General Settings', 'share-by-email' ); ?></a>
				<a href="?page=share_by_email_options&tab=email_options" class="nav-tab <?php echo $active_tab == 'email_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Email Settings', 'share-by-email' ); ?></a>
				<a href="?page=share_by_email_options&tab=uptimeghost" class="nav-tab <?php echo $active_tab == 'uptimeghost' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e( 'Uptime Monitoring', 'share-by-email' ); ?></a>
            </h2>

            <form method="post" action="options.php" class="lhl__admin_form">
				<?php

				if( $active_tab == 'email_options' ) {

					?>

						<div class="tg-outer">
                    
							<?php
								settings_fields( 'sbe_settings_email_options' );
								do_settings_sections( 'sbe_settings_email_options' );
								submit_button();
							?>
						</div>

					<?php
                }
                elseif( $active_tab == 'uptimeghost' ) {

                    ?>

                        <div class="tg-outer">
                            <?php
                                WpLHLAdminUptimeGhost::uptimeGhostPage();                                                            
                            ?>
                        </div>

                    <?php
                } else {
                    ?>                    
                    <?php

                    settings_fields( 'sbe_settings_general_options' );
                    do_settings_sections( 'sbe_settings_general_options' );
                    submit_button();

                    ?>

                    <hr/>
                    <h2><?php echo __('Demo','share-by-email'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><?php echo __('How it will look','share-by-email'); ?></th>
                                <td>
                                    <?php echo do_shortcode("[sbe-share-by-email]");?>
                                    <p class="description">
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr/>

                    <h2><?php echo __('How to use it','share-by-email'); ?></h2>
                    <p><?php echo __('By defaul the Share by Email link will not appear, you have to use it as a shortcode.','share-by-email'); ?><p>
                    <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo __('Shortcode avalable for you','share-by-email'); ?></th>
                            <td>
                                [sbe-share-by-email]
                                <br>
                                <?php echo __('Shortcode is now available for you. You can simply place it inside any Post or Page to show the "Share by Email" link.','share-by-email'); ?>
                                <p>
                                    <br>
                                    <?php echo __('If you have access to template files you can use the following line of code to place it where you wish.','share-by-email'); ?>
                                </p>
                                <textarea disabled rows="2" cols="70"><?php echo '<?php echo do_shortcode("[sbe-share-by-email]"); ?>' ?></textarea>
                                <p class="description">
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    </table>


                <?php


				} // end if/else
                ?>
                </form>

                <hr />
                <vr>
                <br/><br/>
                <div class="sbe__plugin-reviews">
                    <div class="sbe__plugin-reviews-rate">
                        <?php echo __('If you enjoy our plugin, please give it 5 stars on WordPress it helps me a lot:','share-by-email'); ?>
                        <a href="https://wordpress.org/support/plugin/share-by-email/reviews/?filter=5" target="_blank" title="Share By Email review">Rate the plugin</a>
                    </div>
                    <div class="sbe__plugin-reviews-support">
                        <?php echo __('If you have any questions on how to use the plugin, feel free to ask them:','share-by-email'); ?>
                        <a href="https://www.lehelmatyus.com/question/question-category/share-by-email-support" title="ask a question" >Support Questions</a>
                    </div>
                    <div class="sbe__plugin-reviews-donate">

                        <?php echo __('Donations play an important role, please consider donating:','share-by-email'); ?>
                        <span class="dashicons dashicons-carrot"></span>
                        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=EN83B8SEVVLX8&item_name=Help+Support+Share+by+Email+plugin&currency_code=USD&source=url" title="support the plugin" target="_blank" >Donate</a>
                    </div>
                </div>



            </div><!-- /.wrap -->
        <?php

    }

    /**---------------------------------------------------------------------
     * Helper functions to generate a field
     ---------------------------------------------------------------------*/


    function sbe_share_by_email_link_display_render(  ) {
        // return;
        $options = get_option( 'sbe_settings_general_options' );
        // delete_option('sbe_settings_general_options');
        // var_dump($options);
        ?>
        <select name='sbe_settings_general_options[sbe_share_by_email_link_display]'>
            <option value='display_both' <?php selected( $options['sbe_share_by_email_link_display'], 'display_both' ); ?>><?php echo __('Text and Icon'); ?></option>
            <option value='display_text_only' <?php selected( $options['sbe_share_by_email_link_display'], 'display_text_only' ); ?>><?php echo __('Text only'); ?></option>
            <option value='display_icon_only' <?php selected( $options['sbe_share_by_email_link_display'], 'display_icon_only' ); ?>><?php echo __('Icon Only'); ?></option>
        </select>
        <p class="description">
            <?php echo __( '', 'share-by-email' ); ?>
        </p>

    <?php
    }

    function share_by_email_subject_render(  ) {
        $options = $this->sbe_settings_email_options;
        ?>
        <input type='text' class="regular-text" name='sbe_settings_email_options[share_by_email_subject]' value='<?php echo $options['share_by_email_subject']; ?>'>
        <p class="description"> <?php echo __( 'The Subject of the email. <br/> Tokens available: <br/> - <b>[blogname]</b>: The name of your website <br/> - <b>[title]</b>: The title of the post or page your visitor is on when clicking the icon ', 'share-by-email' ); ?> </p>
        <?php
    }

    function share_by_email_body_render( ){
        $options = $this->sbe_settings_email_options;

        printf(
			'<textarea class="large-text" rows="5" name="sbe_settings_email_options[share_by_email_body]" id="share_by_email_body">%s</textarea>',
			isset( $options['share_by_email_body'] ) ? esc_attr( $options['share_by_email_body']) : ''
        );
        ?>

        <p class="description"> <?php echo __( 'The content of the email. All HTML will be stripped out. <br/> Tokens available: <br/> - <b>[excerpt]</b>: The excerpt of the post or page they are on when clicking the icon <br/> - <b>[link]</b>: The link of the post or page they are on when clicking the icon ', 'share-by-email' ); ?> </p>

        <?php
    }

}