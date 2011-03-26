<?php
/*
Plugin Name: Favicon Generator
Plugin URI: http://pixeljar.net/plugins/favicon-generator
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3441397
Description: This plugin will allow you to upload an image file of your choosing to be converted to a favicon for your WordPress site.
Author: Brandon Dove, Jeffrey Zinn
Version: 2.0
Author URI: http://www.pixeljar.net


Copyright 2009  Pixel jar  (email : info@pixeljar.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// SET UP PATH CONSTANTS
define( 'PJFAV',				'pjfavgen' );
define( 'PJFAV_URL',			plugin_dir_url( __FILE__ ) );
define( 'PJFAV_ABS',			plugin_dir_path( __FILE__ ) );
define( 'PJFAV_REL',			basename( dirname( __FILE__ ) ) );
define( 'PJFAV_CORE',			PJFAV_ABS.'core/' );
define( 'PJFAV_EXT',			PJFAV_ABS.'extensions/' );
define( 'PJFAV_LANG',			PJFAV_ABS.'i18n/' );
define( 'PJFAV_CSS',			PJFAV_URL.'css/' );
define( 'PJFAV_JS',				PJFAV_URL.'js/' );
define( 'PJFAV_ADMIN_OPTIONS',	'pj_favicon_generator_options' );

// INTERNATIONALIZATION
load_plugin_textdomain( PJFAV, null, PJFAV_REL );

if ( is_admin() && current_user_can( 'edit_themes' ) ) :
	// ADMIN CONTROLLER
	require_once( PJFAV_CORE.'/controllers/admin.php' );
else :
	// PUBLIC CONTROLLER
	require_once( PJFAV_CORE.'/controllers/public.php' );
endif;

/* DEBUG HELPERS
************************************/
if( ! function_exists( 'pring_r' ) ! function_exists( '_pring_r' ) ) :

	function pring_r( $arr ) {
		echo _pring_r( $arr );
	}
		function _pring_r( $arr ) {
			return '<pre>'.print_r( $arr, true ).'</pre>';
		}
endif;