<?php

	// SETUP WP FileSystem API
	if( !function_exists('WP_Filesystem') )
		require_once( ABSPATH . '/wp-admin/includes/file.php' );

	// Connect the filesystem
	WP_Filesystem();
	global $wp_filesystem;
	
	// Delete the favicon upload directory
	$uploads = wp_upload_dir( date( 'Y/m' ) );
	$favicon_uploads_dir = $uploads['basedir'].'/favicons';
	$wp_filesystem->delete( $favicon_uploads_dir );
	
	// Delete the favicon options
	$favicons_options = 'pj_favicon_generator_options';
	delete_option( $favicons_options );