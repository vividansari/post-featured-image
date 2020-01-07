<tr>
	<th><label for="grp_tag"><?php echo __('GoodReads Tag','grp'); ?></label></th>
	<td>
		<input type="text" name="grp_tag" value="<?php if($settings['grp_tag']) echo $settings['grp_tag']; ?>">
		<span class="description"><?php echo __('Add your goodread tag, which we are used in collect the data','grp'); ?></span>
	</td>
</tr>


<tr>
	<th><label for="grp_tag"><?php echo __('GoodReads Tag Total Page','grp'); ?></label></th>
	<td>
		<input type="number" name="grp_tag_total_page" value="<?php if($settings['grp_tag_total_page']) echo $settings['grp_tag_total_page']; ?>">
		<span class="description"><?php echo __('Add your goodread tag total page, which we are used in collect the data','grp'); ?></span>
		<div class="description" style="display: none;">
			<?php
			$current_cron_page = get_option('grp_cron_current_page');
			echo "Current Cron page = $current_cron_page";
			$cron_completed = get_option('grp_cron_completed');
			echo "<br/>Cron Completed = $cron_completed";
			$grp_done_tag = get_option('grp_done_tag');
			
			echo '<pre>';
			print_r($grp_done_tag);
			echo '</pre>';
			
			?>
		</div>
	</td>
</tr>



