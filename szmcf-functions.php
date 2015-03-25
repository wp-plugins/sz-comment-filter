<?php

function szmcf_plugin_url( $path = '' ) {
	$url = SZMCF_PLUGIN_URL;

	if ( ! empty( $path ) && is_string( $path ) && false === strpos( $path, '..' ) )
		$url .= '/' . ltrim( $path, '/' );

	return $url;
}

// key generate
function szmcf_keygen(){
	
    $ret = '';
    $ipoct_arr = explode('.',$_SERVER['REMOTE_ADDR']);
    // a-seg
    $value_a=szmcf_make_seg_a();

    // b-seg
    $value_b=substr(time(), -5);
    
    // c-seg
    $value_c=szmcf_make_seg_c($value_a, $value_b);

    $ret = $value_a.SZMSF_KEYSEP.$value_b.SZMSF_KEYSEP.$value_c;

    return $ret;

}

// make key of segment a 
function szmcf_make_seg_a(){
	$ipoct_arr = explode('.',$_SERVER['REMOTE_ADDR']);
    // a-seg
    $value_a=0;
    foreach($ipoct_arr as $ipoct){
    	$value_a+=intval($ipoct);
    }
    return $value_a;
}

// make key of segment c 
function szmcf_make_seg_c($value_a, $value_b){
	global $szmcf_settings;
	return substr( md5($value_a+$value_b+$szmcf_settings['allow_trackbacks']), -3);
}

// key check
function szmcf_keychk($chk_key){
	$ret = false;
	if( ! empty($chk_key) ){
		$arr = explode(SZMSF_KEYSEP, trim($chk_key));
		if(count($arr)==3){
			$req_a=$arr[0];
			$req_b=$arr[1];
			$req_c=$arr[2];
			if( $req_c==szmcf_make_seg_c($req_a, $req_b) ){
				if($req_a==szmcf_make_seg_a()){
					$keytm = time();
					$keytm = intval( substr($keytm, 0, (-1)*strlen($req_b) ).$req_b );
					if( ( $keytm >= time() - 180 ) // before gen 180 sec
						 &&
						( $keytm <= time() )
					  ){
						$ret = true;
					}
				}
			}
		}
	}
    return $ret;
}

// regist log.
function szmcf_reglog($spam_req) {
	$szmcf_data = get_option('szmcf_data', array());
	
	// logdata set
	if (array_key_exists('next_log_idx', $szmcf_data)){
		$next_idx = intval($szmcf_data['next_log_idx']);
	} else {
		$next_idx = 0;
	}
	$szmcf_data['logdat_'.strval($next_idx)] = serialize($spam_req);
	
	// next idx set
	$next_idx++;
	if($next_idx>9){
		$next_idx=0;
	}
	$szmcf_data['next_log_idx'] = $next_idx;

	// count set
	if (array_key_exists('blocked_count', $szmcf_data)){
		$szmcf_data['blocked_count']++;
	} else {
		$szmcf_data['blocked_count'] = 1;
	}
	// update
	update_option('szmcf_data', $szmcf_data);
}

function szmcf_get_logcount() {
	$szmcf_data = get_option('szmcf_data', array());
	if ( array_key_exists('blocked_count', $szmcf_data) ){
		$blocked_count = $szmcf_data['blocked_count'];
	} else {
		$blocked_count = 0;
	}
	return $blocked_count;
}

function szmcf_get_loglist() {
	$szmcf_data = get_option('szmcf_data', array());
	
	$ret_array = array();

	if (	( ! array_key_exists('next_log_idx', $szmcf_data) )
			||
			( ! array_key_exists('blocked_count', $szmcf_data) )
			){
		return $ret_array;
	}
	$blkcnt = intval($szmcf_data['blocked_count']);
	$nxlidx = intval($szmcf_data['next_log_idx']);


	$idx=0;
	if( 10 > $blkcnt  ){
		for($datidx=$blkcnt-1;$datidx>=0;$datidx--){
			$keyname = 'logdat_'.strval($datidx);
			if( array_key_exists($keyname, $szmcf_data) ){
				$ret_array[$idx]=unserialize($szmcf_data[$keyname]);
				$idx++;
			}
		}
	} else {
		$datidx = $nxlidx;
		do {
			$datidx--;
			if($datidx<0){
				$datidx=9;
			}

			$keyname = 'logdat_'.strval($datidx);
			if( array_key_exists($keyname, $szmcf_data) ){
				$ret_array[$idx]=unserialize($szmcf_data[$keyname]);
				$idx++;
			}
			
		} while($datidx!=$nxlidx);
	}

	return $ret_array;
	
}

function szmcf_clear_logdata() {
	$szmcf_data = get_option('szmcf_data', array());
	
	$szmcf_data['next_log_idx']=0;
	$szmcf_data['blocked_count']=0;
	
	for($datidx=0;$datidx<=9;$datidx++){
		$keyname = 'logdat_'.strval($datidx);
		if( array_key_exists($keyname, $szmcf_data) ){
			unset($szmcf_data[$keyname]);
		}
	}

	// update
	update_option('szmcf_data', $szmcf_data);

}
