<?php
/*
Plugin Name: Sz Comment Filter
Plugin URI: wordpress.org/plugins/sz-comment-filter/
Description: No spam in comments. blocked by Invisible internal token-code with ajax.It blocks spam without using CAPTCHA.
Author: SzMake
Version: 1.1.2
Author URI: http://www.szmake.net/
Text Domain: szm-comment-filter
Domain Path: /languages/
License: GPLv3
*/

define('SZMCF_VERSION', '1.1.2');
define('SZMCF_DOMAIN', 'szm-comment-filter');
define('SZMCF_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define('SZMSF_KEYSEP', '-');


$szmcf_settings = array(
	'version' => SZMCF_VERSION,
	'salt' => '1234567890',
	'allow_trackbacks' => false,	// if type of trackback comment enable,then set true
	'debug' => 0,

);

include('szmcf-admin.php');
include('szmcf-functions.php');

function szmcf_init() {
	 load_plugin_textdomain( SZMCF_DOMAIN, false, basename( dirname( __FILE__ ) ).'/languages' );
}

add_action( 'init', 'szmcf_init' );

// load .js file
function szmcf_enqueue_script() {
	// jquery.form.js originally bundled with WordPress is out of date and deprecated
	// so we need to deregister it and re-register the latest one
	/*
	wp_deregister_script( 'jquery-form' );
	wp_register_script( 'jquery-form',
		szmcf_plugin_url( 'js/jquery.form.min.js' ),
		array( 'jquery' ), '3.51.0-2014.06.20', true );
	*/
	/*
	// fix to do not work jQuery 1.7.1 older
	global $wp_scripts;
	if($wp_scripts){
		$inc_jquery = $wp_scripts->query('jquery');
		if ( version_compare( $inc_jquery->ver ,'1.7.1','<' ) ) {  
			wp_deregister_script('jquery');
			wp_enqueue_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', array(), '1.7.1');
		}	
	}
	*/

	if (is_singular() && comments_open()) { // load script only for pages with comments form
		//wp_enqueue_script('szm-spam-filter', szmcf_plugin_url('js/sz-comment-filter.js'), array('jquery', 'jquery-form'), SZMCF_VERSION, true);
		wp_enqueue_script('szm-spam-filter', szmcf_plugin_url('js/sz-comment-filter.js'), array('jquery'), SZMCF_VERSION, true);
	}
	

}
add_action('wp_enqueue_scripts', 'szmcf_enqueue_script');

// comment form custom.
function szmcf_form_customizer() {
	global $szmcf_settings;
	
	if($szmcf_settings['debug']){
		echo '<p class="szmcf-input" id="szmcf-input-debug">';
	} else {
		echo '<p class="szmcf-input" id="szmcf-input">
	';
	}
	_e('Please enable javascript.', SZMCF_DOMAIN);
	echo '<br>';
	_e('Or you can post by following procedure.', SZMCF_DOMAIN);
	echo '<br>';
	_e('1.Please click on the link [GET TOKEN-CODE],then it is displayed.', SZMCF_DOMAIN);
	echo '<br>';
	_e('2.Enter displayed token-code to "TOKEN INPUT".', SZMCF_DOMAIN);
	echo '<br>';
	echo '
		<label for="get_token">[<a href="'.admin_url( "admin-ajax.php" ).'?action=szmcf_currentkey&t='.time().'" target="szmcf_iframe">'.__('GET TOKEN-CODE', SZMCF_DOMAIN).'</a>]</label>
		<iframe srcdoc="" name="szmcf_iframe" width="100%" height="28px" marginwidth="2" marginheight="2" scrolling="auto" style="border: 2px gray solid;">
		</iframe>
		<label for="szmcf-key">'.__('TOKEN INPUT', SZMCF_DOMAIN).'</label>
		<input type="text" name="szmcf-key" id="szmcf-key" class="szmcf-param" value="" />
	</p>'.PHP_EOL;
	

	if($szmcf_settings['debug']){
		echo '<p class="szmcf-hunnypot" id="szmcf-hunnypot">';
	} else {
		echo '<p class="szmcf-hunnypot" id="szmcf-hunnypot" style="display: none;">';
	}
	echo '
		<label for="szmcf-email-website-url">'.__('Honeypot(Input unnecessary)', SZMCF_DOMAIN).'</label>
		<input type="text" name="szmcf-email-website-url" id="szmcf-email-website-url" class="szmcf-param" value="" />
	</p>'.PHP_EOL;

}
add_action('comment_form_after_fields', 'szmcf_form_customizer'); // add to the comment form
add_action('comment_form_logged_in_after', 'szmcf_form_customizer'); // add to the comment form




// AjaxURL:header seg (ajax url def)
function szmcf_add_my_ajaxurl() {
?>
    <script>
        var szmcf_ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
    </script>
<?php
}
add_action( 'wp_head', 'szmcf_add_my_ajaxurl', 1 );

// AjaxCaller
function szmcf_ajax_currentkey(){
    echo szmcf_keygen();
    die();
}

add_action( 'wp_ajax_szmcf_currentkey', 'szmcf_ajax_currentkey' );
add_action( 'wp_ajax_nopriv_szmcf_currentkey', 'szmcf_ajax_currentkey' );


function szmcf_chk_comment($commentdata) {
	global $szmcf_settings;

	extract($commentdata);

	$result_pre_error_message = '<p><strong><a href="javascript:window.history.back()">'.__('Go back', SZMCF_DOMAIN).'</a></strong>'.__(' and try again.', SZMCF_DOMAIN).'</p>';
	$result_error_message = '';

	$spam_flag = false;
	$spam_rules = '';

	if( ($szmcf_settings['allow_trackbacks']==false) && ($comment_type == 'trackback') ){
		$result_error_message .= __('Error: trackbacks are disabled.' ,SZMCF_DOMAIN).'<br> ';
		$spam_flag = true;
		$spam_rules = 'type trackback';
	
	} else if ( $comment_type != 'pingback' && $comment_type != 'trackback') {

		if ( ! empty($_POST['szmcf-email-website-url'])) {
			$spam_flag = true;
			$spam_rules = 'honeypot[set data]';
			$result_error_message .= __('Error: field should be empty. [set invisible value]', SZMCF_DOMAIN).'<br> '.PHP_EOL;

		} else if( empty($_POST['szmcf-key'])){
			$spam_flag = true;
			$result_error_message .= '<strong>'.__('Comment was blocked because this post is similar to spam-post.', SZMCF_DOMAIN).'</strong><br> ';
			$spam_rules = 'token[empty]';

		} else if ( ! szmcf_keychk($_POST['szmcf-key']) ) {
			$spam_flag = true;
			$result_error_message .= '<strong>'.__('Comment was blocked because this post is similar to spam-post.', SZMCF_DOMAIN).'</strong><br> ';
			$spam_rules = 'token[invalid]';
		}

	}


	if ($spam_flag) { 
		$spam_req = array();
		$spam_req['at_blocked'] = current_time('Y/m/d H:i:s');
		$spam_req['ip'] = $_SERVER['REMOTE_ADDR'];
		$spam_req['rules'] = $spam_rules;
		$spam_req['post_ID'] = $comment_post_ID;

		$post_data_array = array();
		$flg_was_input = false;
		foreach( $_POST as $key=>$value){
			if(in_array($key, array(	 'submit'
										,'comment_post_ID'
										,'comment_parent'
									) )
				){
				continue;
			}
			if(is_string($key) && is_string($value)){
				$reg_fieldname = $key;
				if( 'szmcf-key' == $reg_fieldname ){
					$reg_fieldname = '[anti-spam token]';
				} else if( 'szmcf-email-website-url' == $reg_fieldname ){
					$reg_fieldname = '[anti-spam honeypot]';
				}
				$post_data_array[$reg_fieldname] = $value;	
				
				if(strlen($value)>0){
					$flg_was_input = true;
				}
			}
		}
		$spam_req['post_data_array'] = $post_data_array;		
		
		// if all empty then no logreg...
		if($flg_was_input){
			szmcf_reglog($spam_req);
		}
		
		wp_die( $result_pre_error_message . $result_error_message ); // die
	}
	
	return $commentdata; // this comment does not looks like spam

}
add_filter('preprocess_comment', 'szmcf_chk_comment', 1);
