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

// upload tag csv file.
function get_grp_tag_csv()
{
  if (isset($_POST['grp_form']))
  {
    if (!empty($_FILES))
    {
      $csv_file = $_FILES['tag_csv'];
      $csv_file_name = $_FILES['tag_csv']['name'];
      $uploaddir = GRP_PLUGIN_DIR.'tag_csv/';

      // $uploadfile = $uploaddir . basename($_FILES['tag_csv']['name']);
      $num = mt_rand(10000 , 99999);
      $new_filename = $uploaddir . $num . str_replace(" ", "", basename($_FILES['tag_csv']['name']));
      if (move_uploaded_file($_FILES['tag_csv']['tmp_name'], $new_filename))
      {
        /*********** Get array of CSV files ****************/
        $files = glob($new_filename) ;
        $file=$files[0];
        /******** Attempt to change permissions if not readable *********/
        if(! is_readable($file)){
          chmod($file, 0774);

        }
        /******** Check if file is writable, then open it in 'read only' mode *********/
        if(is_readable($file) && $_file = fopen($file,'r')){
          $post=array();

           //get header of csv file
          $header=fgetcsv($_file);
           //row, column by column, saving all the data
          while($row =fgetcsv($_file)){
              $post[] = $row[0];
          }
          // $data =$post;
          fclose($_file);
          if (!empty($post) && count($post) > 0)
          {
            update_option('grp_tag_csv_key', $post);
          }
          echo "success";
        }
        else{
          $errors[]="File does not open";
        }

        // echo '<pre>';
        // print_r($data);
        // echo '<pre>';
      }
      else
      {
        echo "Upload failed";
      }
    }
  }
  die();
}
add_action('wp_ajax_get_grp_tag_csv', 'get_grp_tag_csv');

// upload author csv file.
function get_grp_author_csv()
{
  if (isset($_POST['grp_form']))
  {
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // die();

    if (!empty($_FILES))
    {

      $csv_file = $_FILES['author_csv'];
      $csv_file_name = $_FILES['author_csv']['name'];
      $uploaddir = GRP_PLUGIN_DIR.'author_csv/';

      // $uploadfile = $uploaddir . basename($_FILES['tag_csv']['name']);
      $num = mt_rand(10000 , 99999);
      $new_filename = $uploaddir . $num . str_replace(" ", "", basename($_FILES['author_csv']['name']));
      if (move_uploaded_file($_FILES['author_csv']['tmp_name'], $new_filename))
      {
        /*********** Get array of CSV files ****************/
        $files = glob($new_filename) ;
        $file=$files[0];
        /******** Attempt to change permissions if not readable *********/
        if(! is_readable($file)){
          chmod($file, 0774);

        }
        /******** Check if file is writable, then open it in 'read only' mode *********/
        if(is_readable($file) && $_file = fopen($file,'r')){
          $author_arr=array();

           //get header of csv file
          $header=fgetcsv($_file);
           //row, column by column, saving all the data
          while($row =fgetcsv($_file)){
              $author_arr[] = trim($row[0]);
              $author_option = get_option('grp__author_names');
              if( empty( $author_option ) ){
                $author_option = array();
                $author_option[] = $row[0];
              }else{
                $author_option[] = $row[0];
              }
              update_option( 'grp__author_names', $author_option );
          }

          // $data =$post;
          fclose($_file);
         /* echo "<pre>";
          print_r($author_arr);
          echo "</pre>";*/
          update_option( 'checking_new_ansari', 'Yes' );
          $new_arr = maybe_serialize($author_arr);
          echo "new_arr = $new_arr";
          update_option( 'checking_new_ansari_new',  $new_arr );
          update_option( 'checking_new_ansari_1', 'Yes' );
          if (!empty($post) && count($post) > 0)
          {
            // echo "in if";
            update_option('grp_author_csv', $post);

          }
          echo "success";

        }
        else{
          $errors[]="File does not open";
        }

        // echo '<pre>';
        // print_r($data);
        // echo '<pre>';
      }
      else
      {
        echo "Upload failed";
      }
    }
  }
  die();
}
add_action('wp_ajax_get_grp_author_csv', 'get_grp_author_csv');
?>