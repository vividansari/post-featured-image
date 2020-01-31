<?php 
/**
 * class for generating images
 */

class vaGenerateImage 
{
	function __construct(){
			
	}

	public function get_image_from_pixbay( $post_id = '' ){
		
		$settings = get_option( "grp_plugin_settings" );
		$pixbay_api_key = $settings['grp_pixabay_api'];
		$q_tag = $settings['grp_pixabay_q_tag'];
		$cat = $settings['grp_pixabay_cat'];

		#search paramenters
		$query = !empty($q_tag) ? $q_tag : 'love';
		$cat = !empty($cat) ? '&category='.$cat : '';

		$done = false;
	    $tries = 0;
	    while(!$done && $tries < 5){
		    $tries++;
		    $search = 'https://pixabay.com/api/?key='.$pixbay_api_key.'&q='.$query.'&image_type=photo&per_page=100&order=popular'.$cat;

		    $result = wp_remote_request($search);
		    if( !is_wp_error( $result ) && ($result['response']['code'] == 200 || $result['response']['code'] == 201) ){

		    	$data = json_decode($result['body'], true);
			    if($data['totalHits'] > 0){
			        if($data['totalHits'] > 100){
			            $n = rand(0,99);
			            }
			        else{
			            $n = rand(0,($data['totalHits']-1));
			            }
					$backgroundimage1 = $data['hits'][$n]['largeImageURL'];
					$backgroundimage2 = wp_remote_request($backgroundimage1);
			        if( !is_wp_error( $backgroundimage2 ) && ($result['response']['code'] == 200 || $result['response']['code'] == 201) ){
				        /* Create a temp file to use as bg */
				        $temp_file = GRP_IMAGE_DIR . 'temp.jpg';
				        $file_upload = file_put_contents( $temp_file, $backgroundimage2['body'] );
				       if( $file_upload ) {
							$backgroundimg = $temp_file;
				            $done = true;
				        }
				    }
			    }	
		    }
		    
	    }
	    
	    if( !empty( $backgroundimg ) ){
	    	grp_post::grp_log('image find='.$post_id);
	    	$this->generate_image($backgroundimg, $post_id );
	    }else{
	    	grp_post::grp_log('image not find='.$post_id);
	    	$temp_file = GRP_IMAGE_DIR . 'temp.jpg';
	    	$this->generate_image($temp_file, $post_id );
	    }
	}

	public static function generate_image( $backgroundimg , $post_id = '' ){
		grp_post::grp_log('generate_image');
		$settings = get_option( "grp_plugin_settings" );
		$va_image_resize = 'crop';
	    $auto_image_width = 1130;
	    $auto_image_height = 580;
	    $auto_image_write_text = 'yes';

		$ext = strtolower(pathinfo($backgroundimg, PATHINFO_EXTENSION));
		if($ext == 'png'){
	        $new_featured_img = imagecreatefrompng($backgroundimg);
	    }elseif($ext == 'gif'){
	        $new_featured_img = imagecreatefromgif($backgroundimg);
	    }else{
	        $new_featured_img = imagecreatefromjpeg($backgroundimg);
	    }

	    //image width and height
	    $width = imagesx($new_featured_img);
	    $height = imagesy($new_featured_img);

	    $new_featured_image = $new_featured_img;

	   

	    if($va_image_resize != 'no'){
	    	
	    	$original_aspect = $width / $height;
		    $auto_image_aspect = $auto_image_width / $auto_image_height;

	        if ( $original_aspect >= $auto_image_aspect ){
	            // If original image is wider than new generated image (in aspect ratio sense)
	            $new_width = $width / ($height / $auto_image_height);
	            $new_height = $auto_image_height;
	        }else {
	            // If new generated image is wider than original image
	            $new_width = $auto_image_width;
	            $new_height = $height / ($width / $auto_image_width);
	        }

	        if($va_image_resize == 'crop'){
		        // Resize and crop
		        $auto_image = imagecreatetruecolor( $auto_image_width, $auto_image_height );
		        imagecopyresampled(
		            $auto_image,
		            $new_featured_img,
		            // Center the image horizontally
		            0 - ($new_width - $auto_image_width) / 2,
		            // Center the image vertically
		            0 - ($new_height - $auto_image_height) / 2,
		            0, 0,
		            $new_width, $new_height,
		            $width, $height);
		        $new_featured_img = $auto_image;
		    }
	    }

   
	   
	    if($auto_image_write_text=='yes'){
	       self::put_text_on_image($new_featured_img,$post_id);
	    }
	}

	public static function put_text_on_image($new_featured_img , $post_id = ''){
		
		$settings = get_option( "grp_plugin_settings" );
		$copyright_text = isset($settings['grp_copyright_text']) ? $settings['grp_copyright_text'] : '';
		$image_text_color = isset($settings['grp_text_color']) ? $settings['grp_text_color'] : '';
		$image_text_shadow_color = isset($settings['grp_text_shadow_color']) ? $settings['grp_text_shadow_color'] : '';
		$image_text_size = isset($settings['grp_text_size']) ? $settings['grp_text_size'] : '';
		$image_copyrighttext_size = isset($settings['grp_copyright_text_size']) ? $settings['grp_copyright_text_size'] : '';
		$image_copyright_color = isset($settings['grp_copy_right_text_color']) ? $settings['grp_copy_right_text_color'] : '';
		$text_transform = isset($settings['grp_text_transform']) ? $settings['grp_text_transform'] : '';
		$text_x_position = isset($settings['grp_x_position']) ? $settings['grp_x_position'] : '';
		$text_y_position = isset($settings['grp_y_position']) ? $settings['grp_y_position'] : '';
 		$text_padding_top= isset($settings['grp_padding_top']) ? $settings['grp_padding_top'] : '';
 		$text_padding_bottom= isset($settings['grp_padding_bottom']) ? $settings['grp_padding_bottom'] : '';
 		$text_padding_left= isset($settings['grp_padding_left']) ? $settings['grp_padding_left'] : '';
 		$text_padding_right= isset($settings['grp_padding_right']) ? $settings['grp_padding_right'] : '';

 		$auto_image_remove_linebreaks = 'no';
 		
		#comman
		$auto_image_write_text = 'yes';
		$auto_image_width = 1130;
	    $auto_image_height = 580;
		
		#for text 
		$auto_image_bg_color = '#b5b5b5';
		$auto_image_text_color = !empty($image_text_color) ? $image_text_color : '#fff76d';
		$auto_image_border_color = !empty($image_text_shadow_color) ? $image_text_shadow_color : '#000000';
		$auto_image_shadow_color = !empty($image_text_shadow_color) ? $image_text_shadow_color : '#000000';

		$auto_copyright_text_color = !empty($image_copyright_color) ? $image_copyright_color : '#d6d1d1';

		#image generating 
		$auto_image_top_padding = !empty( $text_padding_top ) ? $text_padding_top : 10;
		$auto_image_bottom_padding = !empty( $text_padding_bottom ) ? $text_padding_bottom : 10;
		$auto_image_left_padding = !empty( $text_padding_left ) ? $text_padding_left : 10;
		$auto_image_right_padding = !empty( $text_padding_right ) ? $text_padding_right : 10;

		#$post data
		$post = get_post( $post_id );
		$content = $post->post_title;
		if( empty( $content ) ){
			$content = $post->post_content;
		}
		#get this data from the post
	    $auto_image_before_text  = '';
	    $auto_image_post_text  = $content;//'Alauddin Ansari';//post title
	    $auto_image_after_text = '';
		
	    #text settings
	    $auto_image_fontsize = !empty($image_text_size) ? $image_text_size : 30;
	    $font = GRP_FONT_DIR.'FreeSansBold.ttf';
	    $auto_image_text_transform = !empty($text_transform) ? $text_transform : 'none';//'uppercase'; //lowecase // capitalize
	    $auto_image_text_x_position = !empty($text_x_position) ? $text_x_position : 'center';// right // left
	    $auto_image_text_y_position = !empty($text_y_position) ? $text_y_position : 'center';//top//bottom

	    $auto_image_shadow = 'yes';
		$auto_image_border = 'no';

		$bg = self::va_hex2rgbcolors($auto_image_bg_color);
	    $text = self::va_hex2rgbcolors($auto_image_text_color);
	    $border = self::va_hex2rgbcolors($auto_image_border_color);
	    $shadow = self::va_hex2rgbcolors($auto_image_shadow_color);
	    $copyright_shadow = self::va_hex2rgbcolors($image_copyright_color);
	    

	    $text_color = imagecolorallocatealpha( $new_featured_img, (int)$text["red"], (int)$text["green"], (int)$text["blue"], 0);
        $border_color = imagecolorallocatealpha( $new_featured_img, (int)$border["red"], (int)$border["green"], (int)$border["blue"], 0);
        $shadow_color = imagecolorallocatealpha( $new_featured_img, (int)$shadow["red"], (int)$shadow["green"], (int)$shadow["blue"], 0);

        #new copyright 
        $copyright_color = imagecolorallocatealpha( $new_featured_img, (int)$copyright_shadow["red"], (int)$copyright_shadow["green"], (int)$copyright_shadow["blue"], 0);
           
        $auto_image_text_to_write = $auto_image_before_text . $auto_image_post_text . $auto_image_after_text;

        $auto_image_text_to_write = apply_filters('afift_pro_before_write_text', $auto_image_text_to_write);

       // include('write_text.php');
       // add text in image
        // Determine how much total padding is needed around the text
        $auto_image_top_bottom_padding = $auto_image_top_padding + $auto_image_bottom_padding;
        $auto_image_left_right_padding = $auto_image_left_padding + $auto_image_right_padding;

        if($auto_image_text_transform == 'uppercase'){
            $auto_image_transformed_post_text = strtoupper($auto_image_text_to_write);
        }elseif($auto_image_text_transform == 'lowercase'){
            $auto_image_transformed_post_text = strtolower($auto_image_text_to_write);
        }elseif($auto_image_text_transform == 'capitalize'){
            $auto_image_transformed_post_text = ucwords($auto_image_text_to_write);
        }else{
            $auto_image_transformed_post_text = $auto_image_text_to_write;
        }

         // Get rid of stubborn extra space
        $auto_image_transformed_post_text = str_replace('  ', ' ', $auto_image_transformed_post_text);
        $auto_image_transformed_post_text = str_replace('&#160;',' ',$auto_image_transformed_post_text);
        $auto_image_transformed_post_text = str_replace(' ',' ',$auto_image_transformed_post_text);

        // Get rid of stubborn character returns
  		$auto_image_transformed_post_text = str_replace("\r", '', $auto_image_transformed_post_text);
  		$auto_image_transformed_post_text = str_replace('&#13;', '', $auto_image_transformed_post_text);

  		 // Keep line breaks if the option is set
		if($auto_image_remove_linebreaks == 'yes'){
			$auto_image_transformed_post_text = str_replace('\n', ' ', $auto_image_transformed_post_text);
			$auto_image_transformed_post_text = str_replace("\n", ' ', $auto_image_transformed_post_text);
			$auto_image_transformed_post_text = str_replace("\N", ' ', $auto_image_transformed_post_text);
		}else{
			$auto_image_transformed_post_text = str_replace('\n', ' #10;', $auto_image_transformed_post_text);
			$auto_image_transformed_post_text = str_replace("\n", ' #10;', $auto_image_transformed_post_text);
			$auto_image_transformed_post_text = str_replace("\N", ' #10;', $auto_image_transformed_post_text);
			$auto_image_transformed_post_text = str_replace(' #10; #10;', ' #10;', $auto_image_transformed_post_text);
		}

		// Separate the text into its words
        $words = explode(" ", $auto_image_transformed_post_text);
        $auto_image_fontsize = $auto_image_fontsize + 3;

        do {
            $auto_image_fontsize = $auto_image_fontsize - 3;

		    // Unset variables if this is a subsequent attempt at writing the text
            if(isset($auto_image_text_x)){
                unset($auto_image_text_x);
                unset($auto_image_text_xx);
                unset($auto_image_text_y);
                unset($row);
                }

            // Position the text (the whole string)
            $auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, $auto_image_transformed_post_text);

            if($auto_image_text_x_position == 'left'){
                $auto_image_text_x[] = 0;
                $auto_image_text_xx[] = $auto_image_text_array[2];
                }
            elseif($auto_image_text_x_position == 'right'){
                $auto_image_text_x[] = $auto_image_width - $auto_image_text_array[2];
                $auto_image_text_xx[] = $auto_image_text_array[2];
                }
            else{
                $auto_image_text_x[] = ($auto_image_width - $auto_image_text_array[2]) / 2;
                $auto_image_text_xx[] = $auto_image_text_array[2];
                }

            $auto_image_text_y[] = abs($auto_image_text_array[5]);

		    $string = '';
            $tmp_string = '';
		    $before_break = '';
		    $after_break = '';

            $auto_image_text_array['height'] = abs($auto_image_text_array[7]) - abs($auto_image_text_array[1]);
            if($auto_image_text_array[3] > 0) {
                $auto_image_text_array['height'] = abs($auto_image_text_array[7] - $auto_image_text_array[1]) - 1;
                }
            $lineheight = $auto_image_text_array['height'] + 10;

            $ny = 0;
			for($i = 0; $i < count($words) || $before_break != ''; $i++) {

			    if($before_break != ''){
				    $tmp_string = $after_break;
				    $before_break = '';
                    }

			    // Add a word to the tmp string
		  		if($i>=count($words)){
				    $words[$i] = '';
				    }
                $tmp_string .= $words[$i]." ";

			    // Remove a line break if it begins the string
                if(substr($tmp_string, 0, 4) == '#10;'){
                    $tmp_string = substr($tmp_string, 4);
                    }

                // Check width of the last string to see if it fits within image
                $dim = imagettfbbox($auto_image_fontsize, 0, $font, rtrim($tmp_string));

                // Check to see if there is a line break in the tmp string
                $before_break = strstr($tmp_string, '#10;', true);
                $after_break = strstr($tmp_string, '#10;');

				if($dim[4] < ($auto_image_width-$auto_image_left_right_padding)) {
				  //				if($dim[4] < ($auto_image_width)) {
                    // If it fits, save it as a row
			        if($before_break != ''){
				        $string = rtrim($before_break);
                        $row[$ny] = rtrim($before_break);

                        $auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, rtrim($string));

                        if($auto_image_text_x_position == 'left'){
                            $auto_image_text_x[$ny] = 0;
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                            }
                        elseif($auto_image_text_x_position == 'right'){
			                $auto_image_text_x[$ny] = $auto_image_width - $auto_image_text_array[2];
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
			                }
			            else{
			                $auto_image_text_x[$ny] = ($auto_image_width - $auto_image_text_array[2]) / 2;
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                            }

                        $auto_image_text_y[$ny+1] = $auto_image_text_y[$ny] + $lineheight;
                        $ny++;
                        }
			        else{
                        $string = rtrim($tmp_string);
                        $row[$ny] = rtrim($tmp_string);
					    }
					}
				else {
                    $tmp_string = '';
		            $before_break = '';
		            $after_break = '';
                    
				    // If it doesn't fit, get the width of the whole string
                    $auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, rtrim($string));

                        if($auto_image_text_x_position == 'left'){
                            $auto_image_text_x[$ny] = 0;
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                            }
                        elseif($auto_image_text_x_position == 'right'){
			                $auto_image_text_x[$ny] = $auto_image_width - $auto_image_text_array[2];
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
			                }
			            else{
			                $auto_image_text_x[$ny] = ($auto_image_width - $auto_image_text_array[2]) / 2;
			                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                            }

				    $row[$ny] = $string;
				    $string = '';
                    $auto_image_text_y[$ny+1] = $auto_image_text_y[$ny] + $lineheight;
				    $i--;
                    $ny++;
 	                }
			    }

            $auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, $string);

            if($auto_image_text_x_position == 'left'){
                $auto_image_text_x[$ny] = 0;
                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                }
            elseif($auto_image_text_x_position == 'right'){
                $auto_image_text_x[$ny] = $auto_image_width - $auto_image_text_array[2];
                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                }
            else{
                $auto_image_text_x[$ny] = ($auto_image_width - $auto_image_text_array[2]) / 2;
                $auto_image_text_xx[$ny] = $auto_image_text_array[2];
                }

            $rowsoftext = count($row);
            $bottom_of_text = ($lineheight*$rowsoftext)-10;
            $longest_row_x = min($auto_image_text_x);
			$longest_row_xx = max($auto_image_text_xx);
        } while (($bottom_of_text > ($auto_image_height - $auto_image_top_bottom_padding)) || ($longest_row_xx > ($auto_image_width - $auto_image_left_right_padding)));

        if($auto_image_text_y_position == 'top'){
            $offset = $auto_image_top_padding;
        }elseif($auto_image_text_y_position == 'bottom'){
            $offset = $auto_image_height - $bottom_of_text - $auto_image_bottom_padding;
        }else{
            $offset = ($auto_image_height - $auto_image_top_bottom_padding - $bottom_of_text)/2 + $auto_image_top_padding;
        }
       
            $copy_right_offset = $auto_image_height - $bottom_of_text - $auto_image_bottom_padding;
        for($i = 0; $i < $rowsoftext; $i++) {
			if($auto_image_text_x_position == 'left'){
                $auto_image_text_x[$i] = $auto_image_text_x[$i] + $auto_image_left_padding;
            }elseif($auto_image_text_x_position == 'right'){
                $auto_image_text_x[$i] = $auto_image_text_x[$i] - $auto_image_right_padding - 2;
            }else{
		        $auto_image_text_x[$i] = $auto_image_text_x[$i] + $auto_image_left_padding - $auto_image_right_padding;
            }
		}
		

		$i = 0;
        $row = apply_filters('afift_pro_before_write_rows', $row);
        $auto_image_text_x = apply_filters('afift_pro_before_write_rows', $auto_image_text_x);
        while ($i < $rowsoftext){
        if($auto_image_shadow=='yes'){
            imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i]+2, $auto_image_text_y[$i]+$offset+2, $shadow_color, $font, rtrim($row[$i]));
            }
        if(isset($auto_image_border) && ($auto_image_border=='yes')){
            imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i]+1, $auto_image_text_y[$i]+$offset, $border_color, $font, rtrim($row[$i]));
            imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i], $auto_image_text_y[$i]+$offset+1, $border_color, $font, rtrim($row[$i]));
            imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i]-1, $auto_image_text_y[$i]+$offset, $border_color, $font, rtrim($row[$i]));
            imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i], $auto_image_text_y[$i]+$offset-1, $border_color, $font, rtrim($row[$i]));
            }
        imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x[$i], $auto_image_text_y[$i]+$offset, $text_color, $font, rtrim($row[$i]));
        $i++;
        }

       if( !empty( $copyright_text ) ){
       		$y_last_key = end(array_keys($auto_image_text_y));

	        $white = imagecolorallocate($new_featured_img, 255, 255, 255);
			$txt = $copyright_text;
			$cpy_txt_size = !empty($image_copyrighttext_size) ? $image_copyrighttext_size : 10;
	        imagettftext($new_featured_img, $cpy_txt_size, 0, 5, $auto_image_text_y[$y_last_key]+$copy_right_offset, $copyright_color, $font, $txt);
       }
       
       self::add_image_in_wp( $new_featured_img,$post_id,$text_color,$border_color,$shadow_color );

	}

	public static function add_image_in_wp( $new_featured_img , $post_id = '' , $text_color, $border_color, $shadow_color){

		$settings = get_option( "grp_plugin_settings" );
		$auto_image_filetype = 'jpg';
		$auto_image_quality = 95;
		$auto_image_write_text = 'yes';

		$auto_image_set_as_featured = "yes";
		$auto_image_insert_into_post = "no";

		#post_data
		##$post data
		$post = get_post( $post_id );
		$content = $post->post_title;
		if( empty( $content ) ){
			$content = $post->post_content;
		}

		$auto_image_post_title = $content;
		$auto_image_post_text = $content; //"Alauddin Ansari";

		if($auto_image_post_title == ''){
        	$auto_image_post_title = 'image';
	    }

	    // Save the image
	    $attachment_array = array(
	        'title'          => preg_replace( '/\.[^.]+$/', '', basename( $auto_image_post_text ) ),
	        'alt'            => $auto_image_post_text,
	        'caption'        => $auto_image_post_text,
	        'description'    => $auto_image_post_text,
	        'filename'       => $auto_image_post_title,
	        'filename_spaces' => '-'
	        );
	  /*  $attachment_array = apply_filters('afift_pro_before_save_image', $attachment_array, $post_id);*/
	    $regex = array('/[^\p{L}\-\.\p{N}\s]/u', '/\s/');
	    $repl  = array('', $attachment_array['filename_spaces']);
	    $image_slug = sanitize_title( $attachment_array['filename'] );
	    $post_slug = strtolower(preg_replace($regex, $repl, $image_slug));
	    $post_slug = preg_replace('#[ -]+#', $attachment_array['filename_spaces'], $post_slug);
	    $post_slug = substr($post_slug, 0, 100);
	 
	    $upload_dir = wp_upload_dir();
	    $slug_n = '';
	    while(file_exists($upload_dir['path'] . '/' . $post_slug . $slug_n . '.' . $auto_image_filetype)){
	        if($slug_n == ''){
	            $slug_n = 1;
	            }
	        else {
	            $slug_n++;
	            }
	        }
	    $newimg = $upload_dir['path'] . '/' . $post_slug . $slug_n . '.' . $auto_image_filetype;

	    if($auto_image_filetype == 'jpg'){
	        imagejpeg( $new_featured_img, $newimg, $auto_image_quality );
		    }
	    else{
	        imagepng( $new_featured_img, $newimg, 0 );
		    }

	    if($auto_image_write_text=='yes'){
	        imagecolordeallocate( $new_featured_img, $text_color );
	        imagecolordeallocate( $new_featured_img, $border_color );
	        imagecolordeallocate( $new_featured_img, $shadow_color );
		    }
		

	    // Process the image into the Media Library
	    $newimg_url = $upload_dir['url'] . '/' . $post_slug . $slug_n . '.' . $auto_image_filetype;
	    
	    if($auto_image_filetype=='jpg'){
			$mime_type = 'jpeg';
		    }
		else{
			$mime_type = 'png';
		    }

	    if(isset($caption)){
			$post_excerpt = $caption;
			}
		else {
			$post_excerpt = $attachment_array['caption'];
			}
	  	grp_post::grp_log("newimg_url===".$newimg_url);
	  	grp_post::grp_log("newimg===".$newimg);
	  	global $wpdb;
	    $attachment = array(
	        'guid'           => $newimg_url, 
	        'post_mime_type' => 'image/' . $mime_type,
	        'post_title'     => $attachment_array['title'],
		    'post_excerpt'   => $post_excerpt,
	        'post_content'   => '',
	        'post_status'    => 'inherit'
	    );
	    $attach_id = wp_insert_attachment( $attachment, $newimg, $post_id , true );
	    grp_post::grp_log('---AFTER_ATTACHMENT---');
	    grp_post::grp_log('---LAST_QUERY---');
		grp_post::grp_log($wpdb->last_query);

// Print last SQL query string

	    grp_post::grp_log( print_r( $attach_id, true ) );
	    if(is_wp_error( $attach_id ) ){
	    	grp_post::grp_log('__ATTACHMEN_ERROR__');
	    	grp_post::grp_log( print_r( $attach_id, true ) );
	    }
	    require_once( ABSPATH . 'wp-admin/includes/image.php' );
	    $attach_data = wp_generate_attachment_metadata( $attach_id, $newimg );
	    wp_update_attachment_metadata( $attach_id, $attach_data );
	    update_post_meta( $attach_id, '_wp_attachment_image_alt', wp_slash($attachment_array['alt']) );
	   
	    // Set the image as the featured image
	    //if($auto_image_set_as_featured == 'yes'){
	      
	    //}
	   	
	    if(!is_wp_error( $attach_id ) ){
	    	grp_post::grp_log('add_attachment post_id ='.$post_id .'attachment_id='.$attach_id);
	    	set_post_thumbnail( $post_id, $attach_id );	
	    }else{
	    	grp_post::grp_log('delete_post ='.$post_id);
	    	//wp_delete_post( $post_id );
	    }
	     
	    
	    // Insert the image into the post
	    if($auto_image_insert_into_post == 'yes'){
	        update_post_meta( $post_id, 'add_before_post', $newimg_url );
	    }
	}

	public static function va_hex2rgbcolors( $c ){
		$c = str_replace("#", "", $c);
	    if(strlen($c) == 3){
	        $r = hexdec( $c[0] . $c[1] );
	        $g = hexdec( $c[1] . $c[1] );
	        $b = hexdec( $c[2] . $c[1] );
	        }
	    elseif (strlen($c) == 6 ){
	        $r = hexdec( $c[0] . $c[2] );
	        $g = hexdec( $c[2] . $c[2] );
	        $b = hexdec( $c[4] . $c[2] );
	        }
	    else{
	        $r = 'ff';
	        $g = 'ff';
	        $b = '00';
	        }
	    return array("red" => $r, "green" => $g, "blue" => $b);
	}
}
$vagi = new vaGenerateImage();
?>