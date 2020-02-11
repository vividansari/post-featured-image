<?php
/**
 * adding the plugin setting
 */
class GrpPluginSettingTab
{

	function __construct(){
		add_action( 'admin_menu', array($this , 'grp_settings_page_init' ) );

	}

	public function grp_settings_page_init(){
		// add custom menu page
		$settings_page = add_menu_page(  'GRP Settings', 'GRP Settings', 'edit_theme_options', 'grp-settings', array($this,'grp_settings_page') );
		//add this action to save your setting page data
		add_action( "load-{$settings_page}", array( $this, 'grp_load_settings_page' ) );
	}

	public function grp_load_settings_page(){
		if ( $_POST["grp-settings-submit"] == 'Y' ) {
			check_admin_referer( "grp-settings-page" );
			$this->grp_save_plugin_settings();
			$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
			wp_redirect(admin_url('admin.php?page=grp-settings&'.$url_parameters));
			exit;
		}
	}

	public function grp_save_plugin_settings() {
		global $pagenow;
		$settings = get_option( "grp_plugin_settings" );



		#for cron reset parameters if they change old data
		if(isset( $_POST['grp_tag'] ) && ( $settings['grp_tag'] == $_POST['grp_tag'] ) ){
			#check its change the page number
			if( isset( $_POST['grp_tag_total_page'] ) && ( $settings['grp_tag_total_page'] == $_POST['grp_tag_total_page'] ) ){

			}else if( isset( $_POST['grp_tag_total_page'] ) ){

				if( $setting['grp_tag_total_page'] < $_POST['grp_tag_total_page'] ){
					update_option( 'grp_cron_completed', 'no' );
				}

			}
		}else if( isset( $_POST['grp_tag'] ) ){
			#reset all parameters
			update_option( 'grp_cron_current_page', 1 );
			update_option( 'grp_cron_completed', 'no' );
		}else{
			#reset all parameters
			update_option( 'grp_cron_current_page', 1 );
			update_option( 'grp_cron_completed', 'no' );
		}

		if ( $pagenow == 'admin.php' && $_GET['page'] == 'grp-settings' ){
			if ( isset ( $_GET['tab'] ) )
		        $tab = $_GET['tab'];
		    else
		        $tab = 'general';

		    switch ( $tab ){
		        case 'general' :
					//$settings['ilc_tag_class']	  = $_POST['ilc_tag_class'];
					$settings['grp_tag'] = $_POST['grp_tag'];
					$settings['grp_tag_total_page'] = $_POST['grp_tag_total_page'];
				break;
		        case 'image' :
		        	$settings['grp_pixabay_api']  = $_POST['grp_pixabay_api'];
		        	$settings['grp_copyright_text']  = $_POST['grp_copyright_text'];
		        	$settings['grp_pixabay_q_tag']  = $_POST['grp_pixabay_q_tag'];

		        	$settings['grp_pixabay_cat']  = $_POST['grp_pixabay_cat'];

				break;
				case 'text' :
					$settings['grp_text_color'] = $_POST['grp_text_color'];
					$settings['grp_text_shadow_color'] = $_POST['grp_text_shadow_color'];
					$settings['grp_copy_right_text_color'] = $_POST['grp_copy_right_text_color'];
					$settings['grp_text_size'] = $_POST['grp_text_size'];
					$settings['grp_copyright_text_size'] = $_POST['grp_copyright_text_size'];

					$settings['grp_copy_right_text_color'] = $_POST['grp_copy_right_text_color'];
					$settings['grp_copy_right_text_color'] = $_POST['grp_copy_right_text_color'];

					$settings['grp_x_position'] = $_POST['grp_x_position'];
					$settings['grp_y_position'] = $_POST['grp_y_position'];
					$settings['grp_text_transform'] = $_POST['grp_text_transform'];

					$settings['grp_padding_top'] = $_POST['grp_padding_top'];
					$settings['grp_padding_bottom'] = $_POST['grp_padding_bottom'];
					$settings['grp_padding_left'] = $_POST['grp_padding_left'];
					$settings['grp_padding_right'] = $_POST['grp_padding_right'];


				break;
				case 'homepage' :
					$settings['ilc_intro']	  = $_POST['ilc_intro'];
				break;
		    }
		}



		$updated = update_option( "grp_plugin_settings", $settings );
	}

	public function grp_admin_tabs( $current = 'homepage' ) {
	    $tabs = array( 'general' => 'General', 'image' => 'Image' ,'text' => 'Text' , 'cron_details' => 'Cron Details' ,'upload_tag_csv' => 'Upload Tag csv', 'upload_author_csv' => 'Upload Author csv' );
	    $links = array();
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=grp-settings&tab=$tab'>$name</a>";

	    }
	    echo '</h2>';
	}

	public function grp_settings_page(){
		wp_enqueue_script('grp_admin_js');
		global $pagenow;
		$settings = get_option( "grp_plugin_settings" );

		?>

		<div class="wrap">
			<h2>Good Reads Plugin Settings</h2>

			<?php
				if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p> Settings updated.</p></div>';

				if ( isset ( $_GET['tab'] ) ) $this->grp_admin_tabs($_GET['tab']); else $this->grp_admin_tabs('general');
			?>

			<div id="poststuff">
				<form method="post" action="<?php admin_url( 'admin.php?page=grp-settings' ); ?>" accept-charset="utf-8" enctype="multipart/form-data" id="grp_form_id" name="grp_form">
					<?php
					wp_nonce_field( "grp-settings-page" );

					if ( $pagenow == 'admin.php' && $_GET['page'] == 'grp-settings' ){

						if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
						else $tab = 'general';

						echo '<table class="form-table">';
						switch ( $tab ){
							case 'general' :
								include 'tabs/general.php';
							break;
							case 'image' :
								include 'tabs/image.php';
							break;
							case 'text' :
								include 'tabs/text.php';
							break;

							case 'cron_details':
								include 'tabs/cron_details.php';
							break;
							case 'upload_tag_csv':
								include 'tabs/upload.php';
							break;
							case 'upload_author_csv':
								include 'tabs/upload_author.php';
							break;

						}
						echo '</table>';
					}
					?>
					<p class="submit" style="clear: both;">
						<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
						<input type="hidden" name="grp-settings-submit" value="Y" />
					</p>
				</form>

			</div>

		</div>
	<?php
	}
}
$grp_setting = new GrpPluginSettingTab();

?>