<tr>
	<th><label for="grp_text_color"><?php echo __('Text Color','grp'); ?></label></th>
	<td>
		<input type="text" class="grp-color-field" name="grp_text_color" value="<?php if($settings['grp_text_color']) echo $settings['grp_text_color']; ?>">
		<div class="description"><?php echo __('set text color on image.','grp'); ?></div>
	</td>
</tr>

<tr>
	<th><label for="grp_text_shadow_color"><?php echo __('Text Shadow Color','grp'); ?></label></th>
	<td>
		<input type="text" class="grp-color-field" name="grp_text_shadow_color" value="<?php if($settings['grp_text_shadow_color']) echo $settings['grp_text_shadow_color']; ?>">
		<div class="description"><?php echo __('set text shadow color on image.','grp'); ?></div>
	</td>
</tr>

<tr>
	<th><label for="grp_copy_right_text_color"><?php echo __('Copyright Text Color','grp'); ?></label></th>
	<td>
		<input type="text" class="grp-color-field" name="grp_copy_right_text_color" value="<?php if($settings['grp_copy_right_text_color']) echo $settings['grp_copy_right_text_color']; ?>">
		<div class="description"><?php echo __('set copyright text color on image.','grp'); ?></div>
	</td>
</tr>

<tr>
	<th><label for="grp_text_size"><?php echo __('Text Size','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_text_size" value="<?php if($settings['grp_text_size']) echo $settings['grp_text_size']; ?>">
	</td>
</tr>

<tr>
	<th><label for="grp_copyright_text_size"><?php echo __('Copyright Text Size','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_copyright_text_size" value="<?php if($settings['grp_copyright_text_size']) echo $settings['grp_copyright_text_size']; ?>">
	</td>
</tr>

<tr>
	<th><label for="grp_x_position"><?php echo __('Horizontal Text Position:','grp'); ?></label></th>
	<td>
		<?php
			$x_position_arr = array(
				'left' => __('Left','grp'),
				'center' => __('Center','grp'),
				'right' => __('Right','grp'),
			);
		?>
		<select name="grp_x_position">
			<option value=""><?php echo __('Please select horizontak text position','grp'); ?></option>
			<?php 
				foreach($x_position_arr as $x_key => $x_val){
					?>
					<option value="<?php echo $x_key; ?>" <?php selected( $settings['grp_x_position'], $x_key ); ?> ><?php echo $x_val; ?></option>
					<?php
				}
			?>
		</select>
	</td>
</tr>


<tr>
	<th><label for="grp_y_position"><?php echo __('Vertical Text Position:','grp'); ?></label></th>
	<td>
		<?php
			$y_position_arr = array(
				'top' => __('Top','grp'),
				'center' => __('Center','grp'),
				'bottom' => __('Bottom','grp'),
			);
		?>
		<select name="grp_y_position">
			<option value=""><?php echo __('Please select vertical text position','grp'); ?></option>
			<?php 
				foreach($y_position_arr as $y_key => $y_val){
					?>
					<option value="<?php echo $y_key; ?>" <?php selected( $settings['grp_y_position'], $y_key ); ?> ><?php echo $y_val; ?></option>
					<?php
				}
			?>
		</select>
	</td>
</tr>


<tr>
	<th><label for="grp_text_transform"><?php echo __('Text Transform:','grp'); ?></label></th>
	<td>
		<?php
			$y_position_arr = array(
				'none' => __('none','grp'),
				'uppercase' => __('Uppercase','grp'),
				'lowercase' => __('Lowercase','grp'),
				'capitalize' => __('Capitalize','grp'),
			);
		?>
		<select name="grp_text_transform">
			<option value=""><?php echo __('Please select vertical text position','grp'); ?></option>
			<?php 
				foreach($y_position_arr as $y_key => $y_val){
					?>
					<option value="<?php echo $y_key; ?>" <?php selected( $settings['grp_text_transform'], $y_key ); ?> ><?php echo $y_val; ?></option>
					<?php
				}
			?>
		</select>
	</td>
</tr>

<tr>
	<th><label for="grp_padding_top"><?php echo __('Text Padding top','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_padding_top" value="<?php if($settings['grp_padding_top']) echo $settings['grp_padding_top']; ?>">
	</td>
</tr>

<tr>
	<th><label for="grp_padding_bottom"><?php echo __('Text Padding Bottom','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_padding_bottom" value="<?php if($settings['grp_padding_bottom']) echo $settings['grp_padding_bottom']; ?>">
	</td>
</tr>

<tr>
	<th><label for="grp_padding_left"><?php echo __('Text Padding Left','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_padding_left" value="<?php if($settings['grp_padding_left']) echo $settings['grp_padding_left']; ?>">
	</td>
</tr>

<tr>
	<th><label for="grp_padding_right"><?php echo __('Text Padding Right','grp'); ?></label></th>
	<td>
		<input type="number" class="" name="grp_padding_right" value="<?php if($settings['grp_padding_right']) echo $settings['grp_padding_right']; ?>">
	</td>
</tr>