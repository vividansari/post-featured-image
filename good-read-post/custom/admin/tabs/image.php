<tr>
	<th><label for="pixabay"><?php echo __('Pixabay API', 'grp' ); ?></label></th>
	<td>
		<input type="text" name="grp_pixabay_api" value="<?php if($settings['grp_pixabay_api'] ) echo $settings['grp_pixabay_api']; ?>">
		<div class="description"><?php
			$api_doc = "https://pixabay.com/api/docs/";
			$api_url = "https://pixabay.com/en/accounts/register/";
			echo sprintf( __('Please add your pixbay api, Pixabay API Key (<a href="%1$s">get yours here</a>. You may have to <a href="%2$s">create an account</a> first'),$api_doc,$api_url);
		 ?>
	</td>
</tr>

<tr>
	<th><label for="grp_copyright_text"><?php echo __('Copyright Text','grp'); ?></label></th>
	<td>
		<input type="text" name="grp_copyright_text" value="<?php if($settings['grp_copyright_text']) echo $settings['grp_copyright_text']; ?>">
		<div class="description"><?php echo __('Add your copyright text here it will be display in all images.','grp'); ?></div>
	</td>
</tr>
