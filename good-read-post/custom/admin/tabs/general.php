<tr>
	<th><label for="grp_tag"><?php echo __('GoodReads Tag','grp'); ?></label></th>
	<td>
		<input type="text" name="grp_tag" value="<?php if($settings['grp_tag']) echo $settings['grp_tag']; ?>">
		<span class="description"><?php echo __('Add your goodread tag, which we are used in collect the data','grp'); ?></span>
	</td>
</tr>

<tr>
	<th><label for="ilc_tag_class">Tags with CSS classes:</label></th>
	<td>
		<input id="ilc_tag_class" name="ilc_tag_class" type="checkbox" <?php if ( $settings["ilc_tag_class"] ) echo 'checked="checked"'; ?> value="true" /> 
		<span class="description">Output each post tag with a specific CSS class using its slug.</span>
	</td>
</tr>
