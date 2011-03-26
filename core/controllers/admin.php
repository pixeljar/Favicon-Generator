<?php

class pjfav_main_controller {
	
	function __construct() {
		register_activation_hook( PJFAV_ABS.'pj-favicon-generator.php', array( &$this, 'activate' ) );
		wp_register_style( 'pjfav-admin', PJFAV_CSS.'styles.css', array(), '2.0', 'screen' );
		
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	/**
	 * Activation code
	 *
	 * This script sets up the default options and checks for required php functionality (see below)
	 * JPG SUPPORT - imagecreatefromjpeg
	 * GIF SUPPORT - imagecreatefromgif
	 * PNG SUPPORT - imagecreatefrompng imagealphablending imagesavealpha
	 *
	 */
	function activate() {
		include_once( PJFAV_ABS.'install.php' );
	}
	
	/**
	 * Admin notifications
	 *
	 * If the server doesn't have the required functionality, alert the user
	 *
	 */
	function admin_notices() {
		
		// Only show the notice if we're looking the plugins screen or one of the Favicon Generator screens
		if (
			isset( $_SERVER['REQUEST_URI'] ) &&
			(
				( stripos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false ) ||
				( isset( $_GET['page'] ) && ( $_GET['page'] == PJFAV || $_GET['page'] == PJFAV.'-manage-icons' ) ) 
			)
		) :
			$options = get_option( PJFAV_ADMIN_OPTIONS );
			$errors = array();
			
			// Check for JPG support
			if( $options['jpg'] == false )
				$errors[] = __( 'imagecreatefromjpeg() - Creates a new image from JPG file or URL', PJFAV );
				
			// Check for GIF support
			if( $options['gif'] == false )
				$errors[] =  __( 'imagecreatefromgif() - Creates a new image from GIF file or URL', PJFAV );
				
			// Check for PNG support
			if( $options['png'] == false )
				$errors[] = __( 'imagecreatefrompng() - Creates a new image from PNG file or URL', PJFAV );
				
			if( $options['png-alpha-blending'] == false )
				$errors[] = __( 'imagealphablending() - Sets the blending mode for an image', PJFAV );
				
			if( $options['png-save-alpha'] == false )
				$errors[] = __( 'imagesavealpha() - Sets the flag to save full alpha channel information (as opposed to single-color transparency) when saving PNG images', PJFAV );
			
			if( count( $errors ) > 0 ) :
				echo '<div id="pj-favicon-install-error-message" class="error"><p><strong>'.__('Favicon Generator can\'t fully function on your web host. Your server is missing the following functionality:', PJFAV )."</strong></p><ul><li>";
				echo implode( '</li><li>', $errors );
				echo '</li></ul></div>';
			endif;
			
		endif;
	}
	
	/**
	 * Admin styles
	 *
	 * Include our styles on the plugin pages
	 *
	 */
	function admin_print_styles() {
		wp_enqueue_style( 'pjfav-admin' );
	}
	
	/**
	 * Create Favicon Generator menus
	 *
	 * Create the dashboard and the icon management menus
	 *
	 */
	function admin_menu() {
		add_menu_page( 'Favicon Generator', 'Favicon Generator', 'edit_themes', PJFAV, array( &$this, 'dashboard' ), PJFAV_URL.'images/menu-icon.png' );
		
		$dashboard = add_submenu_page( PJFAV, 'Dashboard', 'Dashboard', 'edit_themes', PJFAV, array( &$this, 'dashboard' ) );
		add_action( 'admin_print_styles-'.$dashboard, array( &$this, 'admin_print_styles' ), 1 );
		
		$management= add_submenu_page( PJFAV, 'Manage Icons', 'Manage Icons', 'edit_themes', PJFAV.'-manage-icons', array( &$this, 'manage' ) );
		add_action( 'admin_print_styles-'.$management, array( &$this, 'admin_print_styles' ), 1 );
	}
	
	/**
	 * Dashboard Page
	 *
	 * Display the dashboard view
	 *
	 */
	function dashboard() {
		$options = get_option( PJFAV_ADMIN_OPTIONS );
		include_once( PJFAV_CORE.'views/dashboard.php' );
	}
	
	/**
	 * Icon Management page
	 *
	 * Display the icon management view
	 *
	 */
	function manage() {
		include_once( PJFAV_CORE.'views/manage.php' );
	}
}
$pjfav_main_controller = new pjfav_main_controller;