<div class="grp_upload_author">
	<tr>
		<th>Upload author csv file</th>
		<td>
			<input type="file" name="author_csv" class="author_csv">
		</td>
	</tr>
	<tr>
		<td>
			<button type="button" name="author_csv_button" class="button-primary author_csv_btn">Upload Author csv</button>
		</td>
	</tr>
</div>
<script type="text/javascript" charset="utf-8" async defer>
	jQuery(document).ready(function($){
		jQuery('.author_csv_btn').click(function(){
			var grp_author = jQuery('#grp_form_id').serialize();
			var tmp_author_formdata = new FormData();
			var tmp_author_formdata = new FormData($('#grp_form_id')[0]);

			tmp_author_formdata.append('file',$('input[type=file]').files);
			tmp_author_formdata.append('action', 'get_grp_author_csv');
			tmp_author_formdata.append('grp_form', grp_author);
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			// var file = jQuery('.tag_csv').val();
			// console.log(file);
			// console.log(ajax_url);
			jQuery.ajax({
				url: ajax_url,
				type: 'POST',
				data: tmp_author_formdata,
				processData: false,
				contentType: false,
				success: function(response)
				{
					document.location.reload();
				}
			});
		});
	});
</script>