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
		
		$options = get_option( PJFAV_ADMIN_OPTIONS );
		if( version_compare( $options['version'], PJFAV_VERSION, '<' ) )
			include_once( PJFAV_ABS.'upgrade.php' );
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
			
			// Return errors if there are any
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
		add_menu_page( 'Favicon Maker', 'Favicon Maker', 'edit_themes', PJFAV, array( &$this, 'dashboard' ), PJFAV_URL.'images/menu-icon.png' );
		
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

		global $wp_filesystem;
		
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

		global $wp_filesystem;
		
		$options = get_option( PJFAV_ADMIN_OPTIONS );
	
		$msg = array();

		// USER UPLOADED A NEW IMAGE
		if( isset( $_FILES['favicon'] ) && isset( $_POST['_wpnonce'] ) ) :

			// Check user capabilities
			if( !current_user_can( 'edit_themes' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
		
			// Check user intention
			if( !wp_verify_nonce( $_POST['_wpnonce'], PJFAV.'_upload' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
		
			$favicon		= $_FILES['favicon'];
			$favicon_name	= sanitize_file_name( $_FILES['favicon']['name'] );
			$favicon_size	= $_FILES['favicon']['size'];
			$favicon_temp	= $_FILES['favicon']['tmp_name'];
			$favicon_err	= $_FILES['favicon']['error'];
	
			// Check for any initial upload errors
			$error_count = count( $file_error );
			if( $error_count > 0 ) :
			
				$msg_type = 'error';
				$msg[] = esc_html( implode( '. ', $favicon_err ).'.' );
				include_once( PJFAV_CORE.'views/manage.php' );
				return;
				
			else :
				
				// Check for an existing favicon and delete
				if( $wp_filesystem->is_file( PJFAV_UPLOAD_PATH.'/favicon.ico' ) ) :
					if( !$wp_filesystem->unlink( PJFAV_UPLOAD_PATH.'/favicon.ico' ) ) :
					
						$msg_type = 'error';
						$msg[] = __( 'There was an error deleting the old favicon.', PJFAV );
						include_once( PJFAV_CORE.'views/manage.php' );
						return;
						
					endif;
				endif;
				
				// Let WordPress upload the file for us and handle security issues
				add_filter( 'upload_dir', array( &$this, 'upload_dir' ) );
				$uploaded_file = wp_handle_upload(
					$favicon,
					array(
						'mimes' => array(
							'jpg' => 'image/jpeg',
							'gif' => 'image/gif',
							'png' => 'image/png'
						)
					)
				);
				remove_filter( 'upload_dir', array( &$this, 'upload_dir' ) );
				if( isset( $uploaded_file['error'] ) ) :
				
					$msg_type = 'error';
					$msg[] = $uploaded_file['error'];
					include_once( PJFAV_CORE.'views/manage.php' );
					return;
					
				endif;
			
				// Include image resizing library
				require_once( PJFAV_CORE.'models/image.php' );
				
				// Resize the image to favicon size (16x16)
				$img =new Image( $uploaded_file['file'] );
				$img->resizeImage( 16, 16 );
				// add '-16' to the end of the image name before the extension
				$filename_parts = explode( '.', $uploaded_file['file'] );
				$filename_parts[ count($filename_parts) - 2 ] = $filename_parts[ count($filename_parts) - 2 ].'-16';
				$filetype = $filename_parts[ count($filename_parts) - 1 ];
				$filename = implode( '.', $filename_parts );
				// Save the resized image to the new filename
				$img->saveImage( $filename );
		
				// Create the favicon file
				// ImageICO function provided by JPEXS.com <http://www.jpexs.com/php.html>
				require_once( PJFAV_CORE.'models/ico2_1.php');
				ImageIco( $img->imageHandle, PJFAV_ABS.'favicon.ico' );

				// Update the options
				$options['favicon'] = $filename;
				$options['favicon_src'] = $uploaded_file['file'];
				$options['favicon_type'] = $filetype;
				update_option( PJFAV_ADMIN_OPTIONS, $options );
				
				$msg_type = 'updated';
				$msg[] = __( 'Your favicon has been updated.', PJFAV );
				include_once( PJFAV_CORE.'views/manage.php' );
				return;
				
			endif;

		endif; // isset( $_FILES['favicon'] ) && isset( $_POST['_wpnonce'] )

		// USER HAS CHOSEN TO DELETE AN UPLOADED IMAGE
		if ( isset( $_GET['d'] ) && isset( $_GET['_wpnonce'] ) ) :
		
			// Check user capabilities
			if( !current_user_can( 'edit_themes' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
			
			// Check user intention
			if( !wp_verify_nonce( $_GET['_wpnonce'], PJFAV.'_delete' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
			
			// Sanitize the file names
			$favicon = PJFAV_UPLOAD_PATH.'/'.sanitize_file_name( $_GET['d'] );
			$filename_parts = explode( '.', $favicon );
			$filename_parts[ count($filename_parts) - 2 ] = $filename_parts[ count($filename_parts) - 2 ].'-16';
			$favicon_src = implode( '.', $filename_parts );
			
			// Check if the files exist
			if( $wp_filesystem->is_file( $favicon ) && $wp_filesystem->is_file( $favicon_src ) ) :
			
				// Delete the favicon and the original source file
				if ( !$wp_filesystem->delete( $favicon ) || !$wp_filesystem->delete( $favicon_src ) ) :
					$msg_type = 'error';
					$msg[] = __( 'There was a problem deleting the selected image.', PJFAV );
				else :
					$msg_type = 'updated';
					$msg[] = __( 'The selected image has been deleted.', PJFAV );
				endif;
			else :
				$msg_type = 'error';
				$msg[] = __( 'The selected image could not be found.', PJFAV );
			endif;
			
			include_once( PJFAV_CORE.'views/manage.php' );
			return;
		endif;
	
		// USER HAS CHOSEN TO CHANGE HIS FAVICON TO A PREVIOUSLY UPLOADED IMAGE
		if ( isset( $_GET['u'] ) && isset( $_GET['_wpnonce'] ) ) :
		
			// Check user capabilities
			if( !current_user_can( 'edit_themes' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
			
			// Check user intention
			if( !wp_verify_nonce( $_GET['_wpnonce'], PJFAV.'_select' ) )
				wp_die( __( "You don't have permissions to do that.", PJFAV ) );
				
			// Sanitize the filename
			$favicon_name	= sanitize_file_name( $_GET['u'] );
			$filename_parts = explode( '.', $favicon_name );
			$filetype = $filename_parts[ count( $filename_parts ) - 1 ];
			$filename_parts[ count($filename_parts) - 2 ] = $filename_parts[ count($filename_parts) - 2 ].'-16';
			$favicon_small = implode( '.', $filename_parts );
			
			// Create the resource for us to create an .ico file
			switch( $filetype ) :
				case "jpg":
					$im = imagecreatefromjpeg( PJFAV_UPLOAD_PATH.'/'.$favicon_small );
					break;
				case "gif":
					$im = imagecreatefromgif( PJFAV_UPLOAD_PATH.'/'.$favicon_small );
					break;
				case "png":
					$im = imagecreatefrompng( PJFAV_UPLOAD_PATH.'/'.$favicon_small );
					imagealphablending( $im, true ); // setting alpha blending on
					imagesavealpha( $im, true ); // save alphablending setting (important)
					break;
			endswitch;
		
			// ImageICO function provided by JPEXS.com <http://www.jpexs.com/php.html>
			require_once( PJFAV_CORE.'models/ico2_1.php');
			ImageIco( $im, PJFAV_ABS.'favicon.ico' );
		
			// Update the options
			$options = get_option( PJFAV_ADMIN_OPTIONS );
			$options['favicon'] = $favicon_small;
			$options['favicon_src'] = $favicon_name;
			$options['favicon_type'] = $filetype;
			update_option( PJFAV_ADMIN_OPTIONS, $options );

			$msg[] = __( 'Your favicon has been updated.', PJFAV );
			include_once( PJFAV_CORE.'views/manage.php' );
			return;
		endif;
		
		// Default view
		include_once( PJFAV_CORE.'views/manage.php' );
	}
	
	function upload_dir( $uploads ) {
		$uploads['path'] = PJFAV_UPLOAD_PATH;
		$uploads['url'] = PJFAV_UPLOAD_URL;
		return $uploads;
	}
}
$pjfav_main_controller = new pjfav_main_controller;