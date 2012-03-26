<?php

/*
Description: Collection of functions related to comment handling
Author: Stephen Atty
Author URI: http://wordbooker.tty.org.uk
Version: 2.1.1
*/

function wordbooker_poll_comments($userid=0) {
	wordbooker_debugger("Comment handling starting "," ",-2,9) ; 
	global  $wpdb, $user_ID,$table_prefix,$blog_id,$comment_user;	
	$wordbooker_settings=get_option('wordbooker_settings'); 
		$result = $wpdb->query('DELETE FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL '.( $wordbooker_settings['wordbooker_comment_cron'] * 3 ) .' MINUTE)  and blog_id ='.$blog_id.' and post_id=-2');
	if (! $wordbooker_settings['wordbooker_comment_handling']) {
		wordbooker_debugger("Comment handling disabled "," ",-2,9) ;
		return;
	 }
	$sql="Select user_ID,name from ".WORDBOOKER_USERSTATUS." where blog_id=".$blog_id;
	$processed_time=time();
	if ($userid>0) { $sql.=" and user_ID=".$userid;}
	$rows = $wpdb->get_results($sql);
	foreach ($rows as $comment_row) {
	$comment_user=$comment_row->user_ID; 
	wordbooker_debugger("Processing comments for ".$comment_row->name," ",-2,9) ;
	wordbooker_debugger("Processing your comments "," ",-3,9) ;	
	if (!isset($wordbooker_settings['wordbooker_comment_pull']) ) {	
		wordbooker_debugger("Starting Incoming comment handling"," ",-2,9);
		$incoming=wordbooker_get_comments_from_facebook($comment_row->user_ID);
		wordbooker_debugger("Incoming comment handling completed"," ",-2,9);
	 }	
	else {wordbooker_debugger("Incoming comment handling disabled "," ",-2,9) ; }
	$outgoing=0;
	if (!isset($wordbooker_settings['wordbooker_comment_push']) ) {	
	wordbooker_debugger("Starting Outgoing comment handling"," ",-2,9);
	$outgoing=wordbooker_post_comments_to_facebook($comment_row->user_ID);
	wordbooker_debugger("Outgoing comment handling completed"," ",-2,9);
	}	
	else {wordbooker_debugger("Outgoing comment handling disabled "," ",-2,9) ; }
	wordbooker_debugger("Completed comment processing for ".$comment_row->name," In : ".$incoming." - Out : ".$outgoing,-2,9) ; 
	wordbooker_debugger("Completed your comment processing "," In : ".$incoming." - Out : ".$outgoing,-3,9) ; 
	$sql="insert into ".WORDBOOKER_POSTCOMMENTS." (user_id,blog_id,comment_timestamp,fb_post_id,wp_post_id,in_out) values (".$userid.",".$blog_id.",'".$processed_time."','".$incoming."','".$outgoing."','stat')";
	$wpdb->query($sql);
	}	
	wordbooker_debugger("Comment handling completed "," ",-2,9) ; 
}

function wordbooker_post_comments_to_facebook($user_id) {
	global  $wpdb, $user_ID,$table_prefix,$blog_id,$comment_user;	
	$processed_posts=0;
	$close_comments=get_option('close_comments_for_old_posts');
	$close_days_old=get_option('close_comments_days_old');
	$wbuser = wordbooker_get_userdata($user_id);
	if (strlen($wbuser->access_token)<20) {
	wordbooker_debugger("No user session for comment handling "," ",-2,9) ;
	return 0; }
	$wordbooker_settings=wordbooker_options();
	$comment_structure=$wordbooker_settings['wordbooker_comment_post_format'];
	$comment_tag=$wordbooker_settings['wordbooker_comment_attribute'];
	wordbooker_debugger("Auto close comments ".$close_comments,$close_days_old,-2,98);
	$sql="select distinct wp_post_id,fb_post_id from ".WORDBOOKER_POSTCOMMENTS." where fb_comment_id is null and blog_id=".$blog_id." and user_id=".$user_id." and in_out is null";
	if ($close_comments==1) {$sql.=" and comment_timestamp  > DATE_SUB( CURDATE( ) , INTERVAL ".$close_days_old." DAY )";}
	$rows = $wpdb->get_results($sql);

	wordbooker_debugger("Blog posts for comment handling : ".$sql,count($rows),-2,98);
	foreach($rows as $row) {
	wordbooker_debugger("Starting comment handling for WP post ".$row->wp_post_id,$row->fb_post_id,-2,9);
	wordbooker_debugger("Starting comment handling for WP post ".$row->wp_post_id,$row->fb_post_id,-3,9);
	$wordbooker_post_options = get_post_meta($row->wp_post_id, '_wordbooker_options', true);
	if (!isset($wordbooker_post_options['wordbooker_comment_put'])) {
		wordbooker_debugger("Outgoing comment disabled for WP post ".$row->wp_post_id,$row->fb_post_id,-2,9);
		wordbooker_debugger("Outgoing comment disabled for WP post ".$row->wp_post_id,$row->fb_post_id,-3,9);	
		continue ;
	}
	$sql="select comment_ID from ".$wpdb->comments." where comment_post_id=".$row->wp_post_id." and comment_approved=1 and comment_id not in (select wp_comment_id from ".WORDBOOKER_POSTCOMMENTS." where  wp_post_id=".$row->wp_post_id." and fb_post_id='".$row->fb_post_id."' and user_id=".$user_id.") and comment_post_id in (select ID from ".$wpdb->posts." WHERE comment_status='open')";
	if ($close_comments==1) { $sql.="and comment_post_id in (select ID from ".$wpdb->posts." WHERE post_date > DATE_SUB( CURDATE( ) , INTERVAL ".$close_days_old." 
DAY ))";}
		$results = $wpdb->get_results($sql);
		wordbooker_debugger("Comments for processing : ".$sql,count($results),-2,98);
		foreach($results as $result){			
			$x=0;
			$comment_content=parse_wordbooker_comment_attributes($result->comment_ID,$comment_structure,$comment_tag);
			try {
				$x=wordbooker_fb_put_comments($row->fb_post_id,$comment_content,$wbuser->access_token);
			}
			catch (Exception $e) 
			{
				$error_msg = $e->getMessage();
				$err_no=(integer) substr($error_msg,2,3);
				wordbooker_debugger("Failed to post comment to Facebook : ".$error_msg,$row->fb_post_id,-2,9);
				wordbooker_debugger("Failed to post comment to Facebook : ".$error_msg,$row->fb_post_id,-3,9);
				if ($err_no=100) {
					$sql="delete from  ".WORDBOOKER_POSTCOMMENTS." where fb_post_id='".$row->fb_post_id."'";
					$wpdb->query($sql);
				}
			}
			if (strlen($x->id)>2){
			$sql="insert into ".WORDBOOKER_POSTCOMMENTS." (wp_post_id,fb_post_id,wp_comment_id,fb_comment_id,user_id,blog_id,comment_timestamp,in_out) values (".$row->wp_post_id.",'".$row->fb_post_id."','".$result->comment_ID."','".$x->id."',".$user_id.",".$blog_id.",'".strtotime($result->comment_date)."','out')";
			$wpdb->query($sql);
			wordbooker_debugger("Posting comment to Facebook Post : ".$row->fb_post_id." returns",$x->id,-2,9) ;
			wordbooker_debugger("Posting comment to Facebook Post : ".$row->fb_post_id." returns",$x->id,-3,9) ;
			$processed_posts=$processed_posts+1;
			}
		}
		wordbooker_debugger("Finished comment handling for WP post ".$row->wp_post_id,$row->fb_post_id,-2,9);
		wordbooker_debugger("Finished comment handling for WP post ".$row->wp_post_id,$row->fb_post_id,-3,9);
	}
	return $processed_posts;
}

function wordbooker_get_comments_from_facebook($user_id) {
	global $wpdb,$blog_id,$comment_user;
	$processed_posts=0;
	$wbuser = wordbooker_get_userdata($user_id);
	if (strlen($wbuser->access_token)<20) {
	wordbooker_debugger("No user session for comment handling "," ",-2,9) ;
	return 0; }
	$wordbooker_settings=get_option('wordbooker_settings'); 
	$comment_approve=0;
	if (isset($wordbooker_settings['wordbook_comment_approve'])) {$comment_approve=1;}
	$sql='Select distinct fb_post_id from '.WORDBOOKER_POSTCOMMENTS.' where fb_comment_id is null and user_id='.$user_id.' and blog_id='.$blog_id. " and in_out is null ";
	$rows = $wpdb->get_results($sql);
	wordbooker_debugger("Blog posts with FB Posts against them : ".$sql,count($rows),-2,98);
	foreach ($rows as $fb_comment) {
		wordbooker_debugger("Starting comment handling for FB post ".$fb_comment->fb_post_id,"",-2,9);
		wordbooker_debugger("Starting comment handling for FB post ".$fb_comment->fb_post_id,"",-3,9);
	#	$sql="select fb_comment_id from ".WORDBOOKER_POSTCOMMENTS." where fb_post_id='".$fb_comment->fb_post_id."' and in_out!='out' and in_out!='stat' order by comment_timestamp desc";
	#	$from_comment=$wpdb->get_row($sql);
		try {
			$all_comments=wordbooker_fb_get_comments($fb_comment->fb_post_id,$wbuser->access_token);
			wordbooker_debugger("Comments pulled from Facebook",count($all_comments->data),-2,9);
		}
		catch (Exception $e) 
		{
			$error_msg = $e->getMessage();
			$err_no=(integer) substr($error_msg,2,3);
			wordbooker_debugger("Failed to get comment from Facebook : ".$error_msg,$row->fb_post_id,-2,9);
			wordbooker_debugger("Failed to get comment from Facebook : ".$error_msg,$row->fb_post_id,-3,9);
		}
		if(count($all_comments->data) > 0 ) {
			foreach($all_comments->data as $single_comment) {
				# Now check that we don't already have this comment in the table as it means we've processed it before (or sent it to FB)
				$sql="Select fb_comment_id from ".WORDBOOKER_POSTCOMMENTS." where fb_comment_id='".$single_comment->id."'";
				if(!$wpdb->query($sql)) {
					wordbooker_debugger("Found new comment for FB post ".$fb_comment->fb_post_id,"from : ".$single_comment->from->name,-2,9);
					wordbooker_debugger("Found new comment for FB post ".$fb_comment->fb_post_id,"from : ".$single_comment->from->name,-3,9);
					$commemail=$wordbooker_settings['wordbooker_comment_email'];
					$time = date("Y-m-d H:i:s",strtotime($single_comment->created_time));
					$current_offset = get_option('gmt_offset');
					$atime = date("Y-m-d H:i:s",strtotime($single_comment->created_time)+(3600*$current_offset));
					$sql="select distinct wp_post_id from ".WORDBOOKER_POSTCOMMENTS." where fb_post_id='".$fb_comment->fb_post_id."'";
					$wp_post_rows = $wpdb->get_results($sql);
					wordbooker_debugger("Blogs posts to send comment to : ".$sql,count($wp_post_rows),-2,98);
					foreach ($wp_post_rows as $wp_post_row) {
						$wordbooker_post_options = get_post_meta($wp_post_row->wp_post_id, '_wordbooker_options', true);
						if (!isset($wordbooker_post_options['wordbooker_comment_get'])) {
							wordbooker_debugger("Incoming comments disabled for WP post ".$wp_post_row->wp_post_id,' ',-2,9);
							wordbooker_debugger("Incoming comments disabled for WP post ".$wp_post_row->wp_post_id,' ',-3,9);	
							continue ;
						}
						$data = array(
							'comment_post_ID' => $wp_post_row->wp_post_id,
							'comment_author' => $single_comment->from->name,
							'comment_author_email' => $commemail,
							'comment_author_url' => 'https://www.facebook.com/'.$single_comment->from->id,
							'comment_content' =>$single_comment->message,
							'comment_author_IP' => '127.0.0.1',
							'comment_date' => $atime,
							'comment_date_gmt' => $time,
							'comment_parent'=> 0,
							'user_id' => 0,
						   	'comment_agent' => 'Wordbooker plugin '.WORDBOOKER_CODE_RELEASE,
							'comment_approved' => $comment_approve,
						);
						$data = apply_filters('preprocess_comment', $data); 
						$data['comment_parent'] = isset($data['comment_parent']) ? absint($data['comment_parent']) : 0;
						$parent_status = ( 0 < $data['comment_parent'] ) ? wp_get_comment_status($data['comment_parent']) : '';
						$data['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $data['comment_parent'] : 0;
						$newComment= wp_insert_comment($data);
						update_comment_meta($newComment, "fb_uid", $single_comment->from->id);
						wordbooker_debugger("Inserted comment from ".$single_comment->from->name." into ".$wp_post_row->wp_post_id,"",-2,9);
						wordbooker_debugger("Inserted comment from ".$single_comment->from->name." into ".$wp_post_row->wp_post_id,"",-3,9);
						$sql="Insert into ".WORDBOOKER_POSTCOMMENTS." (fb_post_id,user_id,comment_timestamp,wp_post_id,blog_id,wp_comment_id,fb_comment_id,in_out) values ('".$fb_comment->fb_post_id."',".$user_id.",".strtotime($single_comment->created_time).",".$wp_post_row->wp_post_id.",".$blog_id.",".$newComment.",'".$single_comment->id."','in' )";
						$wpdb->query($sql);
						$processed_posts=$processed_posts+1;
					}
					wordbooker_debugger("Finished comment inserts for FB post ".$fb_comment->fb_post_id,"",-2,9);
					wordbooker_debugger("Finished comment inserts for FB post ".$fb_comment->fb_post_id,"",-3,9);
				}
			   else {
					wordbooker_debugger("Found existing comment for FB post ".$fb_comment->fb_post_id,"from : ".$single_comment->from->name,-2,9);
					wordbooker_debugger("Found existing comment for FB post ".$fb_comment->fb_post_id,"from : ".$single_comment->from->name,-3,9);	
				}
			}
		}
		wordbooker_debugger("Finished comment handling for FB post ".$fb_comment->fb_post_id,"",-2,9);
		wordbooker_debugger("Finished comment handling for FB post ".$fb_comment->fb_post_id,"",-3,9);
	}
	return $processed_posts;
}

function parse_wordbooker_comment_attributes($comment_id,$comment_structure,$comment_tag) {
	# Changes various "tags" into their WordPress equivalents.
	$comment = get_comment($comment_id);
	$comment_author=$comment->comment_author; 
	$comment_date=date_i18n(get_option('date_format'),strtotime($comment->comment_date));
	$comment_time=date_i18n(get_option('time_format'),strtotime($comment->comment_date));
	$comment_content=$comment->comment_content;
	# Now do the replacements
	$comment_structure=str_ireplace( '%author%',$comment_author,$comment_structure );
	$comment_structure=str_ireplace( '%content%',$comment_content,$comment_structure );
	$comment_structure=str_ireplace( '%date%',$comment_date,$comment_structure );
	$comment_structure=str_ireplace( '%time%',$comment_time,$comment_structure );
	$comment_structure=str_ireplace( '%tag%',$comment_tag,$comment_structure );
	return $comment_structure;
}
?>
