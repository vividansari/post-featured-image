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
		$settings_page = add_menu_page(  'GRP Theme Settings', 'GRP Theme Settings', 'edit_theme_options', 'grp-settings', array($this,'grp_settings_page') );
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
		
		if ( $pagenow == 'admin.php' && $_GET['page'] == 'grp-settings' ){ 
			if ( isset ( $_GET['tab'] ) )
		        $tab = $_GET['tab']; 
		    else
		        $tab = 'general'; 

		    switch ( $tab ){ 
		        case 'general' :
					$settings['ilc_tag_class']	  = $_POST['ilc_tag_class'];
					$settings['grp_tag'] = $_POST['grp_tag'];
				break; 
		        case 'image' :
		        	$settings['grp_pixabay_api']  = $_POST['grp_pixabay_api'];
					
				break;
				case 'homepage' : 
					$settings['ilc_intro']	  = $_POST['ilc_intro'];
				break;
		    }
		}
		
		if( !current_user_can( 'unfiltered_html' ) ){
			if ( $settings['ilc_ga']  )
				$settings['ilc_ga'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['ilc_ga'] ) ) );
			if ( $settings['ilc_intro'] )
				$settings['ilc_intro'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['ilc_intro'] ) ) );
		}

		$updated = update_option( "grp_plugin_settings", $settings );
	}

	public function grp_admin_tabs( $current = 'homepage' ) { 
	    $tabs = array( 'general' => 'General', 'image' => 'Image' ); 
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
				<form method="post" action="<?php admin_url( 'admin.php?page=grp-settings' ); ?>">
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