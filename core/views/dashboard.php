<div id="pjfav-dashboard" class="wrap">
	<?php screen_icon( 'favicon' ) ?>
	<h2>Favicon Generator: Dashboard</h2>

	<h3>Image Creation Support</h3>
	<table border="0" cellspacing="5" cellpadding="5" class="widefat">
	<thead>
		<tr>
			<th>Required Functionality</th>
			<th>Supported</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Required Functionality</th>
			<th>Supported</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>JPG Support</td>
			<td><?php echo ( $options['jpg'] == true ? __( '<span class="supported">OK</span>', PJFAV ) : __( '<span class="not-supported">Not Supported</span>', PJFAV ) ) ?></td>
		</tr>
		<tr>
			<td>GIF Support</td>
			<td><?php echo ( $options['gif'] == true ? __( '<span class="supported">OK</span>', PJFAV ) : __( '<span class="not-supported">Not Supported</span>', PJFAV ) ) ?></td>
		</tr>
		<tr>
			<td>PNG Support</td>
			<td><?php echo ( $options['png'] == true ? __( '<span class="supported">OK</span>', PJFAV ) : __( '<span class="not-supported">Not Supported</span>', PJFAV ) ) ?></td>
		</tr>
		<tr>
			<td>PNG: Alpha Blending Support</td>
			<td><?php echo ( $options['png-alpha-blending'] == true ? __( '<span class="supported">OK</span>', PJFAV ) : __( '<span class="not-supported">Not Supported</span>', PJFAV ) ) ?></td>
		</tr>
		<tr>
			<td>PNG: Alpha Saving Support</td>
			<td><?php echo ( $options['png-save-alpha'] == true ? __( '<span class="supported">OK</span>', PJFAV ) : __( '<span class="not-supported">Not Supported</span>', PJFAV ) ) ?></td>
		</tr>
	</tbody>
	</table>
	
	<h3>WordPress Functionality Support</h3>
	<table border="0" cellspacing="5" cellpadding="5" class="widefat">
	<thead>
		<tr>
			<th>WordPress Feature</th>
			<th>Type</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>WordPress Feature</th>
			<th>Type</th>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>Filesystem Method</td>
			<td><?php echo ucwords( $wp_filesystem->method ) ?></td>
		</tr>
		<tr>
			<td>Upload Path</td>
			<td><?php echo PJFAV_UPLOAD_PATH ?></td>
		</tr>
	</tbody>
	</table>
	<?php // pring_r( $uploads ) ?>
	
</div>