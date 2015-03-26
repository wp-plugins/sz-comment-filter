<?php

function szmcf_admin_enqueue_scripts( $hook_suffix ) {
	global $szmcf_settings;

	$post_pages = array('edit-comments.php', 'post-new.php');
	if(in_array($hook_suffix, $post_pages)){
		wp_enqueue_style(	SZMCF_DOMAIN,
							szmcf_plugin_url('css/styles-admin.css'),
							false,
							$szmcf_settings['version'],
							'all'
							);
		wp_enqueue_script(	SZMCF_DOMAIN,
							szmcf_plugin_url('js/scripts-admin.js'),
							array('prototype'),
							$szmcf_settings['version'],
							false
							);
	}



}
add_action( 'admin_enqueue_scripts', 'szmcf_admin_enqueue_scripts' );

function szmcf_admin_notice() {
	global $pagenow;

	if($pagenow == 'edit-comments.php'){
		$user_id = get_current_user_id();
		$szmcf_info_visibility = get_user_meta($user_id, 'szmcf_info_visibility', true);
		if ($szmcf_info_visibility == 1 OR $szmcf_info_visibility == ''){
			$blocked_total = szmcf_get_logcount();
			$log_items = szmcf_get_loglist();
			$log_itemcnt = 10;
			if($blocked_total<10){
				$log_itemcnt = $blocked_total;
			}
			
			?>

			<div class="update-nag szmcf-panel-info">
				<p style="margin: 0;">
					<div style="overflow: hidden;">
					<div style="float: left;">
					<h4>Sz Comment Filter Report</h4>
					</div>
					<div style="float: right;">
					<p><a href="http://wp.szmake.net/donate/" class="button button-primary" target="_blank"><?php echo esc_html( __( 'Donate', SZMCF_DOMAIN ) ) ?></a></p>
					</div>
					<div style="float: right; margin: 0px 8px;">
					<img src='<?php echo szmcf_plugin_url( 'images/blkfrogman.png' ) ?>' width='48' height='48' align='top'>
					</div>
					</div>
					<?php echo sprintf( __( 'Total %s spam comments were blocked.', SZMCF_DOMAIN), number_format( $blocked_total ) ); ?>
					<?php if($log_itemcnt>0): ?>
					<form method="post" style="padding: 20px 0 5px 0;">
						<input type="hidden" name="szmcf_option_submit" value="9" />
						<input type="submit" class="button" value="<?php _e('Blocked Count Reset', SZMCF_DOMAIN); ?>" onclick='return confirm("<?php _e('Are you sure you want to reset?', SZMCF_DOMAIN) ?>")' />
					</form>
					<?php endif; ?>
				</p>
				<?php if($log_itemcnt>0): ?>
				[<?php _e('Log of blocked spam comment.', SZMCF_DOMAIN); ?> : <?php _e('The latest', SZMCF_DOMAIN); ?> <?php echo $log_itemcnt ?> <?php _e('cases', SZMCF_DOMAIN); ?>]<br>
				
				<div id="szmcf_loglist">
					<?php
					$logno = 0;
					foreach($log_items as $log_item):
					$logno++;
					?>
					<div class="dathead">
							<div class="dhead1">#<?php echo $logno ?>&nbsp;blocked at <?php echo $log_item['at_blocked'] ?></div>
							<div class="dhead2">[From IP : <?php echo $log_item['ip'] ?>]</div>
							<div class="dhead3">[<a href="<?php echo get_permalink( $log_item['post_ID'] ) ?>">Post ID=<?php echo $log_item['post_ID'] ?></a>]</div>
							<div class="dhead4">Rules :  <?php echo $log_item['rules'] ?></div>
					</div>
					<div class="ditail">
						<table>
							<tr>
								<th class="field_name">field name</th>
								<th style='text-align: center'>input data</th>
							</tr>
							<?php 
							foreach( $log_item['post_data_array'] as $key=>$value){
							?>
								<tr>
									<td class="post_key"><?php echo htmlspecialchars($key) ?></td>
									<td class="post_value"><?php echo htmlspecialchars($value) ?></td>
								</tr>
							<?php } ?>
						</table>
					</div>
					<?php
					endforeach;
					?>
				</div>​


				<?php endif; ?>
			</div>
			<?php
		}
	}
}
add_action('admin_notices', 'szmcf_admin_notice');


function szmcf_display_screen_option() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php'){
		$user_id = get_current_user_id();
		$szmcf_info_visibility = get_user_meta($user_id, 'szmcf_info_visibility', true);

		if ($szmcf_info_visibility == 1 OR $szmcf_info_visibility == '') {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		?>
		<script>
			jQuery(function($){
				$('.szmcf_screen_options_group').insertAfter('#screen-options-wrap #adv-settings');
			});
		</script>
		<form method="post" class="szmcf_screen_options_group" style="padding: 20px 0 5px 0;">
			<h5>Sz Comment Filter</h5>
			<input type="hidden" name="szmcf_option_submit" value="1" />
			<label>
				<input name="szmcf_info_visibility" type="checkbox" value="1" <?php echo $checked; ?> />
				<?php _e('reports of blocked spam-post.', SZMCF_DOMAIN); ?>
			</label>
			<input type="submit" class="button" value="<?php _e('Apply'); ?>" />
		</form>
		<?php
	}
}


function szmcf_register_screen_option() {
	add_filter('screen_layout_columns', 'szmcf_display_screen_option');
}
add_action('admin_head', 'szmcf_register_screen_option');


function szmcf_update_screen_option() {

	if (isset($_POST['szmcf_option_submit'])){
		if ( $_POST['szmcf_option_submit'] == 1) {
			$user_id = get_current_user_id();
			if (isset($_POST['szmcf_info_visibility']) AND $_POST['szmcf_info_visibility'] == 1) {
				update_user_meta($user_id, 'szmcf_info_visibility', 1);
			} else {
				update_user_meta($user_id, 'szmcf_info_visibility', 0);
			}
		} else if($_POST['szmcf_option_submit'] == 9){
			// do log zero reset.
			szmcf_clear_logdata();
		}
	}
	
}
add_action('admin_init', 'szmcf_update_screen_option');
