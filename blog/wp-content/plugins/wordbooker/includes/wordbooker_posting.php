<?php
/**
Extension Name: Wordbooker Posting Functions
Extension URI: http://wordbooker.tty.org.uk
Version: 2.1
Description: Collection of functions concerning posting to the various parts of Facebook.
Author: Steve Atty
*/

/*
 * Copyright 2011 Steve Atty (email : posty@tty.org.uk)
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function wordbooker_wall_post($post_id,$access_token,$post_title,$post_data,$target_id,$dummy,$target_name){
	if (isset($dummy)) { 	
		wordbooker_debugger("Wall Post to ".$target_name." Test Only",'No Post Made',$post_id,90) ;
		return;
	}
	$post_data['access_token']=$access_token;
	global $user_ID;
try {
		$result = wordbooker_fb_stream_pubish($post_data,$target_id);
		wordbooker_store_post_result($post_id,$result->id );
		wordbooker_debugger("Wall Post to ".$target_name." Succeeded - result : ",$result->id,$post_id,90) ;
	    }
	catch (Exception $e) {
		$error_code = $e->getCode();
		$error_msg = $e->getMessage();
		wordbooker_append_to_errorlogs($method, $error_code, $error_msg,$post_id,$user_ID);
		wordbooker_debugger("Wall Post to ".$target_name." Failed : ",$error_msg,$post_id,99) ;
	}
}

function wordbooker_link_post($post_id,$access_token,$post_title,$post_data,$target_id,$dummy,$target_name){
	if (isset($dummy)) { 	
		wordbooker_debugger("Link Post to ".$target_name." Test Only",'No Post Made',$post_id,90) ;
		return;
	}
	$post_data2['message']=$post_data['message'];
	$post_data2['link']=$post_data['link'];
	$post_data2['access_token']=$access_token;
	global $user_ID;
try {
		$result = wordbooker_fb_link_publish($post_data2,$target_id);
		wordbooker_store_post_result($post_id,$result->id );
		wordbooker_debugger("Link Post to ".$target_name." Succeeded - result : ",$result->id,$post_id,90) ;
	    }
	catch (Exception $e) {
		$error_code = $e->getCode();
		$error_msg = $e->getMessage();
		wordbooker_append_to_errorlogs($method, $error_code, $error_msg,$post_id,$user_ID);
		wordbooker_debugger("Link Post to ".$target_name." Failed : ",$error_msg,$post_id,99) ;
	}
}
function wordbooker_status_update($post_id,$access_token,$post_date,$target_id,$dummy,$target_name) {
	global $wordbooker_post_options,$user_ID;
	wordbooker_debugger("Setting status_text".$wordbooker_post_options['wordbooker_status_update_text']," ",$post_id) ; 
	if (isset($dummy)) { 	
		wordbooker_debugger("Status update to ".$target_name." Test Only",'No Post Made',$post_id,90) ;
		return;
	}
		
	$status_text = parse_wordbooker_attributes(stripslashes($wordbooker_post_options['wordbooker_status_update_text']),$post_id,strtotime($post_date)); 
	$status_text = wordbooker_post_excerpt($status_text,420); 		
	$data=array( 'access_token'=>$access_token,'message' =>$status_text);
	try {
		$result = wordbooker_fb_status_update($data,$target_id);
		wordbooker_store_post_result($post_id,$result->id );
		wordbooker_debugger("Status update  to ".$target_name." suceeded result : ",$result->id,$post_id,90) ;
	    }
	catch (Exception $e) {
		$error_code = $e->getCode();
		$error_msg = $e->getMessage();
		wordbooker_append_to_errorlogs($method, $error_code, $error_msg,$post_id,$user_ID);
		wordbooker_debugger("Status Update  to ".$target_name." failed : ".$error_msg,$post_id,99) ;
	}
}

function wordbooker_notes_post($post_id,$access_token,$post_title,$target_id,$dummy,$target_name){
	if (isset($dummy)) { 	
		wordbooker_debugger("Notes publish  to ".$target_name." Test Only",'No Post Made',$post_id,90) ;
		return;
	}
	global $post,$user_ID;
	$data=array(
		'access_token'=>$access_token,
		'message' => preg_replace("/<script.*?>.*?<\/script>/xmsi","",apply_filters('the_content', $post->post_content)),
		'subject' =>$post_title
	);
	try {
		$result = wordbooker_fb_note_publish($data,$target_id);
		wordbooker_store_post_result($post_id,$result->id);
		wordbooker_debugger("Note Publish to ".$target_name." result : ",$result->id,$post_id,90) ;
	} 	
	catch (Exception $e) {
		$error_code = $e->getCode();
		$error_msg = $e->getMessage();
		wordbooker_append_to_errorlogs($method, $error_code, $error_msg,$post_id,$user_ID);
		wordbooker_debugger("Notes publish  to ".$target_name." fail : ".$error_msg,$error_code,$post_id,99) ;
	}
}


function wordbooker_store_post_result($post_id,$fb_post_id) {
	global $wpdb,$blog_id,$user_ID;
	$tstamp=time();
	$wordbooker_settings = wordbooker_options();
	$sql=	' INSERT INTO ' . WORDBOOKER_POSTCOMMENTS . ' (fb_post_id,comment_timestamp,wp_post_id,blog_id,user_id) VALUES ("'.$fb_post_id.'",'.$tstamp.','.$post_id.','.$blog_id.','.$user_ID.')';
	$result = $wpdb->query($sql);
	wordbooker_insert_into_postlogs($post_id,$blog_id);
	# Clear down the diagnostics for this post if the user has chosen so
	if (isset($wordbooker_settings['wb_wordbooker_diag_clear'])){
	$result = $wpdb->query(' DELETE FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE   blog_id ='.$blog_id.' and post_id='.$post_id.' and (error_message not like "(%_%)" and method not like "% - result")'); }
	# Now Change the publish flag for this post to mark it as published.
	$wb_params=get_post_meta($post_id, '_wordbooker_options', true); 
	$wb_params["wordbooker_publish_default"]='published';
	update_post_meta($post_id, '_wordbooker_options', $wb_params); 

}

?>
