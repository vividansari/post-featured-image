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
	public function grp_get_data($tag, $page='',$cron_through_tag_or_author){

		$page = empty($page) ? 1 : $page;
		$url_tag = urlencode( $tag );

		if($cron_through_tag_or_author == 1){
			//$url = 'https://www.goodreads.com/quotes/tag/'.$tag.'?page='.$page;
			$url = 'https://www.goodreads.com/quotes/search?commit=Search&page='.$page.'&q='.$url_tag.'&utf8=%E2%9C%93';
		}
		else if($cron_through_tag_or_author == 2){
			$url = "https://www.goodreads.com/quotes/search?commit=Search&page=$page&q=".$url_tag."&utf8=%E2%9C%93";
		}
		
		$html = file_get_contents($url);
		
		if( $html ){
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
		}
		
		
		if($quotes_classes->length > 0){
			$childclass = "quoteText";
			$count = 0;
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
				$tag_data = array();
				$tag_data['tag'] = $tag;
				if(isset($author_arr[0])){
					$author_name = $author_arr[0];
					$tag_data['author'] = $author_name;
				}else{
					$author_name = '';
				}
				
				if(isset($author_arr[1])){
					$tag_data['book_title'] = trim($author_arr[1]);
				}

				$this->grp_create_post($quote,$tag,$tag_data);
				$count++;
			}
		}
		elseif($quotes_classes->length == 0 && $cron_through_tag_or_author == 1)
		{
			$get_tag_key = get_option( 'grp_cron_tag_key');
			$get_tag_key = !empty( $get_tag_key ) ? $get_tag_key : 0;
			$get_tag_key++;
			$page = 1;
			update_option( 'grp_cron_current_page', $page );
			update_option( 'grp_cron_tag_key', $get_tag_key );
			$post_var = "elseif";
			return $post_var;
		}
	}

	public function grp_author_post_last_page( $tag ){

		$url_tag = urlencode( $tag );
		$url = "https://www.goodreads.com/quotes/search?commit=Search&page=1&q=".$url_tag;

		$html = file_get_contents($url);
		if($html){
			$dom = new DOMDocument;
			@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
			//removing elements from a nodelist resets the internal pointer, so traverse backwards:
			$elements = $dom->getElementsByTagName('script');
			$count = $elements->length;
			while(--$count){
			    $elements->item($count)->parentNode->removeChild($elements->item($count));
			}
			$finder = new DomXPath($dom);
			$last_element = $finder->query("/html/body//div[@class='leftContainer']/div[last()]");
		}

		$page_number = 1;
		if($last_element->length > 0){
			foreach ($last_element as $last_nodes) {
				$page_number = $finder->evaluate('string(.//div/a[last()-1])', $last_nodes);
			}
		}
		return $page_number;
	}

	public function grp_get_author($page = ''){

		$page = !empty( $page ) ? $page : 1;

		$link = "https://www.goodreads.com/author/on_goodreads?page=$page&skip_cache=true";
		$html = file_get_contents($link);
		if($html){
			$dom = new DOMDocument;
			@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
			//removing elements from a nodelist resets the internal pointer, so traverse backwards:
			$elements = $dom->getElementsByTagName('script');
			$count = $elements->length;
			if($count > 0){
				while(--$count){
				    $elements->item($count)->parentNode->removeChild($elements->item($count));
				}
			}

			$finder = new DomXPath($dom);
			$auhthorclassname="elementList bookAuthorProfile";

			$author_classes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $auhthorclassname ')]");

			$authorpageclass = "u-marginTopSmall u-textAlignRight";
			$page_element = $finder->query("/html/body//div[@class='u-marginTopSmall u-textAlignRight']/div[last()]");

		}

		$author_arr = array();
		if($author_classes->length > 0){
			foreach ($author_classes as $author_nodes) {
				$author_name = $finder->evaluate('string(.//*[@class="bookAuthorProfile__name"][1])', $author_nodes);

				$author_arr[] = $author_name;
			}
		}

		return $author_arr;

	}

	public function grp_get_author_last_page_number(){

		$author_url = "https://www.goodreads.com/author/on_goodreads";

		$html = file_get_contents($author_url);
		if( $html ){
			$dom = new DOMDocument;
			@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
			//removing elements from a nodelist resets the internal pointer, so traverse backwards:
			$elements = $dom->getElementsByTagName('script');
			$count = $elements->length;
			if($count > 0){
				while(--$count){
				    $elements->item($count)->parentNode->removeChild($elements->item($count));
				}
			}

			$finder = new DomXPath($dom);
			$auhthorclassname="elementList bookAuthorProfile";

			$author_classes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $auhthorclassname ')]");

			$authorpageclass = "u-marginTopSmall u-textAlignRight";
			$page_element = $finder->query("/html/body//div[@class='u-marginTopSmall u-textAlignRight']/div[last()]");
		}

		$page_number = 0;
		if($page_element->length > 0){
			foreach ($page_element as $last_nodes) {
				$page_number = $finder->evaluate('string(.//a[last()-1])', $last_nodes);
			}
		}

		if( $page_number ){
			update_option( 'grp_author_total_page', $page_number );
		}
		return $page_number;
	}

	public function grp_create_post( $quote , $tag , $tag_data ){

		if ( ! function_exists( 'post_exists' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

			$after_content = 'Do you Like Positive words, poetry and words to describe someone you love?
				Check out
				<table>
				<tbody>
				<tr>
				<td><a href="https://positivewordsdictionary.com/">https://positivewordsdictionary.com/</a></td>
				</tr>
                <tr>
				<td><a href="https://wordsthatrhymewith.com/">https://wordsthatrhymewith.com/</a></td>
				</tr>
				<tr>
				<td><a href="https://wordstodescribesomeone.com/">https://wordstodescribesomeone.com/</a></td>
				</tr>
				</tbody>
				</table>';
				if( isset( $tag_data['author'] ) ){
					$after_content = str_replace('<INSERT AUTHOR>',$tag_data['author'],$after_content);
				}else{
					$after_content = '';
				}
				$post_content = $quote .' '.$after_content;

		$check_post = post_exists( $quote , $post_content, '', 'post' );
		if(!post_exists( $quote , $post_content, '', 'post' )){

			$arg = array(
				'post_title'    => wp_strip_all_tags( $quote ),
				// 'post_name'		=> $slug_quotes,
	  			'post_content'  => $post_content,
	  			'post_status'   => 'publish',
			);

			$post_id = wp_insert_post( $arg );

			update_post_meta($post_id,'grp_tag',$tag);
			update_post_meta($post_id,'_yoast_wpseo_metadesc',$quote);

			$post_tag_str = '';
			if( isset( $tag_data['tag'] ) ){
				$_tag = str_replace('quotes', '', $tag_data['tag']);
				$post_tag_str .= $_tag.' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){
				$post_tag_str .= $tag_data['author'].', ';
			}
			if( isset( $tag_data['book_title'] ) ){
				$post_tag_str .= $tag_data['book_title'].', ';
			}
			if( isset( $tag_data['book_title'] ) ){
				$_book_title_tag = str_replace('quotes', '', $tag_data['book_title']);
				$post_tag_str .= $_book_title_tag.' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){
				$_author_tag = str_replace('author', '', $tag_data['author']);
				$post_tag_str .= $_author_tag.' quotes, ';
			}
			if( isset( $tag_data['author'] ) ){

				$_author_tag = str_replace('quotes', '', $tag_data['author']);
				$_tag = str_replace( $_author_tag, '', $tag_data['tag']);
				$_tag = str_replace('quotes', '', $_tag);
				$post_tag_str .= $_author_tag.' '.$_tag.' quotes, ';
			}
			$tag_arr = explode(',', $post_tag_str);
			$tag_arr = array_unique($tag_arr);
			$post_tag_str = implode(',', $tag_arr);
			wp_set_post_tags($post_id,$post_tag_str);

		}else{
			
		}


	}
	public function grp_wp_insert_post( $post_id, $post, $update ){

		 if (!isset($post->ID) ){
   			 return;
		 }
		// If this is a revision, don't send the email.
   		if ( wp_is_post_revision( $post_id ) ){
   			return;
   		}

        // If the post already has a featured image, don't generate an image
    	if (has_post_thumbnail($post_id)){
	    	return;
	    }

    	// If post is in the trash, don't generate an image
	    $post_status = get_post_status($post_id);
	    if($post_status == 'trash'){
		    return;
		}

		if($post_status == 'auto-draft'){
			return;
		}

    	// Try to prevent the script from timing out or running out of memory
    	set_time_limit(0);
    	wp_cache_flush();

        $vagi = new vaGenerateImage();
		$vagi->get_image_from_pixbay($post_id);

	}

}
$grp_post = new grpPost();
?>