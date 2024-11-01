<?php
/**
 * The Block Loader
 * @package RoxBlocks
 * @version 1.0.0
 *
 */

namespace RoxBlocks;

if( ! function_exists( 'add_action' ) ) {
	die();
}

/**
 * Blocks
 *
 */
class Blocks {
	/**
	 * Blocks constructor.
	 * @return void
	 */
    function __construct() {
    	add_action( 'enqueue_block_assets', array( $this, 'frontend_assets' ) );
    	add_filter( 'block_categories', array( $this, 'register_block_category' ), 1, 2 );
    	add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
    }

    /**
	 * Register Gutenberg block category
	 *
	 * @param array  $categories Block categories.
	 * @param object $post Post object.
	 *
	 * @return array
	 */
	function register_block_category( $categories, $post ) {
		return array_merge( $categories, array(
				array(
					'slug'  => 'rox-blocks',
					'title' => __( 'Essential Blocks', 'rox-gutne-blocks' ),
				),
				array(
					'slug'  => 'rox-templates',
					'title' => __( 'Essential Templates', 'rox-gutne-blocks' ),
				),
			)
		);
	}

    /**
     * Enqueue Gutenberg block assets for both frontend + backend.
     *
     * @uses {wp-editor} for WP editor styles.
     *
     * @return void
     */
    function frontend_assets() {
    	// Styles.
    	wp_enqueue_style(
    		'rox-wp_ebs-style-css', // Handle.
    		plugins_url( 'dist/blocks.style.build.css', RoxBlocks_ROOT_FILE ), // Block style CSS.
    		array( 'wp-editor' ), // Dependency to include the CSS after it.
    		filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
		);
    }

    /**
     * Enqueue Gutenberg block assets for backend editor.
     *
     * @uses {wp-blocks} for block type registration & related functions.
     * @uses {wp-element} for WP Element abstraction — structure of blocks.
     * @uses {wp-i18n} to internationalize the block's text.
     * @uses {wp-editor} for WP editor styles.
     *
     * @since 0.1.0
     */
    function editor_assets() {
		// Scripts.
		$roxGuteJsConfigs = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ),
				'csrf' => wp_create_nonce( '__RoxGutenCSRF__' ),
			)
		);
	    $theBlockDependenies = array(
	    	'wp-blocks',
		    'wp-block-library',
		    'wp-i18n',
		    'wp-element',
		    'wp-editor',
		    'wp-compose',
		    'wp-components',
		    'wp-url',
		    'wp-api-fetch',
		    'lodash',
	    );
    	wp_enqueue_script( 'rox-wp_ebs-editor-js', plugins_url( '/dist/blocks.build.js', RoxBlocks_ROOT_FILE ), $theBlockDependenies, filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), true );
		wp_localize_script( 'rox-wp_ebs-editor-js', 'rox_guten_config', apply_filters( 'rox-gutenberg-js-config', $roxGuteJsConfigs ) );
    	// Styles.
    	wp_enqueue_style( 'rox-wp_ebs-editor-css', plugins_url( 'dist/blocks.editor.build.css', RoxBlocks_ROOT_FILE ), array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) );
    }
}
// End of file blocks.php
