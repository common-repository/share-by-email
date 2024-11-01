<?php

defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lhl-keyhandler.php';

/**
 * Extend the main WP_REST_Posts_Controller to a private endpoint controller.
 */

class Share_By_Email_Rest_API extends WP_REST_Posts_Controller {
 
    /**
     * The namespace.
     *
     * @var string
     */
    protected $namespace = 'share-by-email/v1';
 
    /**
     * Rest base for the current object.
     *
     * @var string
     */
    protected $rest_base = 'user';
    protected $rest_base_key = 'key';

    protected $terms_options;
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {

		$this->plugin_name = $plugin_name;
        $this->version = $version;

        $get_terms_options = get_option( 'tpul_settings_term_modal_options' );
        $this->terms_options = $get_terms_options;

        $license_key_handler = new LHL_Key_Handler($this->plugin_name, $this->version);
        $this->license_is_active = $license_key_handler->is_active();

	}

    /**
     * Register the routes for the objects of the controller.
     *
     * Nearly the same as WP_REST_Posts_Controller::register_routes(), but all of these
     * endpoints are hidden from the index.
     */
    public function register_routes() {

        /* Validate Key
         * wp-json/share-by-email/v1/user/activatekey
         */
        register_rest_route( $this->namespace, '/' . $this->rest_base_key . '/activatekey' , array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'activatekey' ),
                'permission_callback' => array( $this, 'activatekey_permission_check' ),
                'show_in_index'       => false,
            ),
        ) );

        
        /* Deactivate Key
         * wp-json/share-by-email/v1/user/deactivatekey
         */
        register_rest_route( $this->namespace, '/' . $this->rest_base_key . '/deactivatekey' , array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'deactivatekey' ),
                'permission_callback' => array( $this, 'deactivatekey_permission_check' ),
                'show_in_index'       => false,
            ),
        ) );

    }


    public function activatekey_permission_check ($request) {
        return true;
    }

    public function activatekey ($request) {
        $error = new WP_Error();
        $response = array();

        /**
         * Check if user is not logged in
         */
        $user_id = get_current_user_id();
        if ($user_id == 0){
            $error->add( "no_such_user", __( 'No such user 0' ), array( 'status' => 401 ) );
            return $error;
        }

        /**
         * Check Admin Referrer, make sure this is called by and Admin
         */
        check_admin_referer();

        /**
         * Check if nonce is bad
         */
        if ( rest_cookie_check_errors($request) ) {
            // Nonce is correct!
            $response['data'] = array('nonce'=>'correct');
        } else {
            // Don't send the data, it's a trap!
            $error->add( "no_such_user", __( 'No such user' ), array( 'status' => 401 ) );
            return $error;
        }        

        /**
         * Get Parameters
         */
        $parameters = $request->get_json_params();

        /**
         * Init Handler
         */

        $key_handler = new LHL_Key_Handler($this->plugin_name, $this->version);
        
        /**
         * Get License Key from request
         */
        if (empty($parameters["license_key"])){
            $error->add( "no_key_provided", __( $key_handler->get_message('no_key_provided') ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        /**
         * Activate License
         */

        // Decode response from activator.
        $_com_response = json_decode($key_handler->_comm__activate_key($parameters["license_key"]));     
        
        // return $key_handler->_comm__activate_key($parameters["license_key"]);
        // return $_com_response;
        

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $error->add( "json_parse_error", __( $key_handler->get_message('json_parse_error') . " " . json_last_error_msg() ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        } 
        if (empty($_com_response)) {
            $error->add( "empty_response", __( $key_handler->get_message('empty_response') ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        $response['_com_response'] = $_com_response;        

        /**
         * If License Key manager says there is no such key
         */
        if (!empty($_com_response->data->status) && $_com_response->data->status == 404){
            $error->add( "key_not_good", __( $key_handler->get_message('key_not_good') . ' ' . $_com_response->message ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        /**
         * Check if License expiration date is missing or expiration date in past
         */
        if (                 
                (!empty($_com_response->data->expiresAt) && $key_handler->check_if_a_date_is_in_past($_com_response->data->expiresAt)))
            {
            $error->add( "key_expired", __( $key_handler->get_message('key_expired') . ' expired at: '. $_com_response->data->expiresAt . ' ' . $_com_response->message ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        /**
         * Test For Success
         */
        if (
            ! empty($_com_response->success) &&
            ($_com_response->success == true) &&
            ! empty($_com_response->data->timesActivated) &&
            ($_com_response->data->timesActivated <= $_com_response->data->timesActivatedMax)
        ) {            

            // Save Stuff in WP DB and return success message
            $key_activated_succesfully = $key_handler->activate_key($parameters["license_key"], $_com_response->data->expiresAt);

                // Successfully activated by License Manager
                $response['code'] = "activated";
                $response['message'] = __($key_handler->get_message('activated'), "share-by-email");
                $response['key_activated_succesfully'] = $key_activated_succesfully;

                return new WP_REST_Response($response, 200);
           

        }

        
        /**
         * Was not able to Activate
         */
        $error->add( "unable_to_activate", __( $key_handler->get_message('unable_to_activate') . " " . $_com_response->message ), array( 'status' => 404 ) );
        $key_handler->flush_key_related_info();
        return $error;

    }

    public function deactivatekey_permission_check ($request) {
        return true;
    }

    public function deactivatekey ($request) {
        $error = new WP_Error();
        $response = array();

        /**
         * Check if user is not logged in
         */
        $user_id = get_current_user_id();
        if ($user_id == 0){
            $error->add( "no_such_user", __( 'No such user 0' ), array( 'status' => 401 ) );
            return $error;
        }

        /**
         * Check Admin Referrer, make sure this is called by and Admin
         */
        check_admin_referer();

        /**
         * Check if nonce is bad
         */
        if ( rest_cookie_check_errors($request) ) {
            // Nonce is correct!
            $response['data'] = array('nonce'=>'correct');
        } else {
            // Don't send the data, it's a trap!
            $error->add( "no_such_user", __( 'No such user' ), array( 'status' => 401 ) );
            return $error;
        }        

        /**
         * Get Parameters
         */
        $parameters = $request->get_json_params();

        /**
         * Init Handler
         */

        $key_handler = new LHL_Key_Handler($this->plugin_name, $this->version);
        
        /**
         * Get License Key from request
         */
        if (empty($parameters["license_key"])){
            $error->add( "no_key_provided", __( $key_handler->get_message('no_key_provided') ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        
        /**
         * Deactivate License
         */

        // Decode response from activator.
        $_com_response = json_decode($key_handler->_comm__deactivate_key($parameters["license_key"]));     
        
        // return $key_handler->_comm__activate_key($parameters["license_key"]);
        // return $_com_response;
        

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $error->add( "json_parse_error", __( $key_handler->get_message('json_parse_error')  . " " . json_last_error_msg()  ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        } 
        if (empty($_com_response)) {
            $error->add( "empty_response", __( $key_handler->get_message('empty_response') ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }

        $response['_com_response'] = $_com_response;        

        /**
         * If License Key manager says there is no such key
         */
        if (!empty($_com_response->data->status) && $_com_response->data->status == 404){
            $error->add( "key_not_good", __( $key_handler->get_message('key_not_good') . ' ' . $_com_response->message ), array( 'status' => 404 ) );
            $key_handler->flush_key_related_info();
            return $error;
        }


         /**
         * Test For Success
         */
        if (
            ! empty($_com_response->success) &&
            ($_com_response->success == true)
        ){
            $key_handler->flush_key_related_info();
            $response['code'] = "deactivated";
            $response['message'] = __($key_handler->get_message('deactivated'), "share-by-email") . "";
            return new WP_REST_Response($response, 200);
        }


        /**
         * Was not able to Activate
         */
        $error->add( "unable_to_deactivate", __( $key_handler->get_message('unable_to_deactivate') . " " . $_com_response->message ), array( 'status' => 404 ) );
        $key_handler->flush_key_related_info();
        return $error;


    }

}