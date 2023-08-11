<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This class contains all of the plugin related settings.
 * Everything that is relevant data and used multiple times throughout 
 * the plugin.
 * 
 * To define the actual values, we recommend adding them as shown above
 * within the __construct() function as a class-wide variable. 
 * This variable is then used by the callable functions down below. 
 * These callable functions can be called everywhere within the plugin 
 * as followed using the get_plugin_name() as an example: 
 * 
 * GNPRODUCTA->settings->get_plugin_name();
 * 
 * HELPER COMMENT END
 */

/**
 * Class Gn_Product_And_Image_Remover_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package      GNCYPRODUCTREMOVER
 * @subpackage	Classes/Gn_Product_And_Image_Remover_Settings
 * @author		George Nicolaou
 * @since		1.0.0
 */
class Gn_Product_And_Image_Remover_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our Gn_Product_And_Image_Remover_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		$this->plugin_name = GNCYPRODUCTREMOVER_NAME;
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'GNPRODUCTA/settings/get_plugin_name', $this->plugin_name );
	}
}
