<?php

class WpLHLAdminUptimeGhost {


    public function __construct(){
        
    }
    /**---------------------------------------------------------------------
     * Seetings Page Layout
    ---------------------------------------------------------------------*/
    public static function uptimeGhostPage(){
        echo "<div>";
            echo "<div>";
                echo "<h1>";
                    echo "UpTime Ghost 👻";
                echo "</h1>";
            echo "</div>";

            echo "<div class='lhl__pb_10'>";
            echo "<p class='lhl__lead'>";
                echo __("<b><u>Get 120 Days free</u></b> Website Uptime monitoring. Pay only $15/year afterwards. Cancel any time.");
            echo "</p>";
                echo "<p class='lhl__lead'>";
                    echo __("Uptime Ghost is a setup and hassle free website uptime monitoring service provided by <a href='https://lehelmatyus.com/uptime-ghost' target='_blank'>LehelMatyus.com</a>.");
                echo "</p>";
                echo "<ul class='lhl__pl_20 lhl__lead'>";
                    echo "<li>";
                        echo __("→ Get instant email notification if your website goes down. ");
                    echo "</li>";
                    echo "<li>";
                        echo __("→ No technical skill required, we set it up for you. Just turn it on and Complete Sign up.");
                    echo "</li>";
                    echo "<li>";
                    echo __("→ Get a website uptime monitoring dashboard for your website.");
                    echo "</li>";
                    echo "<li>";
                        echo __("→ Direct support at: ") . '<a href="mailto:support@uptimeghost.com">support@uptimeghost.com</a>';
                        ;
                    echo "</li>";
            echo "</ul>";
            echo "</div>";

            echo "<div>";
                echo "<h2>";
                    echo "Get Started for free in less than 2 minutes";
                echo "</h2>";
                echo "<ul class='lhl__pl_20 lhl__lead '>";
                    echo "<li class='lhl__pb_10'>";
                        echo __("Step 1: Enable \"Website Uptime Detection\" for Uptime Ghost below and save changes.");
                    echo "</li>";
                    echo "<li class='lhl__pb_10'>";
                        echo __("Step 2: Signup for Sevice here: ") . "<a href='https://buy.stripe.com/28odR19hu1rj5pK9AB' target='_blank'>" . __("Get Uptime Ghost") . "</a>.";
                    echo "</li>";
                    echo "<li class='lhl__pb_10'>";
                        echo __("Step 3: We will add your site to our monitoring service annd send you the link to your uptime dashboard.");
                    echo "</li>";
                echo "</ul>";
            echo "</div>";

            echo "<div>";
            echo "</div>";
        echo "</div>";


        settings_fields( 'uptime_ghost_options' );
        do_settings_sections( 'uptime_ghost_options' );
        submit_button();




        // echo "<div>";
        //     echo "<h2>";
        //         echo "Useful Links";
        //     echo "</h2>";
        //     echo "<ul class='lhl__pl_20 lhl__lead'>";
        //         echo "<li>";
        //             echo "<a href='https://billing.stripe.com/p/login/eVa5m8biz5xX2A0cMM' target='_blank'>" . __("Your Subscription Dashboard") . "</a>.";
        //         echo "</li>";
        //     echo "</ul>";
        // echo "</div>";



    }

    /**---------------------------------------------------------------------
     * Seetings Page Form
    ---------------------------------------------------------------------*/
    public function initialize_uptime_ghost(  ) {

        if( false == get_option( 'uptime_ghost_options' ) ) {
			$default_array = $this->uptime_ghost_default_options();
			update_option( 'uptime_ghost_options', $default_array );
        }

        /**
         * Add Section
         */
        add_settings_section(
            'uptime_ghost_section',
            '',
            array( $this, 'uptimeroboto_options_callback'),
            'uptime_ghost_options'
        );

        /**
         * Add option to Section
         */

        add_settings_field(
            'uptime_ghost_enable',
            __( 'Website Uptime Detection', 'uptime-ghost' ),
            array( $this, 'uptime_ghost_enable_render'),
            'uptime_ghost_options',
            'uptime_ghost_section'
        );

        /**
         * Register Section
         */
        register_setting(
			'uptime_ghost_options',
			'uptime_ghost_options',
			array( $this, 'validate_uptime_ghost_options')
        );

    }

    /**---------------------------------------------------------------------
     * Seetings Page Validation
    ---------------------------------------------------------------------*/
    public function uptimeroboto_options_callback() {
		echo '<p>' . esc_html__( '', 'share-by-email' ) . '</p>';
        ?>

    <?php
    }
    /**---------------------------------------------------------------------
     * Seetings Page Validation
    ---------------------------------------------------------------------*/
    public function validate_uptime_ghost_options( $input ) {

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
		return apply_filters( 'validate_uptime_ghost_options', $output, $input );
    }
    /**---------------------------------------------------------------------
     * Seetings Page From Element Render
    ---------------------------------------------------------------------*/
    function uptime_ghost_enable_render(  ) {
        $options = get_option( 'uptime_ghost_options' );

        ?>
        <select name='uptime_ghost_options[uptime_ghost_enable]'>
            <option value='' <?php selected( $options['uptime_ghost_enable'], '' ); ?>><?php echo __('Disabled'); ?></option>
            <option value='enabled' <?php selected( $options['uptime_ghost_enable'], 'enabled' ); ?>><?php echo __('Enabled'); ?></option>
        </select>
        <p class="description">
            <?php echo __( '', 'share-by-email' ); ?>
        </p>

    <?php
    }
    /**---------------------------------------------------------------------
     * Seetings Page Default options
    ---------------------------------------------------------------------*/
    public function uptime_ghost_default_options() {
		$defaults = array(
            'uptime_ghost_enable'            =>	'',
		);
		return $defaults;
    }


    /**---------------------------------------------------------------------
     * REST API
    ---------------------------------------------------------------------*/

    public function uptime_ghost_rest_api_init() {
        $ghost_options = get_option( 'uptime_ghost_options' );
        // if enabled
        if(!empty($ghost_options['uptime_ghost_enable'])){
            register_rest_route( 'mywebsiteisonline/v1', '/verify', array(
                    'methods'  => 'GET',
                    'callback' => array( $this, 'uptime_ghost_output_code' ),
            ) );
        }

    }

    public function uptime_ghost_output_code() {
        wp_send_json( [
                'code' => esc_attr( "a0510241c3fc69fb65d81bea78bd5c5911c71cdb" )
        ] );
    }

}