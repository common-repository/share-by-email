<?php


/**
* @since      1.0.0
* @package    Share_By_Email
* @subpackage Share_By_Email/includes
* @author     Lehel Matyus <contact@lehelmatyus.com>
*/
class LHL_Key_Handler {


    public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    public function is_active() {
        return true;
	}

    
}


?>