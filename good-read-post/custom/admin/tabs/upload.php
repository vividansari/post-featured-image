<?php
?>
<div class="grp_upload_tags">
	<input type="file" name="tag_csv" class="tag_csv">
	<button type="button" name="tag_csv_button" class="tag_csv_btn">Upload csv</button>
</div>
<script type="text/javascript" charset="utf-8" >
	jQuery(document).ready(function($){
		jQuery('.tag_csv_btn').click(function(){
			var grp_tags_csv = jQuery('#grp_form_id').serialize();
			var tmp_tags_formdata = new FormData();
			var tmp_tags_formdata = new FormData($('#grp_form_id')[0]);

			tmp_tags_formdata.append('file',$('input[type=file]').files);
			tmp_tags_formdata.append('action', 'get_grp_tag_csv');
			tmp_tags_formdata.append('grp_form', grp_tags_csv);
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			// var file = jQuery('.tag_csv').val();
			// console.log(file);
			console.log(ajax_url);
			jQuery.ajax({
				url: ajax_url,
				type: 'POST',
				data: tmp_tags_formdata,
				processData: false,
				contentType: false,
				success: function(response)
				{

				}
			});
		});
	});

</script>
