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
					<?php echo sprintf( __( 'Total %s spam comments were blocked by sz-comment-filter.', SZMCF_DOMAIN), number_format( $blocked_total ) ); ?>
					<?php if($log_itemcnt>0): ?>
					<form method="post" style="padding: 20px 0 5px 0;">
						<input type="hidden" name="szmcf_option_submit" value="9" />
						<input type="submit" class="button" value="<?php _e('spam-counter reset', SZMCF_DOMAIN); ?>" onclick='return confirm("<?php _e('Are you sure you want to reset?', SZMCF_DOMAIN) ?>")' />
					</form>
					<?php endif; ?>
				</p>
				<?php if($log_itemcnt>0): ?>
				<h4><?php _e('Log of blocked spam comment.', SZMCF_DOMAIN); ?> (<?php _e('The latest', SZMCF_DOMAIN); ?> <?php echo $log_itemcnt ?> <?php _e('cases', SZMCF_DOMAIN); ?>)</h4>
				

				<div id="szmcf_loglist">
					<?php
					$logno = 0;
					foreach($log_items as $log_item):
					$logno++;
					?>
					<div class="dathead">
							<div class="dhead1">#<?php echo $logno ?>&nbsp;blocked at <?php echo $log_item['at_blocked'] ?></div>
							<div class="dhead2">[From IP : <?php echo $log_item['ip'] ?>]</div>
							<div class="dhead3">Rules :  <?php echo $log_item['rules'] ?></div>
					</div>
					<div class="ditail">
						<div class="ddtail1">
						<p>
						<?php echo htmlspecialchars($log_item['inp_email']) ?>
						</p>
						<p>
						<?php echo htmlspecialchars($log_item['inp_url']) ?>
						</p>
						<p>
						<?php echo htmlspecialchars($log_item['comment']) ?>
						</p>
						</div>
						<div class="ddtail2">
							<?php
							if ( current_user_can( 'edit_post', $log_item['post_ID'] ) ) {
								$post_link = "<a href='" . get_edit_post_link( $log_item['post_ID'] ) . "'>";
								$post_link .= get_the_title( $log_item['post_ID'] ) . '</a>';
							} else {
								$post_link = get_the_title( $log_item['post_ID'] );
							}
							// ---
							$post_type_object = get_post_type_object( get_post_type( $log_item['post_ID']) );
							?>
							<div class="response-links">
									<?php echo $post_link ?><br>
									<strong>
										<?php
										$_pending_count_temp = get_pending_comments_num( array( $log_item['post_ID'] ) );
										$pending_comments = $_pending_count_temp[$log_item['post_ID']];
										$pending_phrase = sprintf( __( '%s pending' ), number_format( $pending_comments ) );
										?>
										<a href="<?php echo esc_url( add_query_arg( 'p', $log_item['post_ID'], admin_url( 'edit-comments.php' ) ) ) ?>" title="<?php echo esc_attr( $pending_phrase )  ?>" class="post-com-count">
											<span class="comment-count"><?php echo number_format_i18n( get_comments_number($log_item['post_ID']) ) ?></span></a>
									</strong>
									<a href="<?php echo get_permalink( $log_item['post_ID'] ) ?>"><?php echo $post_type_object->labels->view_item ?></a>
							</div>
						</div>
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
	if ($pagenow == 'edit-comments.php'):
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
			<input type="hidden" name="szmcf_option_submit" value="1" />
			<label>
				<input name="szmcf_info_visibility" type="checkbox" value="1" <?php echo $checked; ?> />
				<?php _e('reports of blocked spam-post.', SZMCF_DOMAIN); ?>
			</label>
			<input type="submit" class="button" value="<?php _e('Apply'); ?>" />
		</form>
		<?php
	endif; // end of if($pagenow == 'edit-comments.php')
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
