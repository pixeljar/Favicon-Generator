<?php

/* image function requirements
	JPG - imagecreatefromjpeg
	GIF - imagecreatefromgif
	PNG - imagecreatefrompng imagealphablending imagesavealpha
*/

$defaults = array(
	'favicon'						=> '<empty>',
	'create-from-jpg-support'		=> ( function_exists( 'imagecreatefromjpeg' ) ? true : false ),
	'create-from-gif-support'		=> ( function_exists( 'imagecreatefromgif' ) ? true : false ),
	'create-from-png-support'		=> ( function_exists( 'imagecreatefrompng' ) ? true : false ),
	'png-alpha-blending-support'	=> ( function_exists( 'imagealphablending' ) ? true : false ),
	'png-save-alpha-support'		=> ( function_exists( 'imagesavealpha' ) ? true : false ),
);

$saved = get_option( PJFAV_ADMIN_OPTIONS );
$clean = wp_parse_args( $saved, $defaults );

add_option( PJFAV_ADMIN_OPTIONS,  $clean ) or
	update_option( PJFAV_ADMIN_OPTIONS,  $clean );