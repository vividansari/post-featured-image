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
		grp_post::grp_log(print_r($new_tag_arr,true));
		// $new_tag_arr = array(
		// 	'love',
		// 	'life',
		// 	'inspirational',
		// 	'humor',
		// 	'philosophy',
		// 	'god',
		// 	'inspirational quotes',
		// 	'truth',
		// 	'wisdom',
		// 	'romance',
		// 	'poetry',
		// 	'death',
		// 	'hapiness',
		// 	'hope',
		// 	'faith',
		// 	'writing',
		// 	'inspiration',
		// 	'quotes',
		// 	'religion',
		// 	'life lessons',
		// 	'success',
		// 	'relationships',
		// 	'motivational',
		// 	'time',
		// 	'knowledge',
		// 	'love quotes',
		// 	'spirituality',
		// 	'science',
		// 	'books',
		// 	'education',
		// );
		// $tag = $settings['grp_tag'];  //!!! important

		$tag_key = get_option( 'grp_cron_tag_key');

		$tag_key = !empty( $tag_key ) ? $tag_key : 0;
		////grp_post::grp_log('--------------------------- tag key:'.$tag_key.' -------------------');
		$tag = $new_tag_arr[$tag_key];
		$last_tag_key = count($new_tag_arr);
		////grp_post::grp_log('--------------------------- Start Cron -------------------');

		if( !empty( $tag ) ){
			//grp_post::grp_log('---------------------------'.$tag.'-------------------');
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
				//grp_post::grp_log('--author_key--'.$author_key);
				#get author
				$author_arr = get_option( 'grp_author_data' );
				$last_author_key = count($author_arr);
				if( !empty( $author_arr ) ){
					$grp_post = new grpPost();
					if($cron_through_tag_or_author == 1){
						//grp_post::grp_log('--------------------Search tag----------------------');
						// $total_page = 100;  !!! important
						$total_page = 100;
						$grp_post->grp_get_data( $tag, $current_page,$cron_through_tag_or_author );
						#set currentpage parameters
						$current_page++;

						update_option( 'grp_cron_current_page', $current_page );
						//grp_post::grp_log('-- tag total page--'.$total_page);
						//grp_post::grp_log('-- tag current page--'.$current_page);

						if($total_page < $current_page){
							update_option( 'grp_cron_through_tag_or_author', 2 );
							update_option( 'grp_cron_current_page', '' );
							//grp_post::grp_log('-- cron change to authore page--');
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
					//grp_post::grp_log('-------------------- Search author tag----------------------');
					#author name
					$author_name = $author_arr[$author_key];
					#creating  tag
					$tag = $author_name.' '.$tag.' quotes';
					//grp_post::grp_log('--author search tag--'.$tag);
					$author_total_page  = get_option( 'grp_cron_author_total_page' );
					if( empty( $author_total_page ) ){
						$total_page = $grp_post->grp_author_post_last_page($tag);
						update_option('grp_cron_author_total_page',$total_page);
					}else{
						$total_page = $author_total_page;
					}
					//grp_post::grp_log('--author total page--'.$total_page);
					//grp_post::grp_log('--author current page--'.$current_page);
					//foreach ($author_arr as $key => $author_name) {

						$grp_post->grp_get_data( $tag, $current_page,$cron_through_tag_or_author );
						#set currentpage parameters
						$current_page++;

						update_option( 'grp_cron_current_page', $current_page );

						if($total_page < $current_page){
							$author_key++;
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
								//grp_post::grp_log('---------- Cron Complete   --------');
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

					//}
				}

			}
		}


	}

}
$grpcron = new grpCron();
?>