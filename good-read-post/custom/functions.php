<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require GRP_PLUGIN_INC_DIR.'class-create-image.php';
require GRP_PLUGIN_INC_DIR.'class-grp-post.php';
require GRP_PLUGIN_INC_DIR.'class-grp-cron.php';

add_action('wp_ajax_get_gr_author_list','get_gr_author_list');
function get_gr_author_list(){


	$startpos = $_POST['startpos'];
    $lastpos = $_POST['lastpos'];



    if( empty( $lastpos ) ){
    	#check page number already have or not
		$lastpos = get_option( 'grp_author_total_page' );

		if( !$lastpos ){
		
			$grp_data = new grpPost();
			$lastpos = $grp_data->grp_get_author_last_page_number();

		}
    }
   // $lastpos = 2;
    $d = date("j-M-Y H:i:s");
    //$final_data = $configData[0];
 	$end_pos = $startpos+1;
   	
   	
	$grp_data = new grpPost();

    if($lastpos <= $startpos){
      $authors = $grp_data->grp_get_author($startpos);
      $author_arr = get_option( 'grp_author_data' );
      if( empty( $author_arr ) ){
          $author_arr = $authors;	
      }else{
          $author_arr = array_merge($author_arr , $authors);
      }
      
      $author_arr = array_values(array_unique( $author_arr ) );
      update_option( 'grp_author_data', $author_arr );
      $message = '['.$d.']- page number '.$startpos.' - Done';
      wp_send_json_success(
        array(
            'pos' => 'done',
                //'percentage' => vvd_get_percent_complete($total_data,$end_pos),
            'message' => $message,
            
        )
    );
    }else{

        $message = '['.$d.'] - page number '.$startpos.' -done ';
        
        $authors = $grp_data->grp_get_author($startpos);

        $author_arr = get_option( 'grp_author_data' );
	   	if( empty( $author_arr ) ){
	   		$author_arr = $authors;	
	   	}else{
	   		$author_arr = array_merge($author_arr , $authors);
	   	}
       
        $author_arr = array_values(array_unique( $author_arr ) );
        update_option( 'grp_author_data', $author_arr );
        wp_send_json_success(
            array(
                'pos' => $end_pos,
                'lastpos' => $lastpos,
                //'percentage' => vvd_get_percent_complete($total_data,$end_pos),
                //'total_product' => $total_product,
                'message' => $message,
            )
        );
    }



	
	/*$author_arr = array();
	if( $page_number ){
		
		for ($i=1; $i <= $page_number; $i++) { 
			$page = $i;
			$authors = $grp_data->grp_get_author($page);
			array_merge($author_arr,$authors);
		}
	}
	echo '<pre>';
	print_r($author_arr);
	echo '</pre>';*/
	
	wp_die();
}
// add_action('init','ss_new_test');
// function ss_new_test(){
//    do_action('grp_get_data_cron') ;
// }
?>