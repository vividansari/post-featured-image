<?php
	$author_arr = get_option( 'grp_author_data' );
	echo '<pre>';
	print_r($author_arr);
	echo '</pre>';
?>
<div class="grp_cron_details">
	<a href="javascript:void(0);" class="button-primary grp_get_author_btn"><?php echo __('Get Author','grp'); ?></a>

</div>
<div class="grp_log"></div>
<div class="current_tag_author">
	<div>
		<label><strong>GET CURRENT TAG:</strong></label>
		<?php
		$current_tag = get_option('grp_cron_tag_current_value');
		echo "<pre>";
		print_r($current_tag);
		echo "</pre>";
		?>
	</div>
	<div>
		<label><strong>GET CURRENT AUTHOR:</strong></label>
		<?php
		$current_author = get_option('grp_cron_author_current_value');
		echo "<pre>";
		print_r($current_author);
		echo "</pre>";
		?>
	</div>
</div>
<script type="text/javascript">

	jQuery(document).ready(function(){
		jQuery('.grp_get_author_btn').click(function(){
			get_grp_author_data(1,100);
		});

		function get_grp_author_data($pas,$lastpos = ''){
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';

			jQuery.ajax({
				url : ajax_url,
				type: 'POST',
				dataType : 'json',
				data: {
                    action : 'get_gr_author_list',
                    startpos: $pas,
                    lastpos : $lastpos
                },
                success: function( response ) {

                    if ( response.success ) {
                    	var newpos = response.data.pos;
                    	var lastpos = response.data.lastpos;
                    	var message = response.data.message;
                    	console.log(message);
                    	if( newpos <= lastpos ){
                    		jQuery('.grp_log').prepend(message +'</br>' );
                    		get_grp_author_data( newpos, lastpos );
                    	}else{
                    		jQuery('.grp_log').prepend(message +'</br>' );
                    	}

                    }else{
                    	alert('Something wrong..!!');
                    }
                }
			});
		}
	});

</script>