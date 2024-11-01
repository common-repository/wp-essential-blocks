<?php
/**
 * Plugin Name: WordPress Essential Blocks
 * Plugin URI: https://pluguinrox.com/gutenberg-essential-blocks/
 * Description: Powerful and Feature Rich Blocks for Gutenberg Editor.
 * Author: PluginRox
 * Author URI: https://getRoxBlocks.com/
 * Version: 1.0.2
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Gutenberg Essential Blocks is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Gutenberg Essential Blocks is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Gutenberg Essential Blocks. If not, see <http://www.gnu.org/licenses/>.
 *
 */
// No Direct Access
if ( ! function_exists( 'add_action' ) ) {
	die();
} 

define( 'RoxBlock_ROOT_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The Loader
 */
class RoxBlocks {
	
    /**
     * Plugin version
     * @access public
     * @var string
     */
    public $version = '1.0.2';
	
	/**
	 * Plugin Slug
	 * Plugin Slug for internal prefix uses.
	 * @access
	 * @var string
	 */
    public $pluginName = 'gutenberg-essential-blocks';
	
	/**
	 * Plugin TextDomain
	 * @access public
	 * @var string
	 */
    public $textDomain = 'wp-essential-blocks';

    /**
     * The Singleton instance holder.
     * @access protected
     * @var RoxBlocks
     */
    protected static $_instance = null;
    
	/**
	 * Get Current Instance of this class
	 * @return RoxBlocks
	 */
	public static function instance() {
		
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
    /**
     * @.@ Constructor
     *
     * @uses is_admin()
     * @uses add_action()
     *
     * @return void
     */
    public function __construct() {
        $this->define_constants();
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        if ( ! $this->is_gutenberg_active() ) {
        	$this->admin_error();
        	return;
        }
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'RoxBlocks_VERSION', $this->version );
        define( 'RoxBlocks_ROOT_FILE', __FILE__ );
        define( 'RoxBlocks_ROOT_PATH', plugin_dir_path( __FILE__ ) );
    }

    /**
     * Initialize the plugin
     * @return void
     */
    public function init_plugin() {
	    $this->load_plugin_textdomain();
        $this->includes();
        do_action( '_init' );
    }

    /**
     * Check if Gutenberg is active
     *
     * @return boolean
     */
    public function is_gutenberg_active() {
    	return function_exists( 'register_block_type' );
    }

    /**
     * Placeholder for activation function
     * @return void
     */
    public function activate() {
    	if ( ! get_option( 'RoxBlocks_installed' ) ) {
    	    update_option( 'RoxBlocks_installed', 1 );
    	}
    	update_option( 'RoxBlocks_version', RoxBlocks_VERSION );
    }

    /**
     * Include the required files
     * @return void
     */
    public function includes() {
    	require_once( __DIR__ . '/includes/Blocks.php' );
	    require_once( __DIR__ . '/includes/IcoFont.php' );
	    // Initailize Classes
    	new RoxBlocks\Blocks();
	    RoxBlocks\IcoFont::init();
    }
    
	/**
	 * Load Language files
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'rox-gebs', false, __DIR__ . '/languages' );
	}
	
    /**
     * Admin notice
     * Notify if Gutenberg is unavailable
     * @return void
     */
    public function admin_error() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        printf( '<div class="notice notice-error"><p>%s</p></div>', __( 'Gutenberg Essential Blocks requires Gutenberg plugin installed or WordPress 5.0 or later.', 'rox-gebs') );
    }
}
RoxBlocks::instance();
// End of file gutenberg-essential-blocks.php
