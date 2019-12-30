<?php
/**
 * grp post scrapping
 */
class grpPost 
{
	
	function __construct()
	{
		add_action( 'wp_insert_post', array( $this , 'grp_wp_insert_post' ) , 10, 3 );	
	}
	public function grp_get_data($tag, $page=''){

		$page = empty($page) ? 1 : $page;
		$html = file_get_contents('https://www.goodreads.com/quotes/tag/'.$tag.'?page='.$page);

		$dom = new DOMDocument;
		@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		//removing elements from a nodelist resets the internal pointer, so traverse backwards:
		$elements = $dom->getElementsByTagName('script');
		$count = $elements->length;
		while(--$count){
		    $elements->item($count)->parentNode->removeChild($elements->item($count));
		}

		$finder = new DomXPath($dom);

		$classname="quote mediumText";

		$quotes_classes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");


		if($quotes_classes->length > 0){
			$childclass = "quoteText";

			foreach ($quotes_classes as $quote_nodes) {
				$quote = $finder->evaluate('string(.//*[@class="quoteText"][1])', $quote_nodes);
				$quote_arr  = explode( '―', $quote );
				$search_array = array('“','”');
				$replace_arr = array("","");
				$quote = str_replace($search_array, $replace_arr , $quote_arr[0]);
				$quote = $quote.' ― '.trim($quote_arr[1]);
				$this->grp_create_post($quote,$tag);		
			}

		}
	}

	public function grp_create_post( $quote, $tag ){

		if ( ! function_exists( 'post_exists' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

		$check_post = post_exists( $quote , '', '', 'post' );
		
		if($check_post == 0 ){
			
			$arg = array(
				'post_title'    => wp_strip_all_tags( $quote ),
	  			'post_content'  => $quote,
	  			'post_status'   => 'publish',
			);
			$post_id = wp_insert_post( $arg );	

			update_post_meta($post_id,'grp_tag',$tag);
		}
		

	}

	public function grp_wp_insert_post( $post_id, $post, $update ){
		// If this is a revision, don't send the email.
   		if ( wp_is_post_revision( $post_id ) )
        	return;

        // If the post already has a featured image, don't generate an image
    	if (has_post_thumbnail($post_id))
    	return;

    	// If post is in the trash, don't generate an image
	    $post_status = get_post_status($post_id);
	    if($post_status == 'trash')
	    return;

    	// Try to prevent the script from timing out or running out of memory
    	set_time_limit(0);
    	wp_cache_flush();
        $vagi = new vaGenerateImage();
		$vagi->get_image_from_pixbay($post_id);


	}

}
$grp_post = new grpPost();
?>