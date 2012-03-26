<?php
require_once( '../../../wp-config.php');
global $wpdb,$user_ID;
#nocache_headers();
$match=$_GET['match'];
$name=$_GET['name'];
$userid=$_GET['userid'];
#echo $userid;
if (strlen($match)>0){
$sql="select name from ".WORDBOOKER_FB_FRIENDS." where name like '".$match."%' and user_id=".$userid;

$wb_users = $wpdb->get_results($sql);

if (is_array($wb_users)) {
	foreach ($wb_users as $wb_user){
			echo $wb_user->name."#";
}
}
}
if (strlen($name)>0){
$sql="select facebook_id from ".WORDBOOKER_FB_FRIENDS." where name='".$name."'and user_id=".$userid;
#echo $sql;
$wb_users = $wpdb->get_results($sql);

if (is_array($wb_users)) {
	foreach ($wb_users as $wb_user){
			echo $wb_user->facebook_id;
}
}
}
?>
