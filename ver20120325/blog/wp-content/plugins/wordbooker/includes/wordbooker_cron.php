<?php

/**
Extension Name: Wordbooker Cron
Extension URI: http://blogs.canalplan.org.uk/steve
Version: 2.1
Description: Collection of processes that are often handled by wp_cron scheduled jobs
Author: Steve Atty
*/

function wordbooker_cache_refresh($user_id) {
	global $blog_id,$wpdb,$table_prefix,$wordbooker_user_settings_id,$wbooker_user_id;
	$wbooker_user_id=$user_id;
	$result = $wpdb->query(' DELETE FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE   blog_id ='.$blog_id.' and (user_ID='.$user_id.' or user_ID=0 ) and post_id<=1');
	wordbooker_debugger("Cache Refresh Commence ",$user_id,-1,9) ; 
	$result = $wpdb->get_row("select facebook_id from ".WORDBOOKER_USERDATA." where user_ID=".$user_id);
	$uid=$result->facebook_id;
	$wbuser2= wordbooker_get_userdata($user_id);

	$wordbooker_settings =get_option('wordbooker_settings'); 
	wordbooker_debugger("Cache Refresh for ",$wbuser2->name,-1,9) ;
	wordbooker_debugger("UID length : ",strlen($uid),-1,9) ;  
	# If we've not got the ID from the table lets try to get it from the logged in user
	if (strlen($uid)==0) {
		wordbooker_debugger("No Cache record for user - getting Logged in user ",$uid,-1,9) ; 
		try {
			$x=wordbooker_get_fb_id(null,$wbuser2->access_token);
			$uid=$x->id;
		}
		catch (Exception $e) {
			$error_code = $e->getCode();
			$error_msg = $e->getMessage();
			wordbooker_debugger($error_msg," ",-1,9) ;
			#unset($uid);
		}
	}
	# If we now have a uid lets go and do a few things.
	if (strlen($uid)>0){
		wordbooker_debugger("Cache processing for user : ",$wbuser2->name." (".$uid.")",-1,9) ;
		wordbooker_debugger("Getting Permisions for : ",$uid,-1,9) ;
		$ret=wordbooker_fb_pemissions($wbuser2->facebook_id,$wbuser2->access_token); 
		# If we have an  $ret->error->message then we have a problem
		if($ret->error->message)  {
		wordbooker_append_to_errorlogs("Your Facebook Session is invalid", "99", $ret->error->message,'',$user_id);
		wordbooker_delete_user($user_id,1);
		return;
		}
		if(strlen(serialize($ret))<20) {wordbooker_debugger("Permissions fetch failed - skipping ",'',-1,9) ;} else {
		$add_auths=0;
		$permlist= array(WORDBOOKER_FB_PUBLISH_STREAM,WORDBOOKER_FB_STATUS_UPDATE,WORDBOOKER_FB_READ_STREAM,WORDBOOKER_FB_CREATE_NOTE,WORDBOOKER_FB_PHOTO_UPLOAD,WORDBOOKER_FB_VIDEO_UPLOAD,WORDBOOKER_FB_MANAGE_PAGES,WORDBOOKER_FB_READ_FRIENDS);
		$key=0;
		foreach($permlist as $perm){
		try {
			$permy=$ret->data[0]->$perm;
			$error_code = null;
	
			if($permy!=1) {
				wordbooker_debugger("User is missing permssion : ",$perm,-1,9) ;
				$add_auths = $add_auths | pow(2,$key);
			} 
			else {
				wordbooker_debugger("User has permssion : ",$perm,-1,9) ;
			}
			$error_msg = null;
		} catch (Exception $e) {
			$error_msg = $e->getMessage();
			wordbooker_debugger("Permissions may be corrupted  ",$error_message,-1,9);
			$users = null;
			$add_auths=1;
		}
			$key=$key+1;
		}

		wordbooker_debugger("Additional Permissions needed : ",$add_auths,-1,9) ;
		$sql="update ".WORDBOOKER_USERDATA." set auths_needed=".$add_auths." where user_ID=".$user_id;
		$result = $wpdb->get_results($sql);
		}
		# Lets get the person/page this user wants to get the status for. We get this from the user_meta
		$wordbooker_user_settings_id="wordbookuser".$blog_id;
		$wordbookuser_setting=get_usermeta($user_id,$wordbooker_user_settings_id);
		$suid="PW:".$uid;
		if ( isset ($wordbookuser_setting['wordbooker_status_id']) && $wordbookuser_setting['wordbooker_status_id']!=-100) {$suid=$wordbookuser_setting['wordbooker_status_id'];}
		$x=explode(":",$suid);
		$suid=$x[1];

		wordbooker_debugger("Getting Pages administered by : ",$uid,-1,9) ;			
		try {
			$query="SELECT name, page_url, page_id FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid= $uid )";
			$fb_page_info = wordbooker_fql_query($query,$wbuser2->access_token);
		} 
		catch (Exception $e) 
		{
		$error_msg = $e->getMessage();
		wordbooker_debugger("Failed to get page info : ",$error_msg,-1,9);
		}
		try {
		$ret_code=wordbooker_me($wbuser2->access_token);
		}
		catch (Exception $e) 
		{
		$error_msg = $e->getMessage();
		wordbooker_debugger("Failed to get page tokens : ".$error_msg," ",-1,9);
		}
		if (isset($ret_code->data)){
		foreach($ret_code->data as $page_access) {
			$page_token[$page_access->id]=$page_access->access_token;
		}
		$all_pages=array();
		if (is_array($fb_page_info)) { 
			$encoded_names=str_replace('\\','\\\\',serialize($fb_page_info));
			 foreach ( $fb_page_info as $pageinfo ) {	
				$pages["id"]="FW:".trim($pageinfo->page_id,',');
				if(strlen($pageinfo->name)>1){
				$page_desc="";
				if(strpos($pageinfo->page_url,"application.php")) {$page_desc=__(" (Application)", 'wordbooker');}
					if (function_exists('mb_convert_encoding')) {
						$pages["name"]=mb_convert_encoding($pageinfo->name.$page_desc,'UTF-8');
					}
					else
					{
						$pages["name"]=$pageinfo->name.$page_desc;
					}
					$pages['url']=$pageinfo->page_url;
					$pages["access_token"]=$page_token[$pageinfo->page_id];
					$all_pages[]=$pages;
					wordbooker_debugger("Page info for page ID ".$pageinfo->page_id,$pageinfo->name.$page_desc,-1,9) ;
				}

			}
		}
		 else {
			wordbooker_debugger("Failed to get page information from FB"," ",-1,9);
		 }
		}
		$fb_group_list=array();
		$all_groups=array();
		wordbooker_debugger("Getting Groups owned or managed by : ",$uid,-1,9) ;
		try {
			$query="Select positions, gid from group_member where uid=$uid";
			$fb_groups= wordbooker_fql_query($query,$wbuser2->access_token);
			if(is_array($fb_groups)){
				foreach($fb_groups as $fb_group){
					# Check to see if there are any positions. If not then the user is only a member of the group and thus we dont want it in the list.
					if(count($fb_group->positions)>0) {
						wordbooker_debugger("Getting details for group : ",$fb_group->gid,-1,9) ;
						$query="Select name,gid from group where gid =".$fb_group->gid;
						$fb_group_info= wordbooker_fql_query($query,$wbuser2->access_token);
						$fb_group_list[]= (array) $fb_group_info;
					}			
				}
			}
		} 

		catch (Exception $e) 
		{
			$error_msg = $e->getMessage();
			wordbooker_debugger("Failed to get group info : ",$error_msg,-1,9);
		}

			if (is_array($fb_group_list)) {
		$encoded_names=str_replace('\\','\\\\',serialize($fb_group_list));
		 foreach ( $fb_group_list as $groupinfo ) {
			$groupinfo = (array) $groupinfo;
			if (strlen($groupinfo[0]->gid) >1) {
			$groups["page_id"]=trim($groupinfo[0]->gid,',');	
			$groups["id"]="GW:".trim($groupinfo[0]->gid,',');
			if (function_exists('mb_convert_encoding')) {
				$groups["name"]=mb_convert_encoding($groupinfo[0]->name,'UTF-8');
			}
			else
			{
				$groups["name"]=$groupinfo[0]->name;
			}
			$groups["access_token"]="dummy access token";
			$all_groups[]=$groups;
			wordbooker_debugger("Group info for group ID ".$groupinfo[0]->gid,$groupinfo[0]->name,-1,9) ;
			}
			}
		}
		 else {
			wordbooker_debugger("Failed to get group information from FB"," ",-1,9);
		 }

		
		$all_pages_groups=@array_merge($all_pages,$all_groups);
		$encoded_names=str_replace('\\','\\\\',serialize($all_pages_groups));
/*

		try {
			$query="SELECT flid, owner, name FROM friendlist WHERE owner=$uid";
			$fb_friend_lists= wordbooker_fql_query($query,$wbuser2->access_token);
		if (is_array($fb_friend_lists)) {
		$sql="Delete from ".WORDBOOKER_FB_FRIEND_LISTS." where user_id=".$user_id;
		$result = $wpdb->get_results($sql);
		foreach ($fb_friend_lists as $friend_list) {
			if (function_exists('mb_convert_encoding')) {
				$friend_list->name=mb_convert_encoding($friend_list->name,'UTF-8');
			}
		$sql="replace into ".WORDBOOKER_FB_FRIEND_LISTS." (user_id, flid,  owner, name) values (".$user_id.",'".$friend_list->flid."','".$friend_list->owner."','".$friend_list->name."')";
		$result = $wpdb->get_results($sql);
		}
		}	

		}
		catch (Exception $e) 
		{
			$error_msg = $e->getMessage();
			wordbooker_debugger("Failed to get friend lists : ",$error_msg,-1,9);
		}

 
		try {
			$query="Select name,uid from user where uid in (Select uid from friendlist_member where flid='10150839623220195')";
			$fb_friends_info = wordbooker_fql_query($query,$wbuser2->access_token);
		} 
		catch (Exception $e) 
		{
			$error_msg = $e->getMessage();
			wordbooker_debugger("Failed to get friends : ",$error_msg,-1,9);
		}
			if (is_array($fb_friends_info) ) {
			$sql="delete from ".WORDBOOKER_FB_FRIENDS." where user_id=".$user_id;
			$result = $wpdb->get_results($sql);
			foreach ($fb_friends_info as $friend_info) {
				if (function_exists('mb_convert_encoding')) {
					$friend_info->name=mb_convert_encoding($friend_info->name,'UTF-8');
				}
			$sql="insert into ".WORDBOOKER_FB_FRIENDS." (user_id, facebook_id, name, blog_id) values (".$user_id.",'".$friend_info->uid."','".$friend_info->name."',".$blog_id.")";
			$result = $wpdb->get_results($sql);
			}
		}
*/
		$fb_status_info=wordbooker_status_feed($suid,$wbuser2->access_token);
		foreach($fb_status_info->data as $fbstat) {
			if(!is_null($fbstat->message)){
				if ($suid==$fbstat->from->id) {
	
					$status_message=$fbstat->message;
					$status_time=$fbstat->created_time;
					break; 
				}
			 }
		}
		$picture = 'https://graph.facebook.com/'.$suid.'/picture?type=normal';
		$fb_profile_info=wordbooker_get_fb_id($suid,$wbuser2->access_token);
		wordbooker_debugger("Setting Status Name as  : ",mysql_real_escape_string($fb_profile_info->name),-1,9) ;
		$sql="insert into ".WORDBOOKER_USERSTATUS." set name='".mysql_real_escape_string($fb_profile_info->name)."'";
			if (isset($status_time)) {
				if (stristr($status_message,"[[PV]]")) {
					wordbooker_debugger("Found [[PV]] - not updating status"," ",-1,9);
				} 
				else {
					wordbooker_debugger("Setting status as  : ",mysql_real_escape_string($status_message),-1,9) ;
					$sql.=", status='".mysql_real_escape_string($status_message)."'";
					$sql.=", updated=".mysql_real_escape_string(strtotime($status_time));
				}
		} else {wordbooker_debugger("Failed to get Status information from FB"," ",-1,9); }

		wordbooker_debugger("Setting Status URL as  : ",mysql_real_escape_string($fb_profile_info->link),-1,9) ;
		$sql.=", url='".mysql_real_escape_string($fb_profile_info->link)."'";
		$sql.=", pic='".mysql_real_escape_string($picture)."'";
		$sql.=", facebook_id='".$uid."'";
		$sql.=",user_ID=".$user_id;
		$sql.=",blog_id=".$blog_id;
		$sql.=" on duplicate key update name='".mysql_real_escape_string($fb_profile_info->name)."'";
		if (isset($status_message)) {
			if (stristr($status_message,"[[PV]]")) {
			
			} 
			else {
				$sql.=", status='".mysql_real_escape_string($status_message)."'";
				$sql.=", updated=".mysql_real_escape_string(strtotime($status_time));
			}
		}
		if (isset($fb_profile_info->link)) {
			$sql.=", url='".mysql_real_escape_string($fb_profile_info->link)."'";
			$sql.=", pic='".mysql_real_escape_string($picture)."'";
		}
		$result = $wpdb->get_results($sql);
		$real_user=wordbooker_get_fb_id($uid,$wbuser2->access_token);
		wordbooker_debugger("Setting user name as  : ",mysql_real_escape_string($real_user->name),-1,9) ;
		$sql="update ".WORDBOOKER_USERDATA." set name='".mysql_real_escape_string($real_user->name)."'";
			
		$sql.=", facebook_id='".$uid."'";
		$sql.=", pages= '".mysql_real_escape_string($encoded_names)."'";
		$sql.=", use_facebook=1";
		$sql.="  where user_ID=".$user_id." and blog_id=".$blog_id;
		
		$result = $wpdb->get_results($sql);
	}
#fclose($fp);
	wordbooker_debugger("Cache Refresh Complete for user",$uid,-1,9) ; 
}


function wordbooker_poll_facebook($single_user=null) {
	global  $wpdb, $user_ID,$table_prefix,$blog_id;
	# If a user ID has been passed in then restrict to that single user.
	wordbooker_trim_errorlogs();
	$limit_user="";
	if (isset($single_user)) {$limit_user=" where user_id=".$single_user." limit 1";}
	$wordbooker_settings =get_option('wordbooker_settings'); 
	
	# This runs through the Cached users and refreshes them
      	$sql="Select user_id,name from ".WORDBOOKER_USERDATA.$limit_user;
        $wb_users = $wpdb->get_results($sql);
	if (is_array($wb_users)) {
		wordbooker_debugger("Batch Cache Refresh Commence "," ",-1,9) ; 
		foreach ($wb_users as $wb_user){	
			wordbooker_debugger("Calling Cache refresh for  :  ",$wb_user->name." (".$wb_user->id.")",-1,9) ;	
			$wbuser = wordbooker_get_userdata($wb_user->user_id);
		#	$fbclient = wordbooker_fbclient($wbuser);
			wordbooker_cache_refresh($wb_user->user_id);
		}
		wordbooker_debugger("Batch Cache Refresh completed "," ",-1,9) ; 
	}
}


?>
