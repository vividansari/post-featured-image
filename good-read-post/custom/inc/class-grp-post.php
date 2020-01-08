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

		$url = "https://www.goodreads.com/quotes/search?commit=Search&page=$page&q=$tag&utf8=%E2%9C%93";
		$new_url = 'https://www.goodreads.com/quotes/tag/'.$tag.'?page='.$page;
		
		$html = file_get_contents($url);

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
				$author_name = trim($quote_arr[1]);
				$author_arr = explode(',', $author_name);
				$author_book = '';
				if(isset($author_arr[0])){
					$author_name = $author_arr[0];	
				}
				
				$tag_data = array(
					'tag' => $tag,
					'author' => $author_name,
				);
				if(isset($author_arr[1])){
					$tag_data['book_title'] = trim($author_arr[1]);	
				}
				$this->grp_create_post($quote,$tag,$tag_data);		
			}

		}
	}

	public function grp_create_post( $quote , $tag , $tag_data ){

		if ( ! function_exists( 'post_exists' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

		$check_post = post_exists( $quote , '', '', 'post' );
		
		if($check_post == 0 ){

			$after_content = '<br>
				<br>

				Like <INSERT AUTHOR>?

				Buy <INSERT AUTHOR> products

				[easyazon_link keywords="<INSERT AUTHOR>" locale="US" tag="bestsellerlisting-20"]<img class="alignnone wp-image-63470 size-medium" src="https://loveinquotes.com/wp-content/uploads/2019/12/amazon-logo.png" width="300" height="92" />[/easyazon_link]

				Do you Like Positive words and poetry?

				Check out
				<table>
				<tbody>
				<tr>
				<td><a href="https://positivewordsdictionary.com/">https://positivewordsdictionary.com/</a></td>
				<td><a href="https://wordsthatrhymewith.com/">https://wordsthatrhymewith.com/</a></td>
				</tr>
				</tbody>
				</table>
				';
				if( isset( $tag_data['author'] ) ){
					$after_content = str_replace('<INSERT AUTHOR>',$tag_data['author'],$after_content);
				}else{
					$after_content = '';
				}
				$post_content = $quote .' '.$after_content;
			
			$arg = array(
				'post_title'    => wp_strip_all_tags( $quote ),
	  			'post_content'  => $post_content,
	  			'post_status'   => 'publish',
			);
			$post_id = wp_insert_post( $arg );	

			update_post_meta($post_id,'grp_tag',$tag);
			update_post_meta($post_id,'_yoast_wpseo_metadesc',$quote);

			$post_tag_str = '';
			if( isset( $tag_data['tag'] ) ){
				$post_tag_str .= $tag_data['tag'].' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){
				$post_tag_str .= $tag_data['author'].', ';
			}
			if( isset( $tag_data['book_title'] ) ){
				$post_tag_str .= $tag_data['book_title'].', ';
			}
			if( isset( $tag_data['book_title'] ) ){
				$post_tag_str .= $tag_data['book_title'].' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){
				$post_tag_str .= $tag_data['author'].' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){
				$post_tag_str .= $tag_data['author'].' '.$tag_data['tag'].' quotes, ';
			}
			wp_set_post_tags($post_id,$post_tag_str);

		}
		

	}

	public function grp_wp_insert_post( $post_id, $post, $update ){
		/*echo '<pre>';
		print_r($post);
		echo '</pre>';
		die;*/

		 if (!isset($post->ID) )
   			 return;
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

		if($post_status == 'auto-draft')
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