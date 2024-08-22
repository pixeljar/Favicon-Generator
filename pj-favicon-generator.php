<?php
/**
 * Plugin Name: Favicon Generator (CLOSED)
 * Plugin URI: http://www.pixeljar.com
 * Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3441397
 * Description: This plugin's functionality has been included in WordPress core for many years. It is no longer necessary to use this plugin. As such, this plugin can no longer be activated.
 * Author: Pixel Jar
 * Version: 2.1
 * Author URI: http://www.pixeljar.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pjfavgen
 * Domain Path: /lang
 * Requires at least: 4.0
 * Tested up to: 6.6.1
 *
 * Copyright 2024  Pixel jar  (email : info@pixeljar.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package WordPress
 * @subpackage PJ_Favicon_Generator
 */

/**
 * Deactivate the plugin as the functionality is now in WordPress core.
 */
function pj_favicon_generator_deactivate() {

	$plugin = plugin_basename( __FILE__ );
	if ( is_plugin_active( $plugin ) ) {

		delete_option( 'pj_favicon_generator_options' );
		deactivate_plugins( $plugin, true );

	}

}
add_action( 'admin_init', 'pj_favicon_generator_deactivate' );
