<?php

/* image function requirements
	JPG - imagecreatefromjpeg
	GIF - imagecreatefromgif
	PNG - imagecreatefrompng imagealphablending imagesavealpha
*/

$defaults = array(
	'favicon'						=> '<empty>',
	'create-from-jpg-support'		=> ( function_exists( 'imagecreatefromjpeg' ) ? 'yes' : 'no' ),
	'create-from-gif-support'		=> ( function_exists( 'imagecreatefromgif' ) ? 'yes' : 'no' ),
	'create-from-png-support'		=> ( function_exists( 'imagecreatefrompng' ) ? 'yes' : 'no' ),
	'png-alpha-blending-support'	=> ( function_exists( 'imagealphablending' ) ? 'yes' : 'no' ),
	'png-save-alpha-support'		=> ( function_exists( 'imagesavealpha' ) ? 'yes' : 'no' ),
);

$saved = get_option( PJFAV_ADMIN_OPTIONS );
$clean = wp_parse_args( $saved, $defaults );

add_option( PJFAV_ADMIN_OPTIONS,  $clean ) or
	update_option( PJFAV_ADMIN_OPTIONS,  $clean );