<?php
/**
 * GRP cron for get all post
 */
class grpCron
{
	
	function __construct(){
		#add action for adding the schedule event
		add_action('admin_init', array( $this ,'generate_cron_schedule') );	

		add_filter( 'cron_schedules', array( $this, 'grp_cron_interval' ) );

		   #Cron Function
    	add_action('grp_get_data_cron', array( $this, 'grp_get_data_process' ) );
	}

	public function generate_cron_schedule(){
		 if ( !wp_next_scheduled( 'grp_get_data_cron' ) ) {
	        wp_schedule_event( current_time( 'timestamp' ), 'every_five_minutes', 'grp_get_data_cron');
	    }
	}

	public function grp_cron_interval( $schedules ){
		$schedules['every_five_minutes'] = array(
	     	'interval'  => 300,
	     	'display'   => __( 'Every 5 Minutes', 'grp' )
	    );
	   
	    return $schedules;
	}

	public function grp_get_data_process(){

		
		$settings = get_option( "grp_plugin_settings" );
		
		$tag = $settings['grp_tag'];
		
		if( !empty( $tag ) ){
			

			$total_page = $settings['grp_tag_total_page'];

			$total_page = !empty( $total_page ) ? $total_page : 1 ;
			
			$current_page = get_option('grp_cron_current_page');
			
			$current_page = !empty( $current_page ) ? $current_page : 1 ;
			
			$cron_done = get_option('grp_cron_completed');
			
			if($cron_done != 'yes'){
				
				$grp_post = new grpPost();
				
				$grp_post->grp_get_data( $tag, $current_page );

				#set currentpage parameters
				$current_page++;

				update_option( 'grp_cron_current_page', $current_page );

				if($total_page < $current_page){
					update_option( 'grp_cron_completed', 'yes' );	
					$done_tag = get_option('grp_done_tag');
					if( empty( $done_tag ) ){
						$done_tag = array();
						$done_tag[$tag] = 'total page = '.$total_page.'- done';	
					}else{
						$done_tag[$tag] = 'total page = '.$total_page.'- done';	
					}
					update_option('grp_done_tag',$done_tag);
					
				}
			}
		}
		
	}

}
$grpcron = new grpCron();
?>