<?php

class pjfav_public_controller {
	
	function __construct() {
		add_action( 'wp_head', array( &$this, 'wp_head' ) );
	}
	
	function wp_head() {
		//this is a sample function that includes additional styles within the head of your template.
		echo '<link rel="shortcut icon" href="'.PJFAV_URL.'favicon.ico" />';
		// echo '<link rel="apple-touch-icon" href="'.PJFAV_URL.'touchicon.png" />'; // coming soon!
		
		echo '<meta name="generator" content="Think-Press Favicon Generator v1.5" />';
	}
}