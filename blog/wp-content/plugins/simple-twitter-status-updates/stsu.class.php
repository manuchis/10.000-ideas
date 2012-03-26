<?php

// DEFINE Twitter application settings
define('STSU_TWITTER_CK', 'ACTzPVc74h8wGtdyNPwDSQ');
define('STSU_TWITTER_CS', 'kt4a8eCEnas11nvGVdljMpOljZSrsAYhjL2DvCkYT5s');
define('STSU_TWITTER_RTU', 'http://twitter.com/oauth/request_token');
define('STSU_TWITTER_ATU', 'http://twitter.com/oauth/access_token');
define('STSU_TWITTER_AU', 'http://twitter.com/oauth/authorize');

define('TWITTER_CONSUMER_KEY', 'ACTzPVc74h8wGtdyNPwDSQ');
define('TWITTER_CONSUMER_SECRET', 'kt4a8eCEnas11nvGVdljMpOljZSrsAYhjL2DvCkYT5s');

// Load required PHP files
require_once(dirname(__FILE__).'/twitter-async/EpiCurl.php');
require_once(dirname(__FILE__).'/twitter-async/EpiOAuth.php');
require_once(dirname(__FILE__).'/twitter-async/EpiTwitter.php');

// Main class
class STSU {
	
	// Calculate timestamp from post data attribut
	private function calculateTimestamp($str_time_date){
		
		$int_time_date = explode(" ", $str_time_date);
		$int_time_date[0] = explode("-", $int_time_date[0]);
		$int_time_date[1] = explode(":", $int_time_date[1]);
		$int_time_date = mktime($int_time_date[1][0], $int_time_date[1][1], $int_time_date[1][2], $int_time_date[0][1], $int_time_date[0][2], $int_time_date[0][0]);
	
		return $int_time_date;
	}
	
	// Register settings page
	public function buildAdminMenu(){
		
		// Create new STSU Object
		$objSTSU = new STSU();
		
		// Check if twitter auth-token has been created
		if(!get_option('stsu_twitter_auth_token') and $_GET['page'] != 'stsu'){
			
			// Message, no twitter auth-token
			echo '<div id="message" class="updated"><p><b>Please <a href="options-general.php?page=stsu">authenticate</a> your twitter account with the &quot;Simple Twitter Status Updates&quot; plugin!</b></p></div>';
		}
		
		// Add page to the admin options
		add_options_page('Simple Twitter Status Updates', 'Twitter Updates', 'manage_options', 'stsu', array('STSU', 'pageSettings'));
		
		// Delete log entries (only when user is logged in the WP backend)
		$objSTSU->deleteLogEntries();
	}
	
	// STSU Shutdown Event
	public function shutdownEvent(){
		
		// Check if an error occured
		$error = error_get_last();
    	if($error['type'] == 1){
    		
    		// Get current log id or set to 1
		$int_log_id = (get_option('stsu_current_log_id') > 0) ? get_option('stsu_current_log_id') : 1;
		$int_log_id++;
		
		update_option('stsu_log_entry_'.$int_log_id, '[STSU_LOG]'.time().'[STSU_LOG]alert[STSU_LOG]'.$error['message']);
		
		// Save current log id
		update_option('stsu_current_log_id', $int_log_id);
    	}
	}
	
	// Add new log entry to the database
	public function addLogEntry($str_status, $str_message){
		
		// Check if log is enabled
		if(get_option('stsu_log_enabled') == 1){
		
			// Get current log id or set to 1
			$int_log_id = (get_option('stsu_current_log_id') > 0) ? get_option('stsu_current_log_id') : 1;
			$int_log_id++;
			
			// Write log entry
			if($str_status and $str_message){
				
				update_option('stsu_log_entry_'.$int_log_id, '[STSU_LOG]'.time().'[STSU_LOG]'.$str_status.'[STSU_LOG]'.$str_message);
			}
			
			// Save current log id
			update_option('stsu_current_log_id', $int_log_id);
		}
	}
	
	// Delete old log entries from database
	public function deleteLogEntries(){
		
		// Get needed data
		$int_deleted_log_id = (get_option('stsu_deleted_log_id') > 0) ? get_option('stsu_deleted_log_id') : 1;
		$int_current_log_id = get_option('stsu_current_log_id');
		$int_max_log_entries = get_option('stsu_log_lenght');
		
		// Check if there should log entries be deleted
		if($int_deleted_log_id < ($int_current_log_id - $int_max_log_entries)){
			
			// Delete log entries
			for($i = $int_deleted_log_id; $i <= ($int_current_log_id - $int_max_log_entries); $i++){
				
				// Delete from database
				delete_option('stsu_log_entry_'.$i);
			}
		}
		
		// Calculate new delete log id
		$int_deleted_log_id = ($int_current_log_id - $int_max_log_entries + 1);
		
		// Save deleted log id
		update_option('stsu_deleted_log_id', $int_deleted_log_id);
		
	}
	
	// User settings, twitter oauth authentication (GUI)
	public function pageSettings(){
		
		// Global variables
		global $current_user;
		
		// Create new STSU Object
		$objSTSU = new STSU();
		
		// Check if log should be shown
		if($_GET['action'] == 'showUpdateLog'){
			
			// Build page
			echo '<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Settings &gt; Simple Twitter Status Updates &gt; Update Log</h2></div>
			
			<h3>Log entries</h3>
			
			<p>Do you need help with this log? Contact us on twitter <a href="http://twitter.com/bannerweb">@bannerweb</a></p>';
			
			// Get logs in array
			$arr_log = array();
			$str_date_format = get_option('date_format');

			$int_current_log_id = get_option('stsu_current_log_id');
			$int_max_log_entries = get_option('stsu_log_lenght');
			
			$int_max_log_entries = ($int_current_log_id > $int_max_log_entries) ? ($int_current_log_id - $int_max_log_entries) : 1;
			
			echo '
			
			<p><a href="options-general.php?page=stsu">Back to the settings page</a></p>
			
			<table class="widefat" style="width: 98%;" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" style="width: 20px;">&nbsp;</th>
						<th scope="col" style="width: 100px;">Type</th>
						<th scope="col" style="width: 160px;">Time</th>
						<th scope="col">Message</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col">&nbsp;</th>
						<th scope="col">Type</th>
						<th scope="col">Time</th>
						<th scope="col">Message</th>
					</tr>
				</tfoot>
			
			';
			
			for($i = $int_current_log_id; $i >= $int_max_log_entries + 1; $i--){
				
				$tmp_arr_log = explode('[STSU_LOG]', get_option('stsu_log_entry_'.$i));
				
				if(!$tmp_arr_log[2]){ continue; }

				echo '
				<tr>
					<td scope="col"><img src="'.STSU_PLUGIN_WWW.'images/'.$tmp_arr_log[2].'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
					<td scope="col">'.strtoupper($tmp_arr_log[2]).'</td>
					<td scope="col">'.date($str_date_format." - H:i:s", $tmp_arr_log[1]).'</td>
					<td scope="col">'.$tmp_arr_log[3].'</td>
				</tr>
				';
			}
			
			
			echo '
			</table>
			
			<p><a href="options-general.php?page=stsu">Back to the settings page</a></p>
			
			<p>&nbsp;</p>
			
			<h3>Donate</h3>
			
			<p>Help us to keep this plugin up-to-date, to add more features, to give free support and to fix bugs with just a small amount of money.</p>
			
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="9HCY5XJ343NCC">
			<input type="image" src="https://www.paypal.com/en_US/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>

			<p>&nbsp;</p>
			
			</div>';
		}
		
		// Show normal page
		else{
			
			// Remove Twitter Authentication
			if($_GET['action'] == 'remTwitterAuth'){
				
				// Reset User Authentication
				update_option('stsu_twitter_auth_token', '');
				update_option('stsu_twitter_auth_secret', '');
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Twitter account '.get_option('stsu_twitter_screen_name').' has been removed by user '.$current_user->display_name);
			
				// Reset twitter screen name
				update_option('stsu_twitter_screen_name', '');
			}
			
			// Save data
			if($_POST['stsu_settings']){
				
				// Basic settings
				update_option('stsu_'.'post_new', preg_replace('#[^0-9]#', '', $_POST['post_new']));
				update_option('stsu_'.'comment_post', preg_replace('#[^0-9]#', '', $_POST['comment_post']));
				update_option('stsu_'.'post_modify', preg_replace('#[^0-9]#', '', $_POST['post_modify']));
				
				// Postfix and Suffix // Postfix == suffix // postfix = prefix :-(
				update_option('stsu_'.'new_post_suffix', htmlspecialchars($_POST['new_post_suffix']));
				update_option('stsu_'.'new_post_postfix', htmlspecialchars($_POST['new_post_postfix']));
				update_option('stsu_'.'post_comment_suffix', htmlspecialchars($_POST['post_comment_suffix']));
				update_option('stsu_'.'post_comment_postfix', htmlspecialchars($_POST['post_comment_postfix']));
				update_option('stsu_'.'modified_post_suffix', htmlspecialchars($_POST['modified_post_suffix']));
				update_option('stsu_'.'modified_post_postfix', htmlspecialchars($_POST['modified_post_postfix']));
				
				// Update time interval
				update_option('stsu_'.'time_gap_general', preg_replace('#[^0-9]#', '', $_POST['time_gap_general']));
				update_option('stsu_'.'time_gap_post', preg_replace('#[^0-9]#', '', $_POST['time_gap_post']));
				
				// URL shortener
				update_option('stsu_'.'url_shortener_bitly', preg_replace('#[^0-9]#', '', $_POST['url_shortener_bitly']));
				
				// Update log settings
				update_option('stsu_'.'log_enabled', preg_replace('#[^0-9]#', '', $_POST['log_enabled']));
				update_option('stsu_'.'log_lenght', preg_replace('#[^0-9]#', '', $_POST['log_lenght']));
				
	    		// Display saved message
				echo '<div id="message" class="updated"><p>Changes has been saved!</p></div>';
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Settings has been changed by user '.$current_user->display_name);
			}
			
			// Verify Twitter auth-token
			if($_GET['oauth_token'] and $_GET['oauth_verifier'] and !get_option('stsu_twitter_screen_name')){
				
				$objTwitter = new EpiTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
				$objTwitter->setToken($_GET['oauth_token']);  
				$token = $objTwitter->getAccessToken(array('oauth_verifier' => $_GET['oauth_verifier']));
				$objTwitter->setToken($token->oauth_token, $token->oauth_token_secret);
				$twitterInfo = $objTwitter->get_accountVerify_credentials();
				
				update_option('stsu_twitter_auth_token', $token->oauth_token);
				update_option('stsu_twitter_auth_secret', $token->oauth_token_secret);
				update_option('stsu_twitter_screen_name', $token->screen_name);
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Twitter account '.get_option('stsu_twitter_screen_name').' has been authenticated  by user '.$current_user->display_name);
			}
			
			// Display settings form
			echo '<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Settings &gt; Simple Twitter Status Updates</h2></div>
			
			<h3>How does it work?</h3>
			
			<p>The "Simple Twitter Status Updates" WordPress plugin automatically publishes a status on your twitter account when a new post has been published, modified or commented by an user.<br />
			Keep your followers up-to-date with what happens on your blog!</p>
			
			<p>Visit <a href="http://www.bannerweb.ch/das-unternehmen/kontakt/">www.bannerweb.ch</a> for further information to give us a feedback or to get support!</p>
			
			<p>&nbsp;</p>
			<h3>Twitter authentication (oAuth)</h3>';
			
			// Check if twitter auth-token has been created
			if(!get_option('stsu_twitter_auth_token')){
				
				// Generate oAuth URL
				$objTwitter = new EpiTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
				$str_request_token = $objTwitter->getRequestToken(array('oauth_callback' => get_bloginfo('wpurl').'/wp-admin/options-general.php?page=stsu'));
				$str_request_url = $objTwitter->getAuthenticateUrl($str_request_token);
				
		
				echo '
				<p><b>You have to authenticate this plugin with your twitter account to make it work.</b></p>
				<p>Do so by clicking the button below. The process will take you to the twitter website.</p>
				<p><input type="submit" class="button-primary" name="twitterOAuth" value="Authenticate with twitter" onclick="document.location.href=\''.$str_request_url.'\'" /></p>';
			}
			
			else{
				
				echo '
				<p>The &quot;Simple Twitter Status Updates&quot; plugin has been successfully authenticated with your Twitter account <b>['.get_option('stsu_twitter_screen_name').']</b></p>
				<p><a href="javascript:if(confirm(\'After removing this authentication there will be no more status updates on your twitter timeline!\n\nContinue?\')==true){document.location.href=\'options-general.php?page=stsu&action=remTwitterAuth\'}" />Remove Twitter authentication for ['.get_option('stsu_twitter_screen_name').']</a></p>';
			}
			
			
			echo '
			<p>&nbsp;</p>
			<h3>Basic settings</h3>
			
			<form method="post" action="options-general.php?page=stsu">
	
			<table class="form-table">
				<tr>
					<th scope="row" colspan="2" class="th-full">
					<label for="post_new">
					<input name="post_new" id="post_new" value="1" type="checkbox"
					'.((get_option('stsu_'.'post_new') == 1) ? 'checked="checked"' : false ).'>
					Publish a twitter status when publishing a NEW POST</label>
					</th>
				</tr>
				<tr>
					<th scope="row" colspan="2" class="th-full">
					<label for="post_modify">
					<input name="post_modify" id="post_modify" value="1" type="checkbox"
					'.((get_option('stsu_'.'post_modify') == 1) ? 'checked="checked"' : false ).'>
					Publish a twitter status when a POST has been MODIFIED</label>
					</th>
				</tr>
				<tr>
					<th scope="row" colspan="2" class="th-full">
					<label for="comment_post">
					<input name="comment_post" id="comment_post" value="1" type="checkbox"
					'.((get_option('stsu_'.'comment_post') == 1) ? 'checked="checked"' : false ).'>
					Publish a twitter status when there is a NEW COMMENT on a POST</label>
					</th>
				</tr>
			</table>
			
			<p><input type="submit" class="button" name="stsu_settings" value="save changes" /></p>
			
			<p>&nbsp;</p>
			<h3>Prefix and suffix</h3>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="new_post_suffix">NEW POST prefix</label></th>
					<td><input name="new_post_suffix" id="new_post_suffix" 
					value="'.get_option('stsu_'.'new_post_suffix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>before</b> the link to the post</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="new_post_postfix">NEW POST suffix</label></th>
					<td><input name="new_post_postfix" id="new_post_postfix" 
					value="'.get_option('stsu_'.'new_post_postfix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>after</b> the link to the post</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="modified_post_suffix">MODIFIED POST prefix</label></th>
					<td><input name="modified_post_suffix" id="modified_post_suffix" 
					value="'.get_option('stsu_'.'modified_post_suffix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>before</b> the link to the post</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="modified_post_postfix">MODIFIED POST suffix</label></th>
					<td><input name="modified_post_postfix" id="modified_post_postfix" 
					value="'.get_option('stsu_'.'modified_post_postfix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>after</b> the link to the post</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="post_comment_suffix">POST COMMENT prefix</label></th>
					<td><input name="post_comment_suffix" id="post_comment_suffix" 
					value="'.get_option('stsu_'.'post_comment_suffix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>before</b> the link to the post</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="post_comment_postfix">POST COMMENT suffix</label></th>
					<td><input name="post_comment_postfix" id="post_comment_postfix" 
					value="'.get_option('stsu_'.'post_comment_postfix').'" class="regular-text code" type="text">
					<span class="description">Will be added to the twitter status <b>after</b> the link to the post</span>
					</td>
				</tr>
			</table>
			
			<p><input type="submit" class="button" name="stsu_settings" value="save changes" /></p>
			
			<p>&nbsp;</p>
			<h3>Update time interval</h3>
			
			<p>To prevent your twitter stream from being flooded by an uncountable number of status updates from your blog you can set minimal time gaps between two updates.</p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="time_gap_general">GENERAL time gap</label></th>
					<td><input name="time_gap_general" id="time_gap_general" 
					value="'.((!get_option('stsu_'.'time_gap_general')) ? '1800' : get_option('stsu_'.'time_gap_general') ).'" class="small-text code" type="text">
					<span class="description">General time gap between two twitter status updates in <b>seconds</b> (new post updates will always be updated when activated)</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="time_gap_post">POST time gap</label></th>
					<td><input name="time_gap_post" id="time_gap_post" 
					value="'.((!get_option('stsu_'.'time_gap_post')) ? '1800' : get_option('stsu_'.'time_gap_post') ).'" class="small-text code" type="text">
					<span class="description">General time gap between two twitter status updates in <b>seconds</b> for the same post (don\'t mind if new, modified or commented) </span>
					</td>
				</tr>
			</table>
			
			<p><input type="submit" class="button" name="stsu_settings" value="save changes" /></p>
			
			<p>&nbsp;</p>
			<h3>URL shortener</h3>
		
			<table class="form-table">
				<tr>
					<th scope="row" colspan="2" class="th-full">
					<label for="url_shortener_bitly">
					<input name="url_shortener_bitly" id="url_shortener_bitly" value="1" type="checkbox"
					'.((get_option('stsu_'.'url_shortener_bitly') == 1) ? 'checked="checked"' : false ).'>
					Use bit.ly as URL shortener (http://bit.ly/abc12) instead of the original blog URL ('.home_url().'/?p=123)</label>
					</th>
				</tr>
			</table>
			
			<p><input type="submit" class="button" name="stsu_settings" value="save changes" /></p>
			
			<p>&nbsp;</p>
			<h3>Status update log</h3>
			
			<p>Enable the status update log to find out why status updates won\'t be published on twitter.</p>
			<p><a href="options-general.php?page=stsu&action=showUpdateLog">Show status update log entries</a></p>
			
			<table class="form-table">
				<tr>
					<th scope="row" colspan="2" class="th-full">
					<label for="log_enabled">
					<input name="log_enabled" id="log_enabled" value="1" type="checkbox"
					'.((get_option('stsu_'.'log_enabled') == 1) ? 'checked="checked"' : false ).'>
					Enable status update logging (critical errors always get logged)</label>
					</th>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="log_lenght">Log length</label></th>
					<td><input name="log_lenght" id="log_lenght" 
					value="'.((!get_option('stsu_'.'log_lenght')) ? '100' : get_option('stsu_'.'log_lenght') ).'" class="small-text code" type="text">
					<span class="description">Maximum number of logged events (default: 100)</span>
					</td>
				</tr>
			</table>
			
			<p><input type="submit" class="button" name="stsu_settings" value="save changes" /></p>
			
			</form>
			
			<p>&nbsp;</p>
			
			
			<h3>Donate</h3>
			
			<p>Help us to keep this plugin up-to-date, to add more features, to give free support and to fix bugs with just a small amount of money.</p>
			
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="9HCY5XJ343NCC">
			<input type="image" src="https://www.paypal.com/en_US/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>

			<p>&nbsp;</p>
			
			';
		}
	}

	// Called when there is a new published or modified post or page
	public function postPagePublish($int_id_post){
		
		global $wpdb;
		
		$objSTSU = new STSU();
		
		$int_post_entry = $objSTSU->calculateTimestamp($wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE ID = '".$int_id_post."'"));
		$int_post_modified = $objSTSU->calculateTimestamp($wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE ID = '".$int_id_post."'"));
		
		// Check if Post is new
		if($int_post_entry == $int_post_modified){
		
			// Check if status should be updated
			if(get_option('stsu_'.'post_new') == 1){ 
				
				// Post and prefix
				$str_prefix = get_option('stsu_'.'new_post_suffix');
				$str_postfix = get_option('stsu_'.'new_post_postfix');
				
				// Calculate lenght
				$int_prefix_lenght = strlen($str_prefix) + 1;
				$int_postfix_lenght = strlen($str_postfix) + 1;
				$int_usable_lenght = 140 - $int_prefix_lenght - $int_postfix_lenght;
				
				// Get URL of current post
				$str_post_url = $objSTSU->getAndShortenURL($int_id_post);
				
				// Calculate avilable title lenght
				$int_usable_lenght =  $int_usable_lenght - (strlen($str_post_url) + 2);
				
				// Get title of current post
				$str_post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
				
				// Check if title is too long
				if(strlen($str_post_title) > $int_usable_lenght){
					
					// Shorten title
					$str_post_title = trim(substr($str_post_title, 0, $int_usable_lenght -4)).'...';
				}
				
				// Status string
				$str_status = $str_prefix.' '.$str_post_title.' '.$str_post_url.' '.$str_postfix;
				
				// Set Twitter status
				$objSTSU->updateStatus($str_status, 'post_new');
			
				// Set Timestamp of post update
				update_option('stsu_'.'post_last_update_'.$int_id_post, $int_post_modified);
				update_option('stsu_'.'last_update', $int_post_modified);
			}
			
			// New post status update is not activated
			else{
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Sending status update for a new post is not neccessary (not activated)');
			}
		}
		
		// Post modification
		else {
			
			// Check if status should be updated
			if(get_option('stsu_'.'post_modify') == 1){
				
				// Check if post time gap is ok
				if($int_post_modified - get_option('stsu_'.'time_gap_post') > get_option('stsu_'.'post_last_update_'.$int_id_post)){
				
					// Check if general time gap is ok
					if($int_post_modified - get_option('stsu_'.'time_gap_general') > get_option('stsu_'.'last_update')){
					
						// Post and prefix
						$str_prefix = get_option('stsu_'.'modified_post_suffix');
						$str_postfix = get_option('stsu_'.'modified_post_postfix');
						
						// Calculate lenght
						$int_prefix_lenght = strlen($str_prefix) + 1;
						$int_postfix_lenght = strlen($str_postfix) + 1;
						$int_usable_lenght = 140 - $int_prefix_lenght - $int_postfix_lenght;
						
						// Get URL of current post
						$str_post_url = $objSTSU->getAndShortenURL($int_id_post);
						
						// Calculate avilable title lenght
						$int_usable_lenght =  $int_usable_lenght - (strlen($str_post_url) + 2);
						
						// Get title of current post
						$str_post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
						
						// Check if title is too long
						if(strlen($str_post_title) > $int_usable_lenght){
							
							// Shorten title
							$str_post_title = trim(substr($str_post_title, 0, $int_usable_lenght -4)).'...';
						}
						
						// Status string
						$str_status = $str_prefix.' '.$str_post_title.' '.$str_post_url.' '.$str_postfix;
						
						// Set Twitter status
						$objSTSU->updateStatus($str_status, 'post_modified');
						
						// Set Timestamp of post update
						update_option('stsu_'.'post_last_update_'.$int_id_post, $int_post_modified);
						update_option('stsu_'.'last_update', $int_post_modified);
					}

					// General time gap
					else{
						
						// Add log entry
						$objSTSU->addLogEntry('information', 'Sending status update for a post modification is not neccessary (general time gap)');
					}
				}
				// post time gap
				else{
					
					// Add log entry
					$objSTSU->addLogEntry('information', 'Sending status update for a post modification is not neccessary (post time gap)');
				}
			}
			
			// Modified post status update is not activated
			else{
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Sending status update for a post modification is not neccessary (not activated)');
			}
		}
	}
	
	// Called when there is a new published comment
	public function commentPublish($int_id_comment, $str_comment_state){
		
		global $wpdb;
		
		$objSTSU = new STSU();
		
		// Get post id
		$int_id_post = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = '".$int_id_comment."'");
		
		// Get post type
		$str_post_type = $wpdb->get_var("SELECT post_type FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
		
		// Check if post id is avilable (isn't if comment is not approved) and if it so not a page
		if($int_id_post > 0 and $str_post_type == 'post'){
		
			// Get post modified date
			$int_comment_date = $objSTSU->calculateTimestamp($wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_ID = '".$int_id_comment."'"));
			
			// Check if status should be updated
			if(get_option('stsu_'.'comment_post') == 1){ 
			
				// Post time gap
				if($int_comment_date - get_option('stsu_'.'time_gap_post') > get_option('stsu_'.'post_last_update_'.$int_id_post)){
					
					// general time gap
					if($int_comment_date - get_option('stsu_'.'time_gap_general') > get_option('stsu_'.'last_update')){

						// Check comment state (1 == published)
						if($str_comment_state == 1){
							
							// Post and prefix
							$str_prefix = get_option('stsu_'.'post_comment_suffix');
							$str_postfix = get_option('stsu_'.'post_comment_postfix');
							
							// Calculate lenght
							$int_prefix_lenght = strlen($str_prefix) + 1;
							$int_postfix_lenght = strlen($str_postfix) + 1;
							$int_usable_lenght = 140 - $int_prefix_lenght - $int_postfix_lenght;
							
							// Get URL of current post
							$str_post_url = $objSTSU->getAndShortenURL($int_id_post);
							
							// Calculate avilable title lenght
							$int_usable_lenght =  $int_usable_lenght - (strlen($str_post_url) + 2);
							
							// Get title of current post
							$str_post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
							
							// Check if title is too long
							if(strlen($str_post_title) > $int_usable_lenght){
								
								// Shorten title
								$str_post_title = trim(substr($str_post_title, 0, $int_usable_lenght -4)).'...';
							}
							
							// Status string
							$str_status = $str_prefix.' '.$str_post_title.' '.$str_post_url.' '.$str_postfix;
							
							// Set Twitter status
							$objSTSU->updateStatus($str_status, 'comment');
							
							// Set Timestamp of post update
							update_option('stsu_'.'post_last_update_'.$int_id_post, $int_comment_date);
							update_option('stsu_'.'last_update', $int_comment_date);
						}
						
						// Comment has not been published
						else{
							
							// Mark comment as not yet published on twitter
							update_option('stsu_'.'comment_published_'.$int_id_comment, 'no');
						}
					}

					// General time gap
					else{
						
						// Add log entry
						$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (general time gap)');
					}
				}
				// post time gap
				else{
					
					// Add log entry
					$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (post time gap)');
				}
			}
				
			// new comment status update is not activated
			else{
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (not activated)');
			}
		}
	}
	
	// Called when there is a modified comment
	public function commentEdit($int_id_comment){
		
		global $wpdb;
		
		$objSTSU = new STSU();
		
		// Get post id
		$int_id_post = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = '".$int_id_comment."'");
		
		// Get post type
		$str_post_type = $wpdb->get_var("SELECT post_type FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
		
		// Get comment status
		$str_comment_state = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID = '".$int_id_comment."'");
		
		// Check if post id is avilable (isn't if comment is not approved) and if it so not a page
		if($int_id_post > 0 and $str_post_type == 'post'){
		
			// Get post modified date
			$int_comment_date = $objSTSU->calculateTimestamp($wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_ID = '".$int_id_comment."'"));
			
			// Check if status should be updated
			if(get_option('stsu_'.'comment_post') == 1 and $str_comment_state == 1 and get_option('stsu_'.'comment_published_'.$int_id_comment) == 'no'){ 
						
				// Post time gap
				if($int_comment_date - get_option('stsu_'.'time_gap_post') > get_option('stsu_'.'post_last_update_'.$int_id_post)){
				
					// General time gap
					if($int_comment_date - get_option('stsu_'.'time_gap_general') > get_option('stsu_'.'last_update')){
				
						// Post and prefix
						$str_prefix = get_option('stsu_'.'post_comment_suffix');
						$str_postfix = get_option('stsu_'.'post_comment_postfix');
						
						// Calculate lenght
						$int_prefix_lenght = strlen($str_prefix) + 1;
						$int_postfix_lenght = strlen($str_postfix) + 1;
						$int_usable_lenght = 140 - $int_prefix_lenght - $int_postfix_lenght;
						
						// Get URL of current post
						$str_post_url = $objSTSU->getAndShortenURL($int_id_post);
						
						// Calculate avilable title lenght
						$int_usable_lenght =  $int_usable_lenght - (strlen($str_post_url) + 2);
						
						// Get title of current post
						$str_post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
						
						// Check if title is too long
						if(strlen($str_post_title) > $int_usable_lenght){
							
							// Shorten title
							$str_post_title = trim(substr($str_post_title, 0, $int_usable_lenght -4)).'...';
						}
						
						// Status string
						$str_status = $str_prefix.' '.$str_post_title.' '.$str_post_url.' '.$str_postfix;
						
						// Set Twitter status
						$objSTSU->updateStatus($str_status, 'comment');
						
						// Set Timestamp of post update
						update_option('stsu_'.'post_last_update_'.$int_id_post, $int_comment_date);
						update_option('stsu_'.'last_update', $int_comment_date);
						
						// Delete comment mark
						delete_option('stsu_'.'comment_published_'.$int_id_comment);
					}

					// General time gap
					else{
						
						// Add log entry
						$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (general time gap)');
					}
				}
				// post time gap
				else{
					
					// Add log entry
					$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (post time gap)');
				}
			}
			
			// new comment status update is not activated
			else{
				
				// Add log entry
				$objSTSU->addLogEntry('information', 'Sending status update for a new comment is not neccessary (not activated)');
			}
		}
	}
	
	// Calculate the post URL, shrink if neccessary and return it
	public function getAndShortenURL($int_id_post){
		
		// Objects
		$objSTSU = new STSU();
		
		// Variables
		global $wpdb;
		$str_url;
		
		// Get URL of current post
		$str_url = $wpdb->get_var("SELECT guid FROM $wpdb->posts WHERE ID = '".$int_id_post."'");
		
		// Check if URL must be shortened
		if(get_option('stsu_url_shortener_bitly') == 1){
			
			// Get the permalink
			$str_url = get_permalink($int_id_post);
			
			// Shorten URL using bit.ly
			$objCURL = curl_init('http://api.bitly.com/v3/shorten?login=bannerweb&apiKey=R_f91c20da38d3b038a096255e701290cd&longUrl='.$str_url.'&format=json');
		   	curl_setopt($objCURL, CURLOPT_TIMEOUT, 10);
		   	curl_setopt($objCURL, CURLOPT_RETURNTRANSFER, 1);
		   	$str_curl_response = curl_exec($objCURL);
		   	curl_close($objCURL);
		
		   	// Check result
		   	if($str_curl_response){
		   		
		   			// Save generated URL
		   			$arr_response = json_decode($str_curl_response);
		   			$str_url = $arr_response->data->url;
		   			
		   			// Add status message
		   			$objSTSU->addLogEntry('success', 'Received bit.ly URL ('.$str_url.')');
		   	}  	
		   	
		   	else{
		   		
		   		// Add status message
		   		$objSTSU->addLogEntry('warning', 'bit.ly URL shortening failed: request timed out');
		   	}
		}
		
		// Return URL
		return $str_url;
	}
	
	// Sends a status updat to twitter
	public function updateStatus($str_status, $str_type){
		
		$objSTSU = new STSU();
		
		// Check if there is a twitter authentication
		if(get_option('stsu_twitter_auth_token') and get_option('stsu_twitter_auth_secret')){

			// Create nerw Twitter object
			$objTwitter = new EpiTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, get_option('stsu_twitter_auth_token'), get_option('stsu_twitter_auth_secret'));
			
			// Try to submit status update
			try {
				$resp = $objTwitter->post('/statuses/update.json', array('status' => $str_status));
				
				// Add success log entry
				if($str_type == 'post_new'){
					
					$objSTSU->addLogEntry('success', 'Status update for a new post has been published');
				}
				else if($str_type == 'post_modified'){
					
					$objSTSU->addLogEntry('success', 'Status update for a post modification has been published');
				}
				else if($str_type == 'comment'){
					
					$objSTSU->addLogEntry('success', 'Status update for a new comment has been published');
				}	
				
				//var_dump($resp->response);
			}
			
			catch(EpiTwitterException $e){
				
				// Add log entry
				$objSTSU->addLogEntry('warning', 'Status update rejected by twitter with the following message: '.$e->getMessage());
			}
		}
		
		// No twitter account avilable
		else{
			
			// Add warning log entry
			if($str_type == 'post_new'){
				
				$objSTSU->addLogEntry('warning', 'No twitter account has been authenticated: cannot send status update for a new post');
			}
			else if($str_type == 'post_modified'){
				
				$objSTSU->addLogEntry('warning', 'No twitter account has been authenticated: cannot send status update for a post modification');
			}
			else if($str_type == 'comment'){
				
				$objSTSU->addLogEntry('warning', 'No twitter account has been authenticated: cannot send status update for a new comment');
			}	
		}
	}
}

?>