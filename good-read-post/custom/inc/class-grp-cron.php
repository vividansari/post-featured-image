<?php
/**
 * GRP cron for get all post
 */
class grpCron
{

	function __construct(){
		#add action for adding the schedule event
		// add_action('admin_init', array( $this ,'generate_cron_schedule') );

		add_filter( 'cron_schedules', array( $this, 'grp_cron_interval' ) );

		   #Cron Function
    	add_action('grp_get_data_cron', array( $this, 'grp_get_data_process' ) );
	}

	public function generate_cron_schedule(){
		 if ( !wp_next_scheduled( 'grp_get_data_cron' ) ) {
	        wp_schedule_event( current_time( 'timestamp' ), 'every_five_minutes', 'grp_get_data_cron');
	        //wp_schedule_event( current_time( 'timestamp' ), 'every_five_minutes', 'grp_get_author_cron');
	    }
	}

	public function grp_cron_interval( $schedules ){
		// $schedules['every_five_minutes'] = array(
	 //     	'interval'  => 120,
	 //     	'display'   => __( 'Every 2 Minutes', 'grp' )
	 //    );
	    // $schedules['every_two_minutes'] = array(
	    //  	'interval'  => 120,
	    //  	'display'   => __( 'Every 2 Minutes', 'grp' )
	    // );
	    $schedules['every_five_minutes'] = array(
	     	'interval'  => 300,
	     	'display'   => __( 'Every 5 Minutes', 'grp' )
	    );
	    $schedules['every_two_minutes'] = array(
	     	'interval'  => 120,
	     	'display'   => __( 'Every 2 Minutes', 'grp' )
	    );

	    return $schedules;
	}

	public function grp_get_data_process(){
		$settings = get_option( "grp_plugin_settings" );
		$new_tag_arr = get_option('grp_tag_csv_key');
		if(!empty( $new_tag_arr ) ){
			
			$tag_key = get_option( 'grp_cron_tag_key');
			$tag_key = !empty( $tag_key ) ? $tag_key : 0;
			
			if ($tag_key == 0)
			{
				$current_tag = add_option('grp_cron_tag_current_value', $new_tag_arr[0]);
			}
			
			$tag = $new_tag_arr[$tag_key];
			$last_tag_key = count($new_tag_arr);
			

			if( !empty( $tag ) ){
			
				$tag = str_replace(' ', '-', $tag);

				$total_page = isset($settings['grp_tag_total_page']) ? $settings['grp_tag_total_page'] : 1 ;
				$total_page = !empty( $total_page ) ? $total_page : 1 ;

				$current_page = get_option('grp_cron_current_page');

				$current_page = !empty( $current_page ) ? $current_page : 1 ;

				$cron_done = get_option('grp_cron_completed');

				$cron_through_tag_or_author = get_option('grp_cron_through_tag_or_author');
				$cron_through_tag_or_author = !empty($cron_through_tag_or_author) ? $cron_through_tag_or_author : 1; //1 = only tag And 2 = author

				if($cron_done != 'yes'){

					$author_key = get_option('grp_cron_author_key');
					
					$author_key = !empty( $author_key ) ? $author_key : 0;
					
					#get author
					$author_arr = get_option('grp__author_names');
					
					$last_author_key = count($author_arr);
					
					$grp_post = new grpPost();

						if($cron_through_tag_or_author == 1){
							grp_post::grp_log("Current Tag = $tag ,Current page Number = $current_page");
							
							//$total_page = 100;  //!!! important
							$total_page = 2;  //!!! important
							
							$current_tag_value = $new_tag_arr[$tag_key];
							update_option('grp_cron_tag_current_value', $current_tag_value);
							
							$grp_post_data = $grp_post->grp_get_data( $tag, $current_page,$cron_through_tag_or_author );
							#set currentpage parameters
							// $current_page++;
							if ($grp_post_data == 'elseif')
							{
							}
							else
							{
								
								$current_page++;
								update_option( 'grp_cron_current_page', $current_page );
							}
							// update_option( 'grp_cron_current_page', $current_page );
							

							if($total_page < $current_page){
								update_option( 'grp_cron_through_tag_or_author', 2 );
								update_option( 'grp_cron_current_page', '' );
								
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
						else if($cron_through_tag_or_author == 2){
							if( !empty( $author_arr ) ){
								#author name
								$author_name = $author_arr[$author_key];
								#creating  tag
								if ($author_key == 0)
								{
									$current_author = add_option('grp_cron_author_current_value', $author_arr[0]);
								}
								//$tag = $author_name.' '.$tag.' quotes';
								$tag = $author_name.' '.$tag;
								$author_total_page  = get_option( 'grp_cron_author_total_page' );
								if( empty( $author_total_page ) ){
									$total_page = $grp_post->grp_author_post_last_page($tag);
									update_option('grp_cron_author_total_page',$total_page);
								}else{
									$total_page = $author_total_page;
								}
								grp_post::grp_log("In author Current Tag = $tag ,Current page Number = $current_page");
								$grp_post_var = $grp_post->grp_get_data( $tag, $current_page,$cron_through_tag_or_author );
								#set currentpage parameters
								if($total_page < $current_page){
									$author_key++;
									$current_author_value = $author_arr[$author_key];
									update_option('grp_cron_author_current_value', $current_author_value);
								
									update_option( 'grp_cron_author_key', $author_key );
									update_option('grp_cron_author_total_page','');
									update_option( 'grp_cron_current_page', '' );
									if($last_author_key <= $author_key){
										$tag_key++;
										update_option( 'grp_cron_through_tag_or_author', 1 );
										update_option( 'grp_cron_tag_key', $tag_key );
										update_option( 'grp_cron_author_key','');
									}
									if($last_tag_key <= $tag_key){
										
										update_option( 'grp_cron_completed', 'yes' );
									}
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
		}
		


	}

}
$grpcron = new grpCron();
?>