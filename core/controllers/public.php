<?php

class pjfav_public_controller {
	
	function __construct() {
		add_action( 'wp_head', array( &$this, 'wp_head' ), 1 );
	}
	
	function wp_head() {
		// Add the shortcut icon to the head
		echo '<link rel="shortcut icon" href="'.PJFAV_URL.'favicon.ico" />';
		// echo '<link rel="apple-touch-icon" href="'.PJFAV_URL.'touchicon.png" />'; // coming soon!
		
		echo '<meta name="generator" content="Think-Press Favicon Generator v1.5" />';
	}
}
$pjfav_public_controller = new pjfav_public_controller;