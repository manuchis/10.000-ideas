<?php

/**
Extension Name: Wordbooker Options 
Extension URI: http://wordbooker.tty.org.uk
Version: 2.1
Description: Advanced Options for the WordBooker Plugin
Author: Steve Atty
*/

// This is WP 2.8 specific
function wordbooker_option_init(){
	register_setting( 'wordbooker_options', 'wordbooker_settings','worbooker_validate_options');
}

function worbooker_validate_options($options) {
	global $user_ID;
	# Do they want to reset? If so we reset the options and let WordPress do the business for us!
	if (isset( $_POST["mcp"] )) {
	 wordbooker_poll_comments($user_ID);
	}
	if (isset( $_POST["RSD"] ))  {
		$options["wordbooker_default_author"]=0;
		$options["wordbooker_republish_time_frame"]=10;
		$options["wordbooker_attribute"]= __("Posted a new post on their blog", 'wordbooker');
		$options["wordbooker_status_update_text"]= __(": New blog post :  %title% - %link%", 'wordbooker');
		$options["wordbooker_actionlink"]=300;
		$options['wordbooker_orandpage']=2;
		$options['wordbooker_extract_length']=256;
		$options['wordbooker_page_post']=-100;
		$options['wordbooker_page_post']=-100;
		$options["wordbooker_publish_default"]=null;
		$options["wordbooker_republish_time_obey"]=null;
		$options["wordbooker_publish_override"]=null;
		$options["wordbooker_status_update"]=null;
		$options["wordbooker_search_this_header"]=null;
		$options["wordbooker_comment_approve"]=null;
		$options["wordbooker_comment_push"]=null;
		$options["wordbooker_comment_pull"]=null;
		$options["wordbooker_comment_put"]=null;
		$options["wordbooker_comment_get"]=null;
		$options["wordbooker_comment_poll"]=null;
		$options["wordbooker_publish_no_user"]=null;
		$options["wordbooker_advanced_diagnostics"]=null;
		$options["wordbooker_like_button"]=null;
		$options['wordbooker_description_meta_length']=350;
		$options['wordbooker_like_width']=250;
		$options['wordbooker_advanced_diagnostics_level']=99;
		$options['wordbooker_comment_email']=get_bloginfo( 'admin_email' );
		$options["wordbooker_meta_tag_scan"]="image,thumb,Thumbnail";
	}
	return $options;
}

function wbs_is_hash_valid($form_hash) {
	$ret = false;
	$saved_hash = wbs_retrieve_hash();
	if ($form_hash === $saved_hash) {
		$ret = true;
	}
	return $ret;
}

function wbs_generate_hash() {
	return md5(uniqid(rand(), TRUE));
}

function wbs_store_hash($generated_hash) {
	return update_option('wordbooker_token',$generated_hash,'WordBooker Security Hash');
}

function wbs_retrieve_hash() {
	$ret = get_option('wordbooker_token');
	return $ret;
}


function wordbooker_option_manager() {
	global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id,$wordbooker_hook;
	echo '<div class="wrap">';
	echo '<h2>'.WORDBOOKER_APPLICATION_NAME." ".__('Options Page','wordbooker').' </h2>';
	if ( isset ($_POST["reset_user_config"])) {wordbooker_delete_userdata(); }
	$wordbooker_settings=wordbooker_options();
	if ( isset($wordbooker_settings['wordbooker_disabled'])) { echo "<div align='center'><b> ".__('WARNING : Wordbooker is DISABLED','wordbooker')."</b></div>";} else {
	if ( isset($wordbooker_settings['wordbooker_fake_publish'])) { echo "<div align='center'><b> ".__('WARNING : Wordbooker is in TEST mode - NO Posts will be made to Facebook','wordbooker')."</b></div>";}}
	if ($wordbooker_settings['wordbooker_comment_cron']!=wp_get_schedule('wb_comment_job')) {
	$dummy=wp_clear_scheduled_hook('wb_comment_job');
	$sql="Delete from ".WORDBOOKER_POSTCOMMENTS." where in_out='stat'";
	$wpdb->query($sql);
	if ( ($wordbooker_settings['wordbooker_comment_cron']=='Never') || ($wordbooker_settings['wordbooker_comment_cron']=='Manual')){} else {
	$dummy=wp_schedule_event(time(), $wordbooker_settings['wordbooker_comment_cron'], 'wb_comment_job');}
	} 
	//Set some defaults:
	# If the closedboxes are not set then lets set them up - General Options open, all the rest closed
	$wordbooker_settings=wordbooker_options();
	$wb_boxes=get_usermeta($user_ID,'closedpostboxes_settings_page_wordbooker');
	if (count($wb_boxes)==0) {
		$wb_boxes[0]='wb_opt2';
		$wb_boxes[1]='wb_opt3';
		$wb_boxes[2]='wb_opt4';
		update_user_meta($user_ID,'closedpostboxes_settings_page_wordbooker',$wb_boxes);
	}

	if (! wbs_retrieve_hash()) {
		$temp_hash = wbs_generate_hash();
		wbs_store_hash($temp_hash);
	}

	#var_dump($wordbooker_settings);
	// If no default author set, lets set it
	if (! isset($wordbooker_settings["wordbooker_default_author"])){ $wordbooker_settings["wordbooker_default_author"]=0;}
	// If no attribute set, then set it.
	if (! isset($wordbooker_settings["wordbooker_attribute"])){ $wordbooker_settings["wordbooker_attribute"]= __("Posted a new post on their blog", 'wordbooker');}
	// If no Status line text, then set it 
	if (! isset($wordbooker_settings["wordbooker_status_update_text"])){ $wordbooker_settings["wordbooker_status_update_text"]= __(": New blog post :  %title% - %link%", 'wordbooker');}
	// No Share link set, then set it
	if (! isset($wordbooker_settings["wordbooker_actionlink"])){ $wordbooker_settings["wordbooker_actionlink"]=300;}
	// No extract length
 	if (! isset($wordbooker_settings['wordbooker_extract_length'])) {$wordbooker_settings['wordbooker_extract_length']=256;}
 	if (! isset($wordbooker_settings['wordbooker_page_post'])) {$wordbooker_settings['wordbooker_page_post']=-100;}
	if (! isset($wordbooker_settings['wordbooker_advanced_diagnostics_level'])) {$wordbooker_settings['wordbooker_advanced_diagnostics_level']=99;}
	 // Generate meta Description
	if (! isset($wordbooker_settings['wordbooker_description_meta_length'])) {$wordbooker_settings['wordbooker_description_meta_length']='350';}
	if (! isset($wordbooker_settings['wordbooker_meta_tag_scan'])) {$wordbooker_settings['wordbooker_meta_tag_scan']="image,thumb,Thumbnail";}

	// Now lets write those setting back.;
	wordbooker_set_options($wordbooker_settings);
	$wordbooker_user_settings_id="wordbookuser".$blog_id;

	echo '<div class="wrap">';
	if(isset($_POST['user_meta'])) {
		// Now we check the hash, to make sure we are not getting CSRF
		if(wbs_is_hash_valid($_POST['token'])) {
			foreach(array_keys($_POST) as $key) {
				if (substr($key,0,8)=='wordbook') {
				$wordbookeruser_settings[$key]=$_POST[$key];
					}
			}
			$encoded_setings=$wordbookeruser_settings;
			#$wordbooker_user_settings_id="wordbookuser".$blog_id;
			update_usermeta( $user_ID, $wordbooker_user_settings_id, $encoded_setings );
			if (isset($_POST['rwbus'])) {delete_usermeta( $user_ID, $wordbooker_user_settings_id );$ol_flash =  __("Your user level settings have been reset.", 'wordbooker');} else {
	       		$ol_flash =  __("Your user level settings have been saved.", 'wordbooker'); }
		} else {
			// Invalid form hash, possible CSRF attempt
			$ol_flash =  __("Security hash missing.", 'wordbooker');
		} // endif wbs_is_hash_valid
	}  // end if user_meta check.


	if ($ol_flash != '') echo '<div id="message" class="updated fade"><p>' . $ol_flash . '</p></div>';
	
	wordbooker_option_notices();

	$sql="select user_ID from ".WORDBOOKER_USERDATA." where user_ID=".$user_ID;
	$result = $wpdb->get_results($sql);
	# We need to put a check in here to stop this crapping out if there is no user id - so flag no row returned 
	$got_id=0;
	 if ( isset($result[0]->user_ID)) { 
		$wbuser = wordbooker_get_userdata($result[0]->user_ID);
 		if ($wbuser->access_token) { $got_id=1;}
	}

	if ($got_id==1) {
		wordbooker_update_userdata($wbuser);
		$checked_flag=array('on'=>'checked','off'=>'');

		# Populate  the cache table for this user if its not there.
		$result = $wpdb->get_row("select facebook_id from ".WORDBOOKER_USERDATA." where user_id=".$user_ID);
		if (strlen($result->facebook_id)<4) {
			wordbooker_cache_refresh($user_ID,$fbclient);
 		}
		# If the user saved their config after setting permissions or chose to refresh the cache then lets refresh the cache
		if ( isset ($_POST["perm_save"])) { wordbooker_cache_refresh($user_ID,$fbclient); }

function wordbooker_blog_level_options() {
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id,$wordbooker_hook;

		add_meta_box('wb_opt1', __('General Posting Options','wordbooker'),  'wordbooker_blog_posting_options', $wordbooker_hook, 'normal', 'core');
		add_meta_box('wb_opt2', __('Facebook Like and Share Options','wordbooker'),   'wordbooker_blog_facebook_options', $wordbooker_hook, 'normal', 'core');
		add_meta_box('wb_opt3', __('Comment Handling Options', 'wordbooker'),  'wordbooker_blog_comment_options', $wordbooker_hook, 'normal', 'core');
		add_meta_box('wb_opt4', __('Advanced Options','wordbooker'),   'wordbooker_blog_advanced_options', $wordbooker_hook, 'normal', 'core');

		echo'<p><hr><h3>';
		_e('Blog Level Settings', 'wordbooker');
		echo'</h3><form action="options.php" method="post" action="">';
		wp_nonce_field('wordbooker_bl_options'); 
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); 
		settings_fields('wordbooker_options');
		echo '<input type="hidden" name="wordbooker_settings[schema_vers]" value='.$wordbooker_settings['schema_vers'].' />';


?>
<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
<div id="side-info-column" class="inner-sidebar">
<?php# do_meta_boxes($wordbooker_hook, 'side', $data); ?>
</div>
<div id="post-body" class="has-sidebar">
<div id="post-body-content" class="has-sidebar-content">
	<?php do_meta_boxes($wordbooker_hook, 'normal', $data); ?>
</div>
</div>
<br class="clear"/>
			
</div>	
<?php
		if (current_user_can('activate_plugins')) {echo '<input type="submit" name="SBLO" value="'.__("Save Blog Level Options", 'wordbooker').'" class="button-primary"  />&nbsp;&nbsp;&nbsp;<input type="submit" name="RSD" value="'.__("Reset to system Defaults", 'wordbooker').'" class="button-primary" action="poo" />';}
		echo '</p></form><br /></div><hr>';
}

function wordbooker_blog_posting_options() {
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id;
		$checked_flag=array('on'=>'checked','off'=>'');
		$sql="select wpu.ID,wpu.display_name from $wpdb->users wpu,".WORDBOOKER_USERDATA." wud where wpu.ID=wud.user_id;";
		$wb_users = $wpdb->get_results($sql); 
		if(!isset($wordbooker_settings['wordbooker_comment_email'])) {$wordbooker_settings['wordbooker_comment_email']=get_bloginfo( 'admin_email' );}
		## Make it so that the drop down includes "Current logged in user" We know now that they have to have an account now as I've changed the code.

		echo '<label for="wb_publish_post_default">'.__("Default Publish Post to Facebook", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_publish_post_default]" '.$checked_flag[$wordbooker_settings["wordbooker_publish_post_default"]].' ><br />';
		
		echo '<label for="wb_publish_page_default">'.__("Default Publish Page to Facebook", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_publish_page_default]" '.$checked_flag[$wordbooker_settings["wordbooker_publish_page_default"]].' ><br />';

		_e( 'Unless changed, Posts will be published on the Facebook belonging to :', 'wordbooker');
		echo '<select name="wordbooker_settings[wordbooker_default_author]" ><option value=0>' ;
		 _e('Current Logged in user', 'wordbooker');
		echo '&nbsp;</option>';
		$option="";
  		foreach ($wb_users as $wb_user) {	
			if ($wb_user->ID==$wordbooker_settings["wordbooker_default_author"] ) {$option .= '<option selected="yes" value='.$wb_user->ID.'>';} else {
        		$option .= '<option value='.$wb_user->ID.'>';}
        		$option .= $wb_user->display_name;
        		$option .= '</option>';
		}
		echo $option;
		echo '</select><br />';

		echo '<label for="wb_publish_no_user">'.__("Publish Posts by non Wordbooker users", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_publish_no_user]" '.$checked_flag[$wordbooker_settings["wordbooker_publish_no_user"]].' ><br />';

		echo '<label for="wb_publish_user_publish">'.__("Allow non Wordbooker users to chose to publish a post", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_allow_publish_select]" '.$checked_flag[$wordbooker_settings["wordbooker_allow_publish_select"]].' ><br />';

                echo '<label for="wb_extract_length">'.__('Length of Extract', 'wordbooker').' :</label> <select id="wordbooker_extract_length" name="wordbooker_settings[wordbooker_extract_length]"  >';
	        $arr = array(10=> "10",20=> "20",50=> "50",100=> "100",120=> "120",150=> "150",175=> "175",200=> "200",  250=> "250", 256=>__("256 (Default) ", 'wordbooker'), 270=>"270", 300=>"300", 350 => "350",400 => "400",500 => "500",600 => "600",700 => "700",800 => "800",900 => "900");
                foreach ($arr as $i => $value) {
                        if ($i==$wordbooker_settings['wordbooker_extract_length']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}}
                echo "</select><br />";

		echo '<label for="wb_attribute">'.__("Post Attribute", 'wordbooker'). ' : </label>';
		echo '<INPUT NAME="wordbooker_settings[wordbooker_attribute]" size=60 maxlength=240 value="'.stripslashes($wordbooker_settings["wordbooker_attribute"]).'"><br />';
		echo '<label for="wb_status_update">'.__("Facebook Status Attribute", 'wordbooker'). ' :</label>';
		echo' <INPUT NAME="wordbooker_settings[wordbooker_status_update_text]" size=60 maxlength=60 value="'.stripslashes($wordbooker_settings["wordbooker_status_update_text"]).'"> ';

		echo '<br /><label for="wb_action_link">'.__("Action Link Option ", 'wordbooker'). ': </label><select id="wordbooker_actionlink" name="wordbooker_settings[wordbooker_actionlink]"  >';	
      		 $arr = array(100=> "None ",  200=> __("Share Link ", 'wordbooker'), 300=>__("Read Full Article", 'wordbooker'));
                foreach ($arr as $i => $value) {
                        if ($i==$wordbooker_settings['wordbooker_actionlink']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}}
                echo "</select><br />";

		echo '<label for="wordbooker_search_this_header">'.__("Enable Extended description for Share Link", 'wordbooker'). ' :</label> ';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_search_this_header]" '.$checked_flag[$wordbooker_settings["wordbooker_search_this_header"]].' /><br />';
			echo '<label for="wb_description_meta_length">'.__('Length of Description Meta-Tag', 'wordbooker').' :</label> <select id="wordbooker_description_meta_length" name="wordbooker_settings[wordbooker_description_meta_length]" >';
		$arr = array(0=> "Disable", 150=> "150", 350=>"350 (Default)");
		foreach ($arr as $i => $value) {
		if ($i==$wordbooker_settings['wordbooker_description_meta_length']){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
		else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}}
		echo "</select><br />";


}

function wordbooker_blog_facebook_options() {
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id;
		$fblike_action=array('recommend'=>'Recommend ','like'=>'Like ');
		$fblike_colorscheme=array('dark'=>'Dark','light'=>'Light');
		$fblike_font=array('arial'=>'Arial','lucida grande'=>'Lucida grande ','segoe ui'=>'Segoe ui','tahoma'=>'Tahoma','trebuchet ms'=>'Trebuchet ms ','verdana'=>'Verdana');
		$fblike_button=array('button_count'=>'Button Count ','standard'=>'Standard ','box_count'=>'Box Count');
		$fblike_faces=array('false'=>__('No','wordbooker'),'true'=>__('Yes','wordbooker'));
		$fblike_location=array('top'=>__('Above Post ','wordbooker'),'bottom'=>__('Below Post','wordbooker'),'coded'=>__('Defined by theme template','wordbooker'),'tagged'=>__('Defined by Tag in post','wordbooker'));
		$checked_flag=array('on'=>'checked','off'=>'');
		$fblike_send=array('false'=>__('No','wordbooker'),'true'=>__('Yes','wordbooker'));
		$fblike_send_combi=array('false'=>__('No - use Send instead of Like','wordbooker'),'true'=>__('Yes - use both Like and Send','wordbooker'));
		echo '<label for="wb_facebook_like">'.__("Include a Facebook Like button in blog", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_button_show]" '.$checked_flag[$wordbooker_settings["wordbooker_like_button_show"]].' ><br />';
		echo '<label for="wb_facebook_like_front">&nbsp;'.__("Show Facebook Like button on front page", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_button_frontpage]" '.$checked_flag[$wordbooker_settings["wordbooker_like_button_frontpage"]].' ><br />';

		echo '<label for="wb_facebook_like_front">&nbsp;'.__("Show Facebook Like button on Category pages", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_button_category]" '.$checked_flag[$wordbooker_settings["wordbooker_like_button_category"]].' ><br />';

		echo '<label for="wb_facebook_like_page">&nbsp;'.__("Show Facebook Like button on Pages", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_button_page]" '.$checked_flag[$wordbooker_settings["wordbooker_like_button_page"]].' ><br />';

		echo '<label for="wb_facebook_like">&nbsp;'.__("Show Facebook Like button in each post", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_button_post]" '.$checked_flag[$wordbooker_settings["wordbooker_like_button_post"]].' ><br />';

		echo '<label for="wb_facebook_like">&nbsp;'.__("Don't show Facebook Like / Send Button on Sticky Posts", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_no_like_stick]" '.$checked_flag[$wordbooker_settings["wordbooker_no_like_stick"]].' ><br />';


			if (!is_numeric($wordbooker_settings['wordbooker_like_width']) || $wordbooker_settings['wordbooker_like_width'] <0) {$wordbooker_settings['wordbooker_like_width']=250;}
		echo '<label for="wb_facebook_like_width">&nbsp;'.__("Width of Facebook Like box", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=text NAME="wordbooker_settings[wordbooker_like_width]"  size="7"value="'.$wordbooker_settings["wordbooker_like_width"].'" ><br />';

		echo '<label for="wb_fblike_location">&nbsp;'.__('Facebook Like - Display Button ', 'wordbooker').' :</label> <select id="wordbooker_fblike_location" name="wordbooker_settings[wordbooker_fblike_location]"  >';
		foreach ($fblike_location as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_location']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_location[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_location[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_fblike_action">&nbsp;'.__('Facebook Like - Verb to Display', 'wordbooker').' :</label> <select id="wordbooker_fblike_action" name="wordbooker_settings[wordbooker_fblike_action]"  >';
		foreach ($fblike_action as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_action']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_action[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_action[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_fblike_colorscheme">&nbsp;'.__('Facebook Like - Colour Scheme', 'wordbooker').' :</label> <select id="wordbooker_fblike_colorscheme" name="wordbooker_settings[wordbooker_fblike_colorscheme]"  >';
		foreach ($fblike_colorscheme as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_colorscheme']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_colorscheme[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_colorscheme[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_fblike_font">&nbsp;'.__('Facebook Like - Display Font', 'wordbooker').' :</label> <select id="wordbooker_fblike_font" name="wordbooker_settings[wordbooker_fblike_font]"  >';
		foreach ($fblike_font as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_font']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_font[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_font[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_fblike_button">&nbsp;'.__('Facebook Like - Layout Style', 'wordbooker').' :</label> <select id="wordbooker_fblike_button" name="wordbooker_settings[wordbooker_fblike_button]"  >';
		foreach ($fblike_button as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_button']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_button[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_button[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_fblike_faces">&nbsp;'.__('Facebook Like - Display Faces (Standard layout only)', 'wordbooker').' :</label> <select id="wordbooker_fblike_faces" name="wordbooker_settings[wordbooker_fblike_faces]"  >';
		foreach ($fblike_faces as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_faces']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_faces[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_faces[$i].'</option>';}}
		echo "</select><br />";


		echo '<label for="wb_fblike_send_combi">&nbsp;'.__('Combine Send with Like', 'wordbooker').' :</label> <select id="wordbooker_fblike_send_combi" name="wordbooker_settings[wordbooker_fblike_send_combi]"  >';
		foreach ($fblike_send_combi as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_send_combi']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_send_combi[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_send_combi[$i].'</option>';}}
		echo "</select><br/> ";

		echo '<label for="wb_fblike_send">&nbsp;'.__('Facebook Send - Display Button', 'wordbooker').' :</label> <select id="wordbooker_fblike_send" name="wordbooker_settings[wordbooker_fblike_send]"  >';
		foreach ($fblike_send as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fblike_send']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_send[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_send[$i].'</option>';}}
		echo "</select><br /><hr><br />";

	
		echo '<label for="wb_facebook_like_share">'.__("Include a Facebook Share button in blog", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_like_share_too]" '.$checked_flag[$wordbooker_settings["wordbooker_like_share_too"]].' ><br />';


		echo '<label for="wb_fbshare_location">&nbsp;'.__('Facebook Share - Display Button ', 'wordbooker').' :</label> <select id="wordbooker_fbshare_location" name="wordbooker_settings[wordbooker_fbshare_location]"  >';
		foreach ($fblike_location as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fbshare_location']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_location[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_location[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_facebook_share_front">&nbsp;'.__("Show Facebook Share button on front page", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_share_button_frontpage]" '.$checked_flag[$wordbooker_settings["wordbooker_share_button_frontpage"]].' ><br />';

		echo '<label for="wb_facebook_share_front">&nbsp;'.__("Show Facebook Share button on Category pages", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_share_button_category]" '.$checked_flag[$wordbooker_settings["wordbooker_share_button_category"]].' ><br />';

		echo '<label for="wb_facebook_share_page">&nbsp;'.__("Show Facebook Share button on Pages", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_share_button_page]" '.$checked_flag[$wordbooker_settings["wordbooker_share_button_page"]].' ><br />';

		echo '<label for="wb_facebook_share_post">&nbsp;'.__("Show Facebook Share button in each post", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_share_button_post]" '.$checked_flag[$wordbooker_settings["wordbooker_share_button_post"]].' ><br />';
		echo '<label for="wb_facebook_like">&nbsp;'.__("Don't show Facebook Share button on Sticky Posts", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_no_share_stick]" '.$checked_flag[$wordbooker_settings["wordbooker_no_share_stick"]].' ><br />';
		echo "<hr><br />";
		echo '<label for="wb_facebook_gravatars">'.__("Do not replace Gravtars with Facebook Photos", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_no_facebook_gravatars]" '.$checked_flag[$wordbooker_settings["wordbooker_no_facebook_gravatars"]].' ><br />';
/*
		echo "<hr><br />";
		echo '<label for="wb_facebook_time">'.__("Use Frictionless sharing / Timeline instead of Share", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_time_button]" '.$checked_flag[$wordbooker_settings["wordbooker_time_button"]].' ><br />';

		echo '<label for="wb_fbread_location">&nbsp;'.__('Facebook Read - Display Button ', 'wordbooker').' :</label> <select id="wordbooker_fbread_location" name="wordbooker_settings[wordbooker_fbread_location]"  >';
		foreach ($fblike_location as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_fbread_location']){ print '<option selected="yes" value="'.$i.'" >'.$fblike_location[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fblike_location[$i].'</option>';}}
		echo "</select><br />";
*/


}

function wordbooker_blog_comment_options() {
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id;
		$checked_flag=array('on'=>'checked','off'=>'');
		$fbcomment_colorscheme=array('dark'=>'Dark','light'=>'Light');

		echo "<b>".__('Wordpress Comment handling Options','wordbooker')."</b><br />";
		$scheds1['Never'] = array('interval'   => 999999999,'display'   => __('Never ', 'wordbooker'),);
		$scheds1['Manual'] = array('interval'   => 999999999,'display'   => __('Manual Polling ', 'wordbooker'),);
		$scheds2=wp_get_schedules();
		$scheds=array_merge($scheds1,$scheds2);

		echo '<label for="wb_comment_cron">'.__('Process Comments  ', 'wordbooker').' :</label> <select id="wordbooker_comment_cron" name="wordbooker_settings[wordbooker_comment_cron]"  >';
		foreach(array_keys($scheds) as $ss) {
			if ($ss==$wordbooker_settings['wordbooker_comment_cron']){ print '<option selected="yes" value="'.$ss.'" >'.$scheds[$ss]['display'].'&nbsp;</option>';}
		       else {print '<option value="'.$ss.'" >'.$scheds[$ss]['display'].'&nbsp;</option>';}}
		echo "</select> ";
		if ($wordbooker_settings['wordbooker_comment_cron']!='Never'){echo" &nbsp;&nbsp;(".__("Next Scheduled fetch is in", 'wordbooker').' : '.date('H:i',(wp_next_scheduled('wb_comment_job') - time ())).')';}
		echo '<br /><label for="wb_publish_comment_handling">'.__("Enable Comment processing", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_handling]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_handling"]].' /><br />';
		echo '<label for="wb_import_comment">&nbsp;&nbsp;'.__("Disable Comment Importing", 'wordbooker'). ': </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_pul]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_pull"]]. '/> <br />';
		echo '<label for="wb_import_comment">&nbsp;&nbsp;'.__("Disable Comment Exporting", 'wordbooker'). ': </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_push]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_push"]]. '/> <br />';
		if (!isset($wordbooker_settings["wordbooker_comment_attribute"])) {
		$wordbooker_settings["wordbooker_comment_attribute"]=__("[Comment imported from blog]",'wordbooker');
		}
		echo '<label for="wb_cooment_attribute">'.__('Comment Tag', 'wordbooker').' : </label>';
		echo '<INPUT NAME="wordbooker_settings[wordbooker_comment_attribute]" size=60 maxlength=240 value="'.stripslashes($wordbooker_settings["wordbooker_comment_attribute"]).'"><br />';
		if(strlen($wordbooker_settings['wordbooker_comment_post_format'])<2) {$wordbooker_settings['wordbooker_comment_post_format']="%tag%";}
		echo '<p class="DataForm"><label for="wb_cooment_post_format">'.__('Facebook Comment Structure', 'wordbooker').' : </label>';
echo "<TEXTAREA NAME='wordbooker_settings[wordbooker_comment_post_format]' ROWS=8 COLS=60>".stripslashes($wordbooker_settings["wordbooker_comment_post_format"])."</TEXTAREA><br /></p>" ;
		echo '<label for="wb_comment_email">'.__("Assign this email address to comments", 'wordbooker'). ' :</label>';
		echo' <INPUT NAME="wordbooker_settings[wordbooker_comment_email]" size=60 maxlength=60 value="'.stripslashes($wordbooker_settings["wordbooker_comment_email"]).'"> <br />';
		echo '<label for="wb_import_comment">'.__("Import Comments from Facebook for new Wordbooker Posts", 'wordbooker'). ': </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_get]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_get"]]. '/> <br />';
		echo '<label for="wb_publish_comment_push">'.__("Push Comments up to Facebook for new posts", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_put]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_put"]].' /> <br />  ';
		echo '<label for="wb_publish_comment_approve">'.__("Auto Approve imported comments", 'wordbooker'). ' :</label> ';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_comment_approve]" '.$checked_flag[$wordbooker_settings["wordbooker_comment_approve"]].' /><br />';
		if ($wordbooker_settings['wordbooker_comment_cron']!='Never') {
		echo '<br /><input type="submit" value="'.__("Run Comment Handling Now", 'wordbooker').'" name="mcp" class="button-primary"  />';
		}

		echo "<hr><br /><b>".__('Facebook Comment Box Options','wordbooker')."</b><br /><br />";
		echo '<label for="wb_use_fb_comments">'.__("Enable Facebook Comment handling  ", 'wordbooker'). ' : </label> ';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_use_fb_comments]" '.$checked_flag[$wordbooker_settings["wordbooker_use_fb_comments"]].' /> <br />';
		$fb_comment_location=array('bottom'=>__('Created in line below post','wordbooker'),'coded'=>__('Defined by theme template','wordbooker'),'tagged'=>__('Defined by Tag in post','wordbooker'));

		echo '<label for="wb_comment_location">'.__('Comment placing ', 'wordbooker').' :</label> <select id="wordbooker_comment_location" name="wordbooker_settings[wordbooker_comment_location]"  >';
		foreach ($fb_comment_location as $i => $value) {
			if ($i==$wordbooker_settings['wordbooker_comment_location']){ print '<option selected="yes" value="'.$i.'" >'.$fb_comment_location[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fb_comment_location[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_comment_colorscheme">'.__('Comment Box - Colour Scheme', 'wordbooker').' :</label> <select id="wb_comment_colorscheme" name="wordbooker_settings[wb_comment_colorscheme]"  >';
		foreach ($fbcomment_colorscheme as $i => $value) {
			if ($i==$wordbooker_settings['wb_comment_colorscheme']){ print '<option selected="yes" value="'.$i.'" >'.$fbcomment_colorscheme[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$fbcomment_colorscheme[$i].'</option>';}}
		echo "</select><br />";

		echo '<label for="wb_use_fb_comments_admin">'.__("All Wordbooker users can moderate comments  ", 'wordbooker'). ' : </label> ';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_fb_comments_admin]" '.$checked_flag[$wordbooker_settings["wordbooker_fb_comments_admin"]].' />
 <br />';
		
		if (!defined('WORDBOOKER_PREMIUM') || (defined('WORDBOOKER_MULTI') && WORDBOOKER_MULTI==true)) {
		echo '<label for="fb_comment_app_id">'.__("Use this Facebook Application ID for comment moderation", 'wordbooker'). ' :</label>';
		echo' <INPUT NAME="wordbooker_settings[fb_comment_app_id]" size=20 maxlength=20 value="'.stripslashes($wordbooker_settings["fb_comment_app_id"]).'"> <br />';
		}

		echo '<label for="fb_comment_app_id">'.__("Width of comment box", 'wordbooker'). ' :</label>';
		if (strlen($wordbooker_settings["fb_comment_box_size"])<=2) {$wordbooker_settings["fb_comment_box_size"]=350;}
		if ($wordbooker_settings["fb_comment_box_size"]<350) {$wordbooker_settings["fb_comment_box_size"]=350;}
		echo' <INPUT NAME="wordbooker_settings[fb_comment_box_size]" size=3 maxlength=3 value="'.stripslashes($wordbooker_settings["fb_comment_box_size"]).'"> <br />';

		echo '<label for="fb_comment_count">'.__("Number of comments to display", 'wordbooker'). ' :</label>';
		if (strlen($wordbooker_settings["fb_comment_box_count"])<1) {$wordbooker_settings["fb_comment_box_count"]=20;}
		if ($wordbooker_settings["fb_comment_box_count"]<2) {$wordbooker_settings["fb_comment_box_count"]=2;}
		echo' <INPUT NAME="wordbooker_settings[fb_comment_box_count]" size=3 maxlength=3 value="'.stripslashes($wordbooker_settings["fb_comment_box_count"]).'"> <br />';

		echo '<label for="wordbooker_comment_notify">'.__('Enable notification of new comments', 'wordbooker').' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[fb_comment_box_notify]" '.$checked_flag[$wordbooker_settings["fb_comment_box_notify"]].'><br />';

		echo '<label for="wordbooker_use_facebook_comments">'.__('Facebook comment handling should be enabled on new posts', 'wordbooker').' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_use_facebook_comments]" '.$checked_flag[$wordbooker_settings["wordbooker_use_facebook_comments"]].'><br />';
		
}


function wordbooker_blog_advanced_options() {
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id,$table_prefix;
		$checked_flag=array('on'=>'checked','off'=>'');
		if (intval(WORDBOOKER_WP_VERSION)>30){
		$admin_users=get_users(array('role'=>'administrator'));;
		echo '<label for="wordbooker_diagnostic admin">'.__('User who should get Admin level diagnostics', 'wordbooker').' :</label> <select id="wordbooker_diagnostic_admin" name="wordbooker_settings[wordbooker_diagnostic_admin]"  >';
		foreach ($admin_users as $adminuser) {
			if ($adminuser->ID==$wordbooker_settings['wordbooker_diagnostic_admin']){ print '<option selected="yes" value="'.$adminuser->ID.'" >'.$adminuser->display_name.' ( '.$adminuser->user_login.' ) </option>';}
		       else {print '<option value="'.$adminuser->ID.'" >'.$adminuser->display_name.' ( '.$adminuser->user_login.' ) </option>';}}
		echo "</select><br />";
		}
		if (!isset($wordbooker_settings['wordbooker_advanced_diagnostics_level'])) {$wordbooker_settings['wordbooker_advanced_diagnostics_level']=10;}
		$arr = array(0=> __("Show Everything and I mean everything",'wordbooker'),10=> __("Show everything but Cache Diagnostics",'wordbooker'),90=> __("Show result of major actions",'wordbooker'),99 => __("Don't show anything apart from Fatal errors",'wordbooker'),999 => __("Disabled (Show nothing at all)",'wordbooker'));
		echo '<p><label for="wb_advanced_diagnostics_level">'.__("Post Diagnostics display level", 'wordbooker'). ' : </label><select id="wordbooker_advanced_diagnostics_level" name="wordbooker_settings[wordbooker_advanced_diagnostics_level]"  >';
         foreach ($arr as $i => $value) {
                        if ($i==$wordbooker_settings['wordbooker_advanced_diagnostics_level']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br /></P><p>";
		echo '<label for="wb_wordbooker_diag_clear">'.__("Clear detailed diagnostics on successful post", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_clear_diagnostics]" '.$checked_flag[$wordbooker_settings["wordbooker_clear_diagnostics"]].' ></P><p>';
		echo '<label for="wb_wordbooker_disable_shorties">'.__("Disable the use of short URLs in links posted to Facebook", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_disable_shorties]" '.$checked_flag[$wordbooker_settings["wordbooker_disable_shorties"]].' ></P><p>';

		echo '<label for="wb_wordbooker_fb_rec_act">'.__("Include FB Recent activity on Wordbooker Options page", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_fb_rec_act]" '.$checked_flag[$wordbooker_settings["wordbooker_fb_rec_act"]].' ></P><p>';

		echo '<label for="wb_facebook_iframe">'.__("Use Iframes instead of FBXML to render FB features", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_iframe]" '.$checked_flag[$wordbooker_settings["wordbooker_iframe"]].' ></P><p>';

		echo '<label for="wordbooker_use_url_not_slug">'.__("Use Site URL not Blog Description in Wall Posts", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_use_url_not_slug]" '.$checked_flag[$wordbooker_settings["wordbooker_use_url_not_slug"]].' ></P><p>';
	
		echo '<label for="wb_meta_tag_scan">'.__("Check the following Custom Post Meta tags for images", 'wordbooker'). ' :</label>';
		echo' <INPUT NAME="wordbooker_settings[wordbooker_meta_tag_scan]" size=60 maxlength=129 value="'.stripslashes($wordbooker_settings["wordbooker_meta_tag_scan"]).'"/></P><p> ';

		echo '<label for="wb_meta_tag_thumb">'.__("Use Image from Custom Meta instead of Featured Image for Open Graph image", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_meta_tag_thumb]" '.$checked_flag[$wordbooker_settings["wordbooker_meta_tag_thumb"]].' /></P><p>';

		echo '<label for="wb_wordbooker_default_image">'.__("Default Open Graph image to use for posts", 'wordbooker'). ' :</label>';
		echo' <INPUT NAME="wordbooker_settings[wb_wordbooker_default_image]" size=60 maxlength=120 value="'.stripslashes($wordbooker_settings["wb_wordbooker_default_image"]).'"></P><p>';

		echo '<label for="wb_facebook_use_this_image">'.__("Use the above image instead of a blank for posts with no image", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_use_this_image]" '.$checked_flag[$wordbooker_settings["wordbooker_use_this_image"]].' ></P><p>';

		echo '<label for="wb_wordbooker_disable_ogtags">'.__("Disable in-line production of OpenGraph Tags", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_fb_disable_og]" '.$checked_flag[$wordbooker_settings["wordbooker_fb_disable_og"]].' ></P><p>';

		echo '<label for="wb_fake_publish">'.__("Only Pretend to Publish on Facebook - TEST MODE", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_fake_publish]" '.$checked_flag[$wordbooker_settings["wordbooker_fake_publish"]].' /></P><p>';
		echo '<label for="wb_disable">'.__("Disable ALL Wordbooker functionality", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_settings[wordbooker_disabled]" '.$checked_flag[$wordbooker_settings["wordbooker_disabled"]].' /></P>';

		echo '<input type="hidden" name="wordbooker_settings[wordbooker_page_post]" value="-100" />';
		echo '<input type="hidden" name="wordbooker_settings[wordbooker_orandpage]" value="2" />';


		
}
function wordbooker_user_level_options(){
		global $ol_flash, $wordbooker_settings, $_POST, $wp_rewrite,$user_ID,$wpdb, $blog_id,$wordbooker_user_settings_id,$user_ID,$wordbooker_hook;id;
		# USER LEVEL OPTIONS
		$checked_flag=array('on'=>'checked','off'=>'');
		$wordbookeruser_settings=get_usermeta($user_ID,$wordbooker_user_settings_id);
		# Set a couple of options that we really need.
		if( !isset($wordbookeruser_settings['wordbooker_orandpage'])) {$wordbookeruser_settings['wordbooker_orandpage']=2;}
		if( !isset($wordbookeruser_settings['wordbooker_publish_default'])) {$wordbookeruser_settings['wordbooker_publish_default']=$wordbooker_settings['wordbooker_publish_default'];}

		echo '<div class="wrap">';
		echo '<h3>'.__('User Level Settings', 'wordbooker').'</h3>';
		_e("If set, these options will override the Blog Level options for this user", 'wordbooker');

		echo '<br /><br /><form action="" method="post">';
		echo '<input type="hidden" name="token" value="' . wbs_retrieve_hash() . '" />';
		echo '<input type="hidden" name="user_meta" value="true" />';
		wp_nonce_field('wordbooker_ul_options'); 
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false );
		echo '<label for="wb_publish_post_default">'.__('Default Publish Post to Facebook', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_publish_post_default" name="wordbooker_publish_post_default"  >';	
      		 $arr = array(0=> __("Same as Blog", 'wordbooker'), 100=> __("No", 'wordbooker'),  200=> ("Yes"));
                foreach ($arr as $i => $value) {
                        if ($i==$wordbookeruser_settings['wordbooker_publish_post_default']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br />";

		echo '<label for="wb_publish_page_default">'.__('Default Publish Page to Facebook', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_publish_page_default" name="wordbooker_publish_page_default"  >';	
      		 $arr = array(0=> __("Same as Blog", 'wordbooker'), 100=> __("No", 'wordbooker'),  200=> ("Yes"));
                foreach ($arr as $i => $value) {
                        if ($i==$wordbookeruser_settings['wordbooker_publish_page_default']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br />";


		echo '<input type="hidden" name="wordbooker_page_post" value="-100" />';
		echo '<input type="hidden" name="wordbooker_orandpage" value="2" />';
		# Get the list of pages this user is an admin for
	
		$result = $wpdb->get_row("select pages from ".WORDBOOKER_USERDATA." where user_id=".$user_ID);
		$fanpages=unserialize($result->pages);
		$sql="select wpu.ID,wpu.display_name,facebook_id from $wpdb->users wpu,".WORDBOOKER_USERDATA." wud where wpu.ID=wud.user_id and wud.user_id=".$user_ID;
		$wb_users = $wpdb->get_results($sql);
		$fanpages2=$fanpages;
		$fanpages[]=array( 'id'=>'PW:'.$wb_users[0]->facebook_id, 'name'=>"Personal Wall");
		if(!isset ($wordbookeruser_settings["wordbooker_primary_target"])) { $wordbookeruser_settings["wordbooker_primary_target"]='PW:'.$wb_users[0]->facebook_id;}
		$have_fan_pages=0;
		if (count($fanpages)>1){
	echo '<p><label for="wb_primary_target">'.__('Post to the following Wall', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_primary_target" name="wordbooker_primary_target"  >';
				$option="";
			foreach ($fanpages as $fan_page) {
				if (strlen($fan_page['name'])>=2) {
				if ($fan_page['id']==$wordbookeruser_settings["wordbooker_primary_target"] ) {$option .= '<option selected="yes" value='.$fan_page['id'].'>';} else { $option .= '<option value='.$fan_page[id].'>';}
				$option .= $fan_page['name']." (".substr($fan_page['id'],3).")&nbsp;&nbsp;";
				$option .= '</option>';
				}
			}
			echo $option;
			echo '</select> &nbsp;';
		$arr = array(1=> __("As a Wall Post", 'wordbooker'),  2=> __("As a Note", 'wordbooker'), 3=> __("As a Status Update" , 'wordbooker'), 4=> __("As a Link" , 'wordbooker')   );
	echo '<select id="wordbooker_primary_type" name="wordbooker_primary_type"  >';
	foreach ($arr as $i => $value) {
       		 if ($i==$wordbookeruser_settings['wordbooker_primary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
	}
	echo '</select>	&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_primary_active" '.$checked_flag[$wordbookeruser_settings["wordbooker_primary_active"]].'></p><p>';

	} else 

	{
	echo '<p><label for="wb_primary_target">'.__('Post to my Personal Wall', 'wordbooker').' : </label> ';
	echo '<input type="hidden" name="wordbooker_primary_target" value="PW:'.$wb_users[0]->facebook_id.'" />';

	echo '<select id="wordbooker_primary_type" name="wordbooker_primary_type"  >';
	foreach ($arr as $i => $value) {
       		 if ($i==$wordbookeruser_settings['wordbooker_primary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
	}
	echo '&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_primary_active" '.$checked_flag[$wordbookeruser_settings["wordbooker_primary_active"]].'></p><p>';
	}
		if (is_array($fanpages2)){
			$have_fan_pages=1;


		echo '<label for="wb_secondary_target">'.__('Post to the following Wall', 'wordbooker').' : </label>';
		echo '<select id="wordbooker_secondary_target" name="wordbooker_secondary_target"  >';
				$option="";
			foreach ($fanpages2 as $fan_page) {
				if (strlen($fan_page['name'])>=2) {
				if ($fan_page['id']==$wordbookeruser_settings["wordbooker_secondary_target"] ) {$option .= '<option selected="yes" value='.$fan_page['id'].'>';} else { $option .= '<option value='.$fan_page[id].'>';}
				$option .= $fan_page['name']." (".substr($fan_page['id'],3).")&nbsp;&nbsp;";
				$option .= '</option>';
				}
			}
			echo $option;
			echo '</select> &nbsp;'; 
	
		echo '<select id="wordbooker_secondary_type" name="wordbooker_secondary_type"  >';
		foreach ($arr as $i => $value) {
	       		 if ($i==$wordbookeruser_settings['wordbooker_secondary_type']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
	      		 else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
	echo "</select>";
		echo '&nbsp;<INPUT TYPE=CHECKBOX NAME="wordbooker_secondary_active" '.$checked_flag[$wordbookeruser_settings["wordbooker_secondary_active"]].'></p><P>';

		}
		if (!isset($wordbookeruser_settings['wordbooker_extract_length'])) {$wordbookeruser_settings['wordbooker_extract_length'] =$wordbooker_settings['wordbooker_extract_length'];}
		echo '<label for="wb_extract_length">'.__('Length of Extract', 'wordbooker').' : </label><select id="wordbooker_extract_length" name="wordbooker_extract_length"  >';
	        $arr = array(10=> "10",20=> "20",50=> "50",100=> "100",120=> "120",150=> "150",175=> "175",200=> "200",  250=> "250", 256=>__("256 (Default) ", 'wordbooker'), 270=>"270", 300=>"300", 350 => "350",400 => "400",500 => "500",600 => "600",700 => "700",800 => "800",900 => "900");
                foreach ($arr as $i => $value) {
                        if ($i==$wordbookeruser_settings['wordbooker_extract_length']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br />";

	echo '<label for="wb_status_update">'.__('Facebook Status Text', 'wordbooker').'  : </label> ';
		echo '<INPUT NAME="wordbooker_status_update_text" size=60 maxlength=60 value="'.stripslashes($wordbookeruser_settings["wordbooker_status_update_text"]).'"> ';
		echo '</select><br />';

		echo '<label for="wb_attribute">'.__('Post Attribute', 'wordbooker').' : </label>';
		echo '<INPUT NAME="wordbooker_attribute" size=60 maxlength=240 value="'.stripslashes($wordbookeruser_settings["wordbooker_attribute"]).'"><br />';

		echo '<label for="wb_action_link">'.__('Action Link Option', 'wordbooker').' : </label><select id="wordbooker_actionlink" name="wordbooker_actionlink"  >';	
       		$arr = array(0=> __("Same as Blog", 'wordbooker'), 100=> __("None", 'wordbooker'),  200=> __("Share Link", 'wordbooker'), 300=>__("Read Full Article", 'wordbooker'));
                foreach ($arr as $i => $value) {
                        if ($i==$wordbookeruser_settings['wordbooker_actionlink']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br />";

		echo '<label for="wordbooker_search_this_header">'.__('Enable Extended description for Share Link', 'wordbooker').' : </label> ';
		echo '<select id="wordbooker_search_this_header" name="wordbooker_search_this_header"  >';	
       		$arr = array(0=> __("Same as Blog", 'wordbooker'), 100=> __("No", 'wordbooker'),  200=> __("Yes", 'wordbooker'));
                foreach ($arr as $i => $value) {
                        if ($i==$wordbookeruser_settings['wordbooker_search_this_header']){ echo '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
                       else {echo '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
                echo "</select><br />";


		if ( function_exists( 'get_the_post_thumbnail' ) ) {
			echo '<label for="wb_thumb_only">'.__('Use Post Thumbnail only', 'wordbooker').' : </label>';
			echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_thumb_only" '.$checked_flag[$wordbookeruser_settings["wordbooker_thumb_only"]].'><br />';
		}
		
		echo '<label for="wb_use_extract">'.__('Use Post Excerpt', 'wordbooker').' : </label>';
		echo '<INPUT TYPE=CHECKBOX NAME="wordbooker_use_excerpt" '.$checked_flag[$wordbookeruser_settings["wordbooker_use_excerpt"]].'><br />';


		echo '<label for="wb_status_id">'.__('Show Status for', 'wordbooker').' : </label> <select name="wordbooker_status_id" ><option selected="yes" value=-100>'.__('My Own Profile', 'wordbooker').'&nbsp;&nbsp;</option>';
		$option="";
		if ($have_fan_pages==1) {	
			foreach ($fanpages as $fan_page) {
				if(substr($fan_page[id],0,2)!="GW"){
				if ($fan_page[id]==$wordbookeruser_settings["wordbooker_status_id"] ) {$option .= '<option selected="yes" value='.$fan_page[id].'>';} else { $option .= '<option value='.$fan_page[id].'>';}
				$option .= $fan_page[name]."&nbsp;&nbsp;";
				$option .= '</option>';}
			}
			echo $option;
		}
		echo '</select><br />'; 
		_e('Disable Facebook User information in Status', 'wordbooker');
		echo ' : <INPUT TYPE=CHECKBOX NAME="wordbooker_disable_status" '.$checked_flag[$wordbookeruser_settings["wordbooker_disable_status"]].'><br /><p>';

		echo '<input type="submit" value="'.__("Save User Options", 'wordbooker').'" name="swbus" class="button-primary"  />&nbsp;&nbsp;&nbsp;<input type="submit" name="rwbus" value="'.__("Reset to Blog Defaults", 'wordbooker').'" class="button-primary"  /></form><br /></div><hr>';

}
		
	// Lets poll if they want to - we only poll for this user
		if ( isset($wordbooker_settings["wordbooker_comment_poll"])  && ADVANCED_DEBUG ){
			$dummy=wordbooker_poll_facebook($user_ID);
		}
		wordbooker_blog_level_options();
		wordbooker_user_level_options();
		wordbooker_render_errorlogs();
		wordbooker_status($user_ID);
		wordbooker_option_status($wbuser);

		echo "<br /><hr><h3>";
 	_e("Donate", 'wordbooker');
		echo "</h3>";

	if (defined('WORDBOOKER_PREMIUM')) { _e("You're using the Premium options in Wordbooker. You really should contribute something to the support and development of this plugin.  Please provide your FB Id number and your website when making payment so your details can be added to the <a href='http://wordbooker.tty.org.uk/thanks/'>'Thanks'</a>  list on the web site", 'wordbooker');
} 		
	_e("If you've found this extension useful then please feel free to donate to its support and future development. Please provide your FB Id number and your website when making payment so your details can be added to the <a href='http://wordbooker.tty.org.uk/thanks/'>'Thanks'</a> page on the Website", 'wordbooker'); 
	  ?><br /><br />
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
		<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
		</form><br /><br /><hr><h3>
		<?php

		wordbooker_option_support();
?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			//save_postboxes_state('<?php echo $wordbooker_hook; ?>');
			postboxes.add_postbox_toggles('<?php echo $wordbooker_hook; ?>');
		});
		//]]>
	</script>
		
		<?php

        }
	 else {
		wordbooker_option_setup($wbuser);
		wordbooker_render_errorlogs();
		wordbooker_option_support();
	}	

	}	


function wordbooker_admin_menu() {
	if (!current_user_can(WORDBOOKER_MINIMUM_ADMIN_LEVEL)) { return; }

	global $wordbooker_hook;
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
	$wordbooker_hook = add_options_page(WORDBOOKER_APPLICATION_NAME.' Option Manager', WORDBOOKER_APPLICATION_NAME,WORDBOOKER_MINIMUM_ADMIN_LEVEL, WORDBOOKER_SETTINGS_PAGENAME,'wordbooker_option_manager');
	add_action("load-$wordbooker_hook", 'wordbooker_admin_load');
	add_action("admin_head-$wordbooker_hook", 'wordbooker_admin_head');
}


add_action('admin_init', 'wordbooker_option_init' );
add_action('admin_menu', 'wordbooker_admin_menu');



include("wordbooker_posting_options.php");
?>
