<?php

/* image function requirements
	JPG - imagecreatefromjpeg
	GIF - imagecreatefromgif
	PNG - imagecreatefrompng imagealphablending imagesavealpha
*/

$defaults = array(
	'favicon'				=> '<empty>',
	'favicon_src'			=> '<empty>',
	'favicon_type'			=> '<empty>',
	'jpg'					=> ( function_exists( 'imagecreatefromjpeg' ) ? true : false ),
	'gif'					=> ( function_exists( 'imagecreatefromgif' ) ? true : false ),
	'png'					=> ( function_exists( 'imagecreatefrompng' ) ? true : false ),
	'png-alpha-blending'	=> ( function_exists( 'imagealphablending' ) ? true : false ),
	'png-save-alpha'		=> ( function_exists( 'imagesavealpha' ) ? true : false ),
);

$saved = get_option( PJFAV_ADMIN_OPTIONS );
$clean = wp_parse_args( $saved, $defaults );

add_option( PJFAV_ADMIN_OPTIONS,  $clean ) or
	update_option( PJFAV_ADMIN_OPTIONS,  $clean );