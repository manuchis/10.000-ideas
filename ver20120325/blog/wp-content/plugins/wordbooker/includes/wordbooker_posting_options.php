<?php

/**
Extension Name: Wordbooker Posting Options 
Extension URI: http://wordbooker.tty.org.uk
Version: 2.1
Description: Posting Options for the WordBooker Plugin
Author: Steve Atty
*/

/* Use the admin_menu action to define the custom boxes. Dont do this unless we have options set */
if (get_option('wordbooker_settings')) { add_action('admin_menu', 'wordbooker_add_custom_box');}

/* Adds a custom section to the "advanced" Post edit screens */
function wordbooker_add_custom_box() {
	global $user_ID;
	if (current_user_can(WORDBOOKER_MINIMUM_ADMIN_LEVEL)) {
		add_meta_box( 'wordbooker_post_options', __('WordBooker Options', 'wordbooker'),'wordbooker_inner_custom_box', 'post', 'advanced' );
		add_meta_box( 'wordbooker_post_options', __('WordBooker Options', 'wordbooker'),'wordbooker_inner_custom_box', 'page', 'advanced' );
		# only display the tagging box if the user actually has a Wordbooker account configured.
	#	if (wordbooker_get_userdata($user_ID)) {
	#		add_meta_box( 'wordbooker_tagging', __('Facebook Tagging'),'wordbooker_tag_inner_custom_box', 'post', 'advanced' );
	#		add_meta_box( 'wordbooker_tagging', __('Facebook Tagging'),'wordbooker_tag_inner_custom_box', 'page', 'advanced' );
	#	}
	}
}

   
/* Prints the inner fields for the custom post/page section */
function wordbooker_inner_custom_box() {
	# We need to put in a "read only" key on the inputs for users who will be able to post but not be able to change settings.
	echo '<input type="hidden" name="wordbooker_noncename" id="wordbooker_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	global $wpdb,$user_ID,$blog_id,$post;
	$wordbooker_global_settings=wordbooker_options(); 
	$wordbooker_settings=$wordbooker_global_settings;
	$checked_flag=array('on'=>'checked','off'=>'', 100=>'', 200=>'checked');
	# Now get the user settings for this blog.
	# If the user is set to logged in user then get the current user, otherwise pick up the settings for the user selected.
	if  ($wordbooker_settings["wordbooker_default_author"] == 0 ) {$wb_user_id=$user_ID;} else {$wb_user_id=$wordbooker_settings["wordbooker_default_author"];}
	$wordbooker_user_settings_id="wordbookuser".$blog_id;
	# We need to do some more checking here. If the user does not have an entry in the wordbooker user table then we should get the user options for the user set as the default user.
	$wordbookuser=get_usermeta($wb_user_id,$wordbooker_user_settings_id);
	# If we have user settings then lets go through and override the blog level defaults.
	if(is_array($wordbookuser)) {
		foreach (array_keys($wordbookuser) as $key) {
			if ((strlen($wordbookuser[$key])>0) && ($wordbookuser[$key]!="0") ) {
				$wordbooker_settings[$key]=$wordbookuser[$key];
			} 
		}
	}
	$x = get_post_meta($post->ID, '_wordbooker_options', true); 
#	var_dump($x);
	if(is_array($x)) {
		foreach (array_keys($x) as $key ) {
			if (substr($key,0,8)=='wordbook') {
				$post_meta[$key]=str_replace( array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),array('&','"','\'','<','>',"\t"),$x[$key]);
			}
		}
	}	
	if(is_array($post_meta)) {
		$wordbooker_settings=$post_meta;
	}
	$post_pub_prompt=__("Publish this post to Facebook", 'wordbooker');
	if($post->post_type=='page'){
		$publish=$wordbooker_settings["wordbooker_publish_page_default"];		
		$post_pub_prompt=__("Publish this page to Facebook", 'wordbooker');
	}
	else {
		$publish=$wordbooker_settings["wordbooker_publish_post_default"];
			$post_pub_prompt=__("Publish this post to Facebook", 'wordbooker');
	}

	if($wordbooker_settings["wordbooker_post_edited"]!='yes') {$wordbooker_settings["wordbooker_publish_default"]=$publish;}
	if ($post->post_status=="publish") {$wordbooker_settings["wordbooker_publish_default"]='';}
	
		if ( isset($wordbooker_settings['wordbooker_disabled'])) { echo "<div align='center'><b> ".__('WARNING : Wordbooker is DISABLED','wordbooker')."</b></div>";} else {
	if ( isset($wordbooker_settings['wordbooker_fake_publish'])) { echo "<div align='center'><b> ".__('WARNING : Wordbooker is in TEST mode - NO Posts will be made to Facebook','wordbooker')."</b></div>";}}
echo "<br />";
	if (wordbooker_get_userdata($user_ID)) {

		echo __("The following options override the defaults set on the options page", 'wordbooker')."<br /><br />";

		$sql="select wpu.ID,wpu.display_name,facebook_id from $wpdb->users wpu,".WORDBOOKER_USERDATA." wud where wpu.ID=wud.user_id and wud.user_id=".$user_ID;
		$wb_users = $wpdb->get_results($sql);
		# Get the list of pages this user is an admin for
		$result = $wpdb->get_row("select pages from ".WORDBOOKER_USERDATA." where user_id=".$user_ID);
		$fanpages=unserialize($result->pages);
		$fanpages2=$fanpages;
		$fanpages[]=array( 'id'=>'PW:'.$wb_users[0]->facebook_id, 'name'=>__("Personal Wall",'wordbooker'));
		$have_fan_pages=0;

		# If the post has already been published then we uncheck the publish option 
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_publish_default" '.$checked_flag[$wordbooker_settings["wordbooker_publish_default"]].' > '.$post_pub_prompt.'<br />';
if (count($fanpages)>1){
	echo '<p><label for="wb_primary_target">'.__('Post to the following Wall', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_primary_target" name="wordbooker_primary_target"  >';
				$option="";
			foreach ($fanpages as $fan_page) {
				if (strlen($fan_page['name'])>=2) {
				if ($fan_page['id']==$wordbooker_settings["wordbooker_primary_target"] ) {$option .= '<option selected="yes" value='.$fan_page['id'].'>';} else { $option .= '<option value='.$fan_page[id].'>';}
				$option .= $fan_page['name']." (".substr($fan_page['id'],3).")&nbsp;&nbsp;";
				$option .= '</option>';
				}
			}
			echo $option;
			echo '</select> &nbsp;';
	$arr = array(1=> __("As a Wall Post", 'wordbooker'),  2=> __("As a Note", 'wordbooker'), 3=> __("As a Status Update" , 'wordbooker'), 4=> __("As a Link" , 'wordbooker')   );
	echo '<select id="wordbooker_primary_type" name="wordbooker_primary_type"  >';
	foreach ($arr as $i => $value) {
       		 if ($i==$wordbooker_settings['wordbooker_primary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
	}
	echo '</select>	&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_primary_active" '.$checked_flag[$wordbooker_settings["wordbooker_primary_active"]].'></p>';

	} else 

	{
	echo '<p><label for="wb_primary_target">'.__('Post to my Personal Wall', 'wordbooker').' : </label> ';
	echo '<input type="hidden" name="wordbooker_primary_target" value="PW:'.$wb_users[0]->facebook_id.'" />';

$arr = array(1=> __("As a Wall Post", 'wordbooker'),  2=> __("As a Note", 'wordbooker'), 3=> __("As a Status Update" , 'wordbooker'),4=> __("As a Link" , 'wordbooker')   );
	echo '<select id="wordbooker_primary_type" name="wordbooker_primary_type"  >';
	foreach ($arr as $i => $value) {
       		 if ($i==$wordbooker_settings['wordbooker_primary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
	}
	echo '&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_primary_active" '.$checked_flag[$wordbooker_settings["wordbooker_primary_active"]].'></p><p>';
	}
		if ($fanpages2 ){
			$have_fan_pages=1;
		echo '<label for="wb_secondary_target">'.__('Post to the following Wall', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_secondary_target" name="wordbooker_secondary_target"  >';
				$option="";
			foreach ($fanpages2 as $fan_page) {
				if (strlen($fan_page['name'])>=2) {
				if ($fan_page['id']==$wordbooker_settings["wordbooker_secondary_target"] ) {$option .= '<option selected="yes" value='.$fan_page['id'].'>';} else { $option .= '<option value='.$fan_page[id].'>';}
				$option .= $fan_page['name']." (".substr($fan_page['id'],3).")&nbsp;&nbsp;";
				$option .= '</option>';
				}
			}
			echo $option;
			echo '</select> &nbsp;'; 
		echo '<select id="wordbooker_secondary_type" name="wordbooker_secondary_type"  >';
		foreach ($arr as $i => $value) {
	       		 if ($i==$wordbooker_settings['wordbooker_secondary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
	      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
	echo "</select> ";
		echo '&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_secondary_active" '.$checked_flag[$wordbooker_settings["wordbooker_secondary_active"]].'></p><P>';

		}

		echo __('Length of Extract', 'wordbooker').' : <select id="wordbooker_extract_length" name="wordbooker_extract_length"  >';
	        $arr = array(10=> "10",20=> "20",50=> "50",100=> "100",120=> "120",150=> "150",175=> "175",200=> "200",  250=> "250", 256=>__("256 (Default) ", 'wordbooker'), 270=>"270", 300=>"300", 350 => "350",400 => "400",500 => "500",600 => "600",700 => "700",800 => "800",900 => "900");
	        foreach ($arr as $i => $value) {
	                if ($i==$wordbooker_settings['wordbooker_extract_length']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
	               else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
	        echo "</select><br />";

		echo __('Action Link Option', 'wordbooker').' :<select id="wordbooker_actionlink" name="wordbooker_actionlink"  >';	
	       $arr = array(100=> "None ",  200=> __("Share Link", 'wordbooker'), 300=>__("Read Full Article", 'wordbooker'));
		        foreach ($arr as $i => $value) {
		                if ($i==$wordbooker_settings['wordbooker_actionlink']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';} 
		               else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}}
		        echo "<</select><br /><br />";

		echo '<input type="hidden" name="soupy" value="twist" />';

	#	echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_publish_override" '.$checked_flag[$wordbooker_settings["wordbooker_publish_override"]].' >'.__('Force Re-Publish Post to Facebook on Edit (overrides republish window)').'<br />';
		if ( function_exists( 'get_the_post_thumbnail' ) ) {
			echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_thumb_only" '.$checked_flag[$wordbooker_settings["wordbooker_thumb_only"]].'>'.__(' Use Thumbnail as only image', 'wordbooker').' <br />';
		}

		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_use_excerpt" '.$checked_flag[$wordbooker_settings["wordbooker_use_excerpt"]].' > Use Wordpress Excerpt for Wall Post <br />';
		echo 'Facebook Post Attribute line: <INPUT NAME="wordbooker_attribute" size=80 maxlength=240 value="'.stripslashes($wordbooker_settings["wordbooker_attribute"]).'"><br />';	
		echo __('Facebook Status Update text', 'wordbooker').' : <INPUT NAME="wordbooker_status_update_text" size=60 maxlength=240 value="'.stripslashes($wordbooker_settings["wordbooker_status_update_text"]).'"><br />';

		if($post->post_type=='page'){
			if(!isset($wordbooker_settings['wordbooker_like_button_page'])) {$wordbooker_settings['wordbooker_like_button_page']=1;}
			if(!isset($wordbooker_settings['wordbooker_share_button_page'])) {$wordbooker_settings['wordbooker_share_button_page']=1;}
			echo __('Show Facebook Like/Send for this Page', 'wordbooker').' : <select id="wordbooker_like_button_page" name="wordbooker_like_button_page"  >';
			$arr = array(1=> __("Yes", 'wordbooker'),  2=> __("No", 'wordbooker') );
				foreach ($arr as $i => $value) {
					if ($i==$wordbooker_settings['wordbooker_like_button_page']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
				       else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
				}
	        	echo "</select><br />";
		
echo __('Show Facebook Share for this Page', 'wordbooker').' : <select id="wordbooker_share_button_page" name="wordbooker_share_button_page"  >';
$arr = array(1=> __("Yes", 'wordbooker'),  2=> __("No", 'wordbooker') );
	        foreach ($arr as $i => $value) {
	                if ($i==$wordbooker_settings['wordbooker_share_button_page']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
	               else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
	        echo "</select><br />";
		}
		else {

			if(!isset($wordbooker_settings['wordbooker_like_button_post'])) {$wordbooker_settings['wordbooker_like_button_post']=1;}
			if(!isset($wordbooker_settings['wordbooker_share_button_post'])) {$wordbooker_settings['wordbooker_share_button_post']=1;}
			echo __('Show Facebook Like/Send for this Post', 'wordbooker').' : <select id="wordbooker_like_button_post" name="wordbooker_like_button_post"  >';
			$arr = array(1=> __("Yes", 'wordbooker'),  2=> __("No", 'wordbooker') );
				foreach ($arr as $i => $value) {
					if ($i==$wordbooker_settings['wordbooker_like_button_post']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
				       else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
				}
				echo "</select><br />";
		
			echo __('Show Facebook Share for this Post', 'wordbooker').' : <select id="wordbooker_share_button_post" name="wordbooker_share_button_post"  >';
			$arr = array(1=> __("Yes", 'wordbooker'),  2=> __("No", 'wordbooker') );
				foreach ($arr as $i => $value) {
					if ($i==$wordbooker_settings['wordbooker_share_button_post']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
				       else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
				}
				echo "</select><br />";
		}


		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_comment_put" '.$checked_flag[$wordbooker_settings["wordbooker_comment_put"]].' > '.__('Push Comments from this post to Facebook', 'wordbooker').'<br />';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_comment_get" '.$checked_flag[$wordbooker_settings["wordbooker_comment_get"]].' > '.__('Pull Comments from Facebook for this post', 'wordbooker').'<br />';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_use_facebook_comments" '.$checked_flag[$wordbooker_settings["wordbooker_use_facebook_comments"]].' > '.__('Enable Facebook Comments for this post', 'wordbooker').'<br />';
	}  else {
echo "<p>".__('Wordbooker Blog level settings are in force','wordbooker')."<br /></p>";

if ( isset($wordbooker_settings['wordbooker_allow_publish_select'])) { 
echo '<input type="hidden" name="crabstick" value="stairwell" />';
echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_publish_default" '.$checked_flag[$wordbooker_settings["wordbooker_publish_default"]].' > '.__('Publish This Post to Facebook', 'wordbooker').'<br />';
#echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_publish_override" '.$checked_flag[$wordbooker_settings["wordbooker_publish_override"]].' >'.__('Force Re-Publish Post to Facebook on Edit (overrides republish window)').'<br />';
} else { echo '<input type="hidden" name="wordbooker_publish_default" value="'.$wordbooker_settings["wordbooker_publish_default"].'" />';}
}

echo '<input type="hidden" name="wordbooker_publish_page_default" value="'.$wordbooker_settings["wordbooker_publish_page_default"].'" />';
echo '<input type="hidden" name="wordbooker_publish_post_default" value="'.$wordbooker_settings["wordbooker_publish_post_default"].'" />';
echo '<input type="hidden" name="wordbooker_post_edited" value="yes" />';
}

function wordbooker_tag_inner_custom_box() {
	global $wpdb,$user_ID,$blog_id,$post;
	$wordbooker_options = get_post_meta($post->ID, '_wordbooker_options', true); 
	echo '<script type="text/javascript"> var userid='.$user_ID.'</script>';
	echo '<script type="text/javascript" src="/wp-content/plugins/wordbooker/wordbooker_functions.js" DEFER></script>';
	echo '<script type="text/javascript" src="/wp-content/plugins/wordbooker/wordbooker_actb.js"></script>';
	echo '<input type="hidden" name="wordbooker_tag_noncename" id="wordbooker_tag_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	echo 'Tag list : <input name="wordbooker_tag_list" id="wordbooker_tag_list" size="80" maxlength="2000" value="'.$wordbooker_options['wordbooker_tag_list'].'" /><br />';	
	echo 'Start typing a friends name here :  <input type="text" ID="FriendID" name="FriendID" align="LEFT" size="30" maxlength="200"/> ';
	echo ' <INPUT TYPE="button" name="FBsub" VALUE="Add Friend to list"  onclick="Wordbooker_getFBFriend(FriendID.value);"/>';
	echo '<script>wordbooker_actb(document.getElementById("FriendID"),new Array());</script>';
}
?>
