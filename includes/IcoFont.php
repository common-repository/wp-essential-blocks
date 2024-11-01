<?php

namespace RoxBlocks;
/**
 * IcoFont
 * Handle Ajax Requests for IcoFont
 *
 * @package RoxBlocks
 * @subpackage IcoFont
 *
 */
class IcoFont {
	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;
	/**
	 * Initializes the RoxBlocks() class
	 *
	 * Checks for an existing RoxBlocks() instance
	 * and if it doesn't find one, creates it.
	 */
	public static function init() {
		
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function __construct(){
		add_action( 'wp_ajax__icofont', array( __CLASS__, 'handle_ajax_request' ) );
		add_action( 'enqueue_block_assets', array( $this, 'font_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'font_assets' ) );
	}
	public function font_assets() {
		wp_enqueue_style( 'icofont', plugins_url( 'dist/icofont.min.css', RoxBlocks_ROOT_FILE ), array(), '1.0.0' );
	}
	/**
	 * Verify Nonce
	 * @return bool
	 */
	private static function verify_csrf() {
		$nonce = '';
		if ( isset( $_REQUEST['_ajax_nonce'] ) )
			$nonce = $_REQUEST['_ajax_nonce'];
		elseif ( isset( $_REQUEST['_wpnonce'] ) )
			$nonce = $_REQUEST['_wpnonce'];
		elseif ( isset( $_REQUEST['_nonce'] ) )
			$nonce = $_REQUEST['_nonce'];
		// wp_die( -1, 403 );
		return (bool) wp_verify_nonce( $nonce, '__RoxGutenCSRF__' );
	}
	
	/**
	 * Handle Ajax Calls
	 * @return void
	 */
	public static function handle_ajax_request() {
		if( ! is_admin() ) {
			wp_send_json_error( __( 'Forbidden', 'wp-essential-blocks' ), 403 );
		}
		if( self::verify_csrf() ) {
			$icons = array_filter( self::get_icons(), function ($item) {
				$cat = strtolower( $item['category'] );
				$cat = preg_replace( '/\s/', '-', $cat );
				if ( $cat == $_REQUEST['category'] ) {
					return true;
				}
				return false;
			});
			wp_send_json_success( array_values( $icons ) );
		}
		wp_send_json_error( null, 403 );
	}
	/**
	 * Get icons json
	 * @param string $category      Optional. icon category, default null, will return all icons
	 * @return string|null
	 */
	public static function get_icons( $category = NULL ) {
		// get icon json
		$file = RoxBlock_ROOT_PATH . '/dist/icons.json';
		if( file_exists( $file ) ) {
			try{
				if( empty( $category ) ) return json_decode( file_get_contents( $file ), true );
				else {
					$icons = file_get_contents( $file );
					$icons = json_decode( $icons, true );
					if( isset( $icons[$category] ) ) {
						return json_encode( $icons[$category] );
					} else {
						return;
					}
				}
			} catch( Exception $e ) {
				error_log( $e->getMessage() );
				return;
			}
		} else {
			error_log( 'Icon json file missing from IcoFont Package' );
			return;
		}
	}
	/**
	 * get icon category json
	 * @return array|null
	 */
	public static function get_categories() {
		$file = RoxBlock_ROOT_PATH . '/dist/categories.json';
		if( file_exists( $file ) ) {
			try{
				return json_decode( file_get_contents( $file ), true );
			} catch( Exception $e ) {
				error_log( $e->getMessage() );
				return;
			}
		} else {
			error_log( 'Icon category json file missing from IcoFont Package' );
			return;
		}
	}
}
// End of file IcoFont.php
