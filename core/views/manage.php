<div id="pjfav-manage" class="wrap">
	<?php screen_icon( 'favicon' ) ?>
	<h2>Favicon Generator</h2>

	<?php
		if( count( $msg ) > 0 ) :
			echo '<div id="pj-favicon-update-error-message" class="'.$msg_type.'"><p>';
			echo implode( '', $msg );
			echo '</p></div>';
		endif;
	?>

	<form method="post" action="<?php echo admin_url( 'admin.php?page='.PJFAV.'-manage-icons' ) ?>" enctype="multipart/form-data">
		<?php wp_nonce_field( PJFAV.'_upload' ); ?>
		<input type="hidden" name="action" value="wp_handle_upload" />

		<h3><?php _e( 'Upload a New Image', PJFAV ); ?></h3>
		<p><?php _e( 'Acceptable file types are JPG, JPEG, GIF and PNG. Note that for this to work, you\'re going to need to have PHP configured with the GD2 library.', PJFAV ); ?></p>
	
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e( 'Favicon Source', PJFAV ); ?></th>
			<td>
				<input type="file" name="favicon" id="favicon" />
				<input type="submit" class="button" name="html-upload" value="Upload" />
			</td>
		</tr>
		</table>

		<h3><?php _e( 'Select a Previously Uploaded File', PJFAV); ?></h3>
		<p><?php _e( 'Since this plugin stores every image you upload, you can upload as many images as you like. You can then come back from time to time and change your favicon. Select from the choices below.', PJFAV ); ?></p>
		<p><em><strong><?php _e( 'Note:', PJFAV ) ?></strong> <?php _e( 'Some browsers hang on to old favicon images in their cache. This is an unfortunate side effect of caching. If you make a change to your favicon and don\'t immediately see the change, don\'t start banging your head against the wall. This is not an indication that this plugin is not working. Try <a href="http://en.wikipedia.org/wiki/Bypass_your_cache" target="_blank">emptying your cache</a> and quitting the browser.', PJFAV ); ?></em></p>
		<?php

			$files = $wp_filesystem->dirlist( PJFAV_UPLOAD_PATH );
			foreach( $files as $filename => $file_details ) :
				if( substr( $filename, 0, 1 ) != '.' && substr( $filename, -7, 3 ) != '-16' ) :
					$active = ( $filename == basename( $options['favicon_src'] ) ) ? true : false;
					echo '<div style="float: left; margin-top: 20px; padding: 10px; text-align: center;'.( ( $active ) ? ' background-color: #dddddd' : '' ).'">';
					echo '	<div class="choice-block" style="position: relative; width: 36px; height: 36px; border: 1px solid '.( ( $active ) ? '#ff6666' : '#cccccc' ).';">';
					echo '		<img src="'.PJFAV_UPLOAD_URL.'/'.$filename.'" title="'.$filename.'" alt="'.$filename.'" class="favicon-choices" style="position: absolute; top: 10px; left: 10px; width: 16px; height: 16px;" />';
					echo '	</div>';
					echo '	<div>';
					echo ( $active ) ? __( 'Active', PJFAV ).'<br />' : '<a href="'.wp_nonce_url( admin_url( 'admin.php?page='.PJFAV.'-manage-icons&d='.$filename ), PJFAV.'_delete' ).'">'.__( 'Delete', PJFAV ).'</a><br />';
					echo ( $active ) ? __( 'Icon', PJFAV ) : '<a href="'.wp_nonce_url( admin_url( 'admin.php?page='.PJFAV.'-manage-icons&u='.$filename ), PJFAV.'_select' ).'">'.__( 'Use', PJFAV ).'</a>';
					echo '	</div>';
					echo '</div>';
				endif;
			endforeach;
			echo '<div class="clear"></div>'
		?>
	</form>
</div>