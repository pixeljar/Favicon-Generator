<?php

class pjfav_main_controller {
	
	function __construct() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		
		add_action( 'admin_print_scripts-'.PJFAV, array( &$this, 'admin_print_scripts') );
		add_action( 'admin_print_scripts-'.PJFAV.'manage-icons', array( &$this, 'admin_print_scripts') );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function activate() {
		include_once( PJFAV_ABS.'install.php' );
	}
	
	function admin_notices() {
		if ( (stripos($_SERVER['REQUEST_URI'],'plugins.php') !== false) || (!empty($_GET['page']) && $_GET['page'] == "Favicon-Generator" ) ) :
			$dir_list = "";
			$dir_list2 = "";

			$uploaddir = PJFAV_ABS.'uploads/';
			wp_mkdir_p( $uploaddir );

			if (!is_dir(PJFAV_ABS)){
				$dir_list2.= "<li>".PJFAV_ABS . "</li>";
			} elseif (!is_writable(PJFAV_ABS)){
				$dir_list.= "<li>".PJFAV_ABS . "</li>";
			}

			if (!is_dir($uploaddir)){
				$dir_list2.= "<li>".$uploaddir . "</li>";
			} elseif (!is_writable($uploaddir)){
				$dir_list.= "<li>".$uploaddir . "</li>";
			}
	
			if ($dir_list2 != ""){
				echo "<div id='pj-favicon-install-error-message' class='error'><p><strong>".__('Favicon Generator is not ready yet.', 'pj-favicon-generator')."</strong> ".__('must create the following folders (and must chmod 777):', 'pj-favicon-generator')."</p><ul>";
				echo $dir_list2;
				echo "</ul></div>";
			}
			if ($dir_list != ""){
				echo "<div id='pj-favicon-install-error-message-2' class='error'><p><strong>".__('Favicon Generator is not ready yet.', 'pj-favicon-generator')."</strong> ".__('The following folders must be writable (usually chmod 777 is neccesary):', 'pj-favicon-generator')."</p><ul>";
				echo $dir_list;
				echo "</ul></div>";
			}
		endif;
	}
	
	function admin_print_scripts() {
		wp_enqueue_style( 'pjfav-admin-css', PJFAV_URL.'styles/styles.css' );
	}
	
	function admin_menu() {
		$main = add_menu_page( 'Favicon Generator', 'Favicon Generator', 'edit_themes', PJFAV, array( &$this, 'dashboard' ), PJFAV_URL.'images/menu-icon.png' );
		$sub_1= add_submenu_page( PJFAV, 'Dashboard', 'Dashboard', 'edit_themes', PJFAV, array( &$this, 'dashboard' ) );
		$sub_2= add_submenu_page( PJFAV, 'Manage Icons', 'Manage Icons', 'edit_themes', PJFAV.'-manage-icons', array( &$this, 'manage' ) );
	}
	
	function dashboard() {
		include_once( PJFAV_CORE.'views/dashboard.php' );
	}
	
	function manage() {
		include_once( PJFAV_CORE.'views/manage.php' );
	}
	
	function getAdminOptions() {
		$adminOptions = array(
			'favicon' => '<empty>',
			'donated' => 'no'
		);
		$savedOptions = get_option( PJFAV_ADMIN_OPTIONS );
		if (!empty($savedOptions)) {
			foreach ($savedOptions as $key => $option) {
				$adminOptions[$key] = $option;
			}
		}
		update_option( PJFAV_ADMIN_OPTIONS, $adminOptions);
		return $adminOptions;
	}
	
	function saveAdminOptions(){
		update_option( PJFAV_ADMIN_OPTIONS, $this->adminOptions );
	}	
}
$pjfav_main_controller = new pjfav_main_controller;