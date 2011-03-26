<?php
	
	$msg = array();

	// USER UPLOADED A NEW IMAGE
	if( isset( $_FILES['favicon'] ) && isset( $_POST['_wpnonce'] ) ) :

		if( !current_user_can( 'edit_themes' ) )
			wp_die( __( "You don't have permissions to do that.", PJFAV ) );
		
		if( !wp_verify_nonce( $_POST['_wpnonce'], PJFAV.'_upload' ) )
			wp_die( __( "You don't have permissions to do that.", PJFAV ) );
			
		// SETUP WP FileSystem API
		if( !function_exists('WP_Filesystem') ) :
			require_once( ABSPATH . '/wp-admin/includes/file.php' );

			// Connect the filesystem
			WP_Filesystem();

			global $wp_filesystem;
		endif;

		$upload_folder = wp_upload_dir( date( 'Y/m' ) );
	
		$favicon		= $_FILES['favicon'];
		$favicon_name	= sanitize_file_name( $_FILES['favicon']['name'] );
		$favicon_size	= $_FILES['favicon']['size'];
		$favicon_temp	= $_FILES['favicon']['tmp_name'];
		$favicon_err	= $_FILES['favicon']['error'];

	
		// ERROR CHECKING
		$error_count = count( $file_error );
		if( $error_count > 0 ) :
			for( $i = 0; $i <= $error_count; ++$i ) :
				$msg[] = $favicon_err[$i]."<br />";
			endfor;
		else :
		
			if( $wp_filesystem->is_file( $upload_folder.'favicon.ico' ) ) :
				if( !$wp_filesystem->unlink( $upload_folder.'favicon.ico' ) ) :
					$msg[] = __( 'There was an error deleting the old favicon.', PJFAV );
				endif;
			endif;

			if( $uploaded_file != wp_handle_upload( $favicon, array( 'mimes' => array( 'image/jpeg', 'image/gif', 'image/png' ) ) ) ) :
				$msg[] = __( 'There was an error when uploading your file.', PJFAV );
			endif;
			
			require_once( PJFAV_CORE.'models/image.php' );
			$img =new Image( $uploaded_file );
			$img->resizeImage( 16, 16 );
			$img->saveImage( $uploaded_file );
		
			switch( $file_type ) {
				case "jpeg":
				case "jpg":
					$im = imagecreatefromjpeg( $uploaded_file );
					break;
				case "gif":
					$im = imagecreatefromgif( $uploaded_file );
					break;
				case "png":
					$im = imagecreatefrompng( $uploaded_file );
					imagealphablending( $im, true ); // setting alpha blending on
					imagesavealpha( $im, true ); // save alphablending setting (important)
					break;
			}
			
			// ImageICO function provided by JPEXS.com <http://www.jpexs.com/php.html>
			ImageIco( $im, PJFAV_ABS.'favicon.ico' );

			$options = get_option( PJFAV_ADMIN_OPTIONS );
			
			$options['favicon'] = $userfile;
			update_option( PJFAV_ADMIN_OPTIONS, $options );
			$msg[] = __( 'Your favicon has been updated.', PJFAV );
		endif;

	endif; // isset( $_FILES['favicon'] ) && isset( $_POST['_wpnonce'] )
	
	if (!empty($_POST['donated'])) :
	
		$this->adminOptions['donated'] = $_POST['donated'];
		$this->saveAdminOptions();
	
	endif;

	// USER HAS CHOSEN TO DELETE AN UPLOADED IMAGE
	if (!empty($_GET['d']) && is_file($uploaddir.$_GET['d'])) {
		if (!unlink ($uploaddir.$_GET['d'])) {
			$msg .= __("There was a problem deleting the selected image.", 'pj-favicon-generator');
		} else {
			$msg .= __("The selected image has been deleted.", 'pj-favicon-generator');
		}
	}
	
	// USER HAS CHOSEN TO CHANGE HIS FAVICON TO A PREVIOUSLY UPLOADED IMAGE
	if (!empty($_GET['u'])) {
		$file_name = explode('.', $_GET['u']);
		$file_type = $file_name[count($file_name) - 1];
		switch ($file_type) {
			case "jpeg":
			case "jpg":
				$im = imagecreatefromjpeg($uploaddir.$_GET['u']);
				break;
			case "gif":
				$im = imagecreatefromgif($uploaddir.$_GET['u']);
				break;
			case "png":
				$im = imagecreatefrompng($uploaddir.$_GET['u']);
				imagealphablending($im, true); // setting alpha blending on
				imagesavealpha($im, true); // save alphablending setting (important)
				break;
		}
		
		// ImageICO function provided by JPEXS.com <http://www.jpexs.com/php.html>
		ImageIco($im, PJFAV_ABS.'favicon.ico');
		$this->adminOptions['favicon'] = $_GET['u'];
		$this->saveAdminOptions();
		$msg .= __("Your favicon has been updated.", 'pj-favicon-generator');
	}
	?>
<div class="wrap pjfav-manage">
<div id="favicon-options" class="icon32" style="background: transparent url('<?php echo PJFAV_URL; ?>large-menu-icon.png') no-repeat;"><br /></div>
<h2>Favicon Generator</h2>

<?php
$ads = '<script type="text/javascript">';
$ads.= 'var psHost = (("https:" == document.location.protocol) ? "https://" : "http://");';
$ads.= 'document.write(unescape("%3Cscript src=\'" + psHost + "pluginsponsors.com/direct/spsn/display.php?client=pj-favicon-generator&spot=\' type=\'text/javascript\'%3E%3C/script%3E"));';
$ads.= '</script>';
if ($this->adminOptions['donated'] == 'no')
	echo $ads;
?>

<form method="post" enctype="multipart/form-data">
<?php wp_nonce_field('update-options'); ?>


<h3><?php _e('Upload a New Image', 'pj-favicon-generator'); ?></h3>
<p><?php _e('Acceptable file types are JPG, JPEG, GIF and PNG. Note that for this to work, you\'re going to need to have PHP configured with the GD2 library.', 'pj-favicon-generator'); ?></p>
	
<table class="form-table">
<?php if ($this->adminOptions['donated'] == 'no') : ?>
<tr valign="top">
	<th scope="row"><?php _e('Donations', 'pj-favicon-generator'); ?></th>
	<td>
		<input type="radio" name="donated" value="no"<?php echo ($this->adminOptions['donated'] == 'no') ? ' checked="checked"' : '' ?> />  <?php _e('I haven\'t donated to this plugin yet. <small>(<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3441397" target="_blank">What are you waiting for?</a>)</small>', 'pj-favicon-generator'); ?><br />
		<input type="radio" name="donated" value="yes" /> <?php _e('I\'ve donated to this plugin because I love it. Turn off the ads.', 'pj-favicon-generator'); ?><br />
		<input type="submit" class="button-primary" name="save-donations" value="Save Changes" />
	</td>
</tr>
<?php endif; ?>
<tr valign="top">
	<th scope="row"><?php _e('Favicon Source', 'pj-favicon-generator'); ?></th>
	<td>
		<input type="file" name="favicon" id="favicon" />
		<input type="submit" class="button" name="html-upload" value="Upload" />
	</td>
</tr>
</table>

<h3><?php _e('Select a Previously Uploaded File', 'pj-favicon-generator'); ?></h3>
<p><?php _e('Since this plugin stores every image you upload, you can upload as many images as you like. You can then come back from time to time and change your favicon. Select from the choices below.', 'pj-favicon-generator'); ?></p>
<p><em><strong><?php _e('Note:', 'pj-favicon-generator') ?></strong> <?php _e('Some browsers hang on to old favicon images in their cache. This is an unfortunate side effect of caching. If you make a change to your favicon and don\'t immediately see the change, don\'t start banging your head against the wall. This is not an indication that this plugin is not working. Try <a href="http://en.wikipedia.org/wiki/Bypass_your_cache" target="_blank">emptying your cache</a> and quitting the browser.', 'pj-favicon-generator'); ?></em></p>
<?php
	$wp_filesystem = new WP_filsystem
	$files = dirList($uploaddir);
	for ($i = 0; $i < count($files); $i++) :
		$active = ($files[$i] == $this->adminOptions['favicon']) ? true : false;
		echo '<div style="float: left; margin-top: 20px; padding: 10px; text-align: center;'.(($active) ? ' background-color: #dddddd' : '').'">';
		echo '	<div class="choice-block" style="position: relative; width: 36px; height: 36px; border: 1px solid '.(($active) ? '#ff6666' : '#cccccc').';">';
		echo '		<img src="'.$uploadurl.$files[$i].'" title="'.$files[$i].'" alt="'.$files[$i].'" class="favicon-choices" style="position: absolute; top: 10px; left: 10px; width: 16px; height: 16px;" />';
		echo '	</div>';
		echo '	<div>';
		echo ($active) ? __('Active', 'pj-favicon-generator').'<br />' : '		<a href="'.$submiturl.'&d='.$files[$i].'">'.__('Delete', 'pj-favicon-generator').'</a><br />';
		echo ($active) ? __('Icon', 'pj-favicon-generator') : '		<a href="'.$submiturl.'&u='.$files[$i].'">'.__('Use', 'pj-favicon-generator').'</a>';
		echo '	</div>';
		echo '</div>';
		
	endfor;
	echo '<div class="clear"></div>'
?>
</form>
</div>