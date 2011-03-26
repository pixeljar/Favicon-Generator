<?php
/*
Plugin Name: Favicon Generator
Plugin URI: http://www.think-press.com/plugins/favicon-generator
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3441397
Description: This plugin will allow you to upload an image file of your choosing to be converted to a favicon for your WordPress site.
Author: Brandon Dove, Jeffrey Zinn
Version: 1.5
Author URI: http://www.think-press.com


Copyright 2009  Pixel jar  (email : info@pixeljar.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'PJFAV_URL' ) )
	define( 'PJFAV_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'PJFAV_DIR' ) )
	define( 'PJFAV_DIR', plugin_dir_path( __FILE__ ) );

load_plugin_textdomain( 'pj-favicon-generator', PJFAV_DIR );

function pj_favicon_admin_scripts() {
	if ( isset($_GET['page']) && $_GET['page'] == "Favicon-Generator" )
		wp_enqueue_style( 'pjfav-admin-css', PJFAV_URL.'styles/styles.css' );
}
add_action( 'admin_init', 'pj_favicon_admin_scripts', 1 );
register_activation_hook( __FILE__, array( &$pj_favicon_generator, 'activate' ) );
add_action( 'admin_notices', array(&$pj_favicon_generator, 'check_installation') );


if (!class_exists('pj_favicon_generator')) {
	
	include ('php/Directory.php');
	include ('php/Ico2_1.php');
	include ('php/Image.php');
	
    class pj_favicon_generator	{
		
		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = "pj_favicon_generator_options";
		
		/**
		* @var array   The options that are saved in the database.
		*/
		var $adminOptions = array();
		
		/**
		* PHP 4 Compatible Constructor
		*/
		function pj_favicon_generator(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
			$this->adminOptions = $this->getAdminOptions();
			add_action("admin_menu", array(&$this,"add_admin_pages"));
			add_action('wp_head', array(&$this,'wp_head_intercept'));
		}
		
		/**
		* Retrieves the options from the database.
		* @return array
		*/
		function getAdminOptions() {
			$adminOptions = array(
				"favicon" => "<empty>",
				"donated" => "no"
			);
			$savedOptions = get_option($this->adminOptionsName);
			if (!empty($savedOptions)) {
				foreach ($savedOptions as $key => $option) {
					$adminOptions[$key] = $option;
				}
			}
			update_option($this->adminOptionsName, $adminOptions);
			return $adminOptions;
		}
		
		function saveAdminOptions(){
			update_option($this->adminOptionsName, $this->adminOptions);
		}
		
		/**
		* Runs on plugin activation
		*/
		function activate () {
			$uploaddir = PJFAV_DIR.'uploads/';
			wp_mkdir_p( $uploaddir );
		}
		/**
		* Checks to make sure that all
		* required directories are set up properly
		*/
		function check_installation() {
			if ( (stripos($_SERVER['REQUEST_URI'],'plugins.php') !== false) || (!empty($_GET['page']) && $_GET['page'] == "Favicon-Generator" ) ) :
				$dir_list = "";
				$dir_list2 = "";
	
				$uploaddir = PJFAV_DIR.'uploads/';
				wp_mkdir_p( $uploaddir );
	
				if (!is_dir(PJFAV_DIR)){
					$dir_list2.= "<li>".PJFAV_DIR . "</li>";
				} elseif (!is_writable(PJFAV_DIR)){
					$dir_list.= "<li>".PJFAV_DIR . "</li>";
				}
	
				if (!is_dir($uploaddir)){
					$dir_list2.= "<li>".$uploaddir . "</li>";
				} elseif (!is_writable($uploaddir)){
					$dir_list.= "<li>".$uploaddir . "</li>";
				}
		
				if ($dir_list2 != ""){
					echo "<div id='pj-favicon-install-error-message' class='error'><p><strong>".__('Favicon Generator is not ready yet.', 'pj-favicon-generator')."</strong> ".__('must create the following folders (and must chmod 777):', 'pj-favicon-generator')."</p><ul>";
					echo $dir_list2;
					echo "</ul></div>";
				}
				if ($dir_list != ""){
					echo "<div id='pj-favicon-install-error-message-2' class='error'><p><strong>".__('Favicon Generator is not ready yet.', 'pj-favicon-generator')."</strong> ".__('The following folders must be writable (usually chmod 777 is neccesary):', 'pj-favicon-generator')."</p><ul>";
					echo $dir_list;
					echo "</ul></div>";
				}
			endif;
		}
		
		/**
		* Creates the admin page.
		*/
		function add_admin_pages(){
			add_menu_page("Favicon Generator", "Favicon Generator", 10, "Favicon-Generator", array(&$this,"output_sub_admin_page_0"), PJFAV_URL.'/menu-icon.png');
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function output_sub_admin_page_0 () {

			// PATHS
			$uploaddir = PJFAV_DIR.'uploads/';
			$uploadurl = PJFAV_URL.'uploads/';
			$submiturl = preg_replace('/&[du]=[a-z0-9.%()_-]*\.(jpg|jpeg|gif|png)/is', '', $_SERVER['REQUEST_URI']);
			
			$msg = "";

			// USER UPLOADED A NEW IMAGE
			if (!empty($_FILES)) {
				$userfile = preg_replace('/\\\\\'/', '', $_FILES['favicon']['name']);
				$file_size = $_FILES['favicon']['size'];
				$file_temp = $_FILES['favicon']['tmp_name'];
				$file_err = $_FILES['favicon']['error'];
				$file_name = explode('.', $userfile);
				$file_type = strtolower($file_name[count($file_name) - 1]);
				$uploadedfile = $uploaddir.$userfile;
				
				if(!empty($userfile)) {
					$file_type = strtolower($file_type);
					$files = array('jpeg', 'jpg', 'gif', 'png');
					$key = array_search($file_type, $files);
				
					if(!$key) {
						$msg .= __("ILLEGAL FILE TYPE. Only JPEG, JPG, GIF or PNG files are allowed.", 'pj-favicon-generator')."<br />";
					}
				
					// ERROR CHECKING
					$error_count = count($file_error);
					if($error_count > 0) {
						for($i = 0; $i <= $error_count; ++$i) {
							$msg .= $_FILES['favicon']['error'][$i]."<br />";
						}
					} else {
						if (is_file(PJFAV_DIR.'favicon.ico')) {
							if (!unlink(PJFAV_DIR.'favicon.ico')) {
								$msg .= __("There was an error deleting the old favicon.", 'pj-favicon-generator')."<br />";
							}
						}
					
						if(!move_uploaded_file($file_temp, $uploadedfile)) {
							$msg .= __("There was an error when uploading your file.", 'pj-favicon-generator')."<br />";
						}
						if (!chmod($uploadedfile, 0777)) {
							$msg .= __("There was an error when changing your favicon's permissions.", 'pj-favicon-generator')."<br />";
						}
					
						$img =new Image($uploadedfile);
						$img->resizeImage(16,16);
						$img->saveImage($uploadedfile);
					
						switch ($file_type) {
							case "jpeg":
							case "jpg":
								$im = imagecreatefromjpeg($uploadedfile);
								break;
							case "gif":
								$im = imagecreatefromgif($uploadedfile);
								break;
							case "png":
								$im = imagecreatefrompng($uploadedfile);
								imagealphablending($im, true); // setting alpha blending on
								imagesavealpha($im, true); // save alphablending setting (important)
								break;
						}
						// ImageICO function provided by JPEXS.com <http://www.jpexs.com/php.html>
						ImageIco($im, PJFAV_DIR.'favicon.ico');
						$this->adminOptions['favicon'] = $userfile;
						$this->saveAdminOptions();
						$msg .= __("Your favicon has been updated.", 'pj-favicon-generator');
					}

				}
			}
			
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
				ImageIco($im, PJFAV_DIR.'favicon.ico');
				$this->adminOptions['favicon'] = $_GET['u'];
				$this->saveAdminOptions();
				$msg .= __("Your favicon has been updated.", 'pj-favicon-generator');
			}
			?>
<div class="wrap favicon-generator">
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
	
	<form method="post" action="<?php echo $submiturl; ?>" enctype="multipart/form-data">
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
			<?php
		} 
		
		
		/**
		* Called by the action wp_head
		*/
		function wp_head_intercept() {
			//this is a sample function that includes additional styles within the head of your template.
			echo '<link rel="shortcut icon" href="'.PJFAV_URL.'favicon.ico" />';
			// echo '<link rel="apple-touch-icon" href="'.PJFAV_URL.'touchicon.png" />'; // coming soon!
			echo '<meta name="generator" content="Think-Press Favicon Generator v1.5" />';
		}
		
    }
}

//instantiate the class
if (class_exists('pj_favicon_generator')) {
	$pj_favicon_generator = new pj_favicon_generator();
}