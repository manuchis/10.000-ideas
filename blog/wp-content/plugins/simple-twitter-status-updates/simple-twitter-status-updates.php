<?php

/*

Plugin Name: Simple Twitter Status Updates
Plugin URI: http://www.bannerweb.ch/unsere-leistungen/wordpress-dev/simple-twitter-status-updates/
Description: Automatically publishes a status on your twitter account when a post has been plublished, modified or commented by a user.
Version: 1.4.2.2
Author: Bannerweb GmbH
Author URI: http://www.bannerweb.ch/

*/


# Define settings
# -----------------------------------------------------
define('STSU_PLUGIN_WWW', get_bloginfo('wpurl').'/wp-content/plugins/simple-twitter-status-updates/');



# Compatibilty check / plugin initialization
# -----------------------------------------------------

// get wordpress version number and fill it up to 9 digits
$int_wp_version = preg_replace('#[^0-9]#', '', get_bloginfo('version'));
while(strlen($int_wp_version) < 9){
	
	$int_wp_version .= '0'; 
}

// get php version number and fill it up to 9 digits
$int_php_version = preg_replace('#[^0-9]#', '', phpversion());
while(strlen($int_php_version) < 9){
	
	$int_php_version .= '0'; 
}

// Check if CURL is loaded, get version number and fill it up to 9 digits
if(extension_loaded('curl') === true){
	
	if(function_exists('curl_version')){
	
		$arr_curl_version = curl_version();
		$int_curl_version = preg_replace('#[^0-9]#', '', $arr_curl_version['version']);
		while(strlen($int_curl_version) < 9){
			
			$int_curl_version .= '0'; 
		}
	}
}

// Check if PHP isn't running in save mode and open_basedir isn't set
if(extension_loaded('curl') === true){
	
	$arr_curl_version = curl_version();
	$int_curl_version = preg_replace('#[^0-9]#', '', $arr_curl_version['version']);
	while(strlen($int_curl_version) < 9){
		
		$int_curl_version .= '0'; 
	}
}


# Functions
# -----------------------------------------------------

// Write shutdown error log
register_shutdown_function('stsuWriteShutdownErrorLog');

function stsuWriteShutdownErrorLog() {
	
	// Get erroro data
    $error = error_get_last();
    
    // If script has been shutdown due to a fatal error
    if ($error['type'] == 1) {
        
    	// Get current log id or set to 1
		$int_log_id = (get_option('stsu_current_log_id') > 0) ? get_option('stsu_current_log_id') : 1;
		$int_log_id++;
		
		update_option('stsu_log_entry_'.$int_log_id, '[STSU_LOG]'.time().'[STSU_LOG]alert[STSU_LOG]'.$error['message']);
		
		// Save current log id
		update_option('stsu_current_log_id', $int_log_id);
    }
}

// Hide errors
error_reporting(0);

// Display incompatibility notification
function stsu_incompatibility_notification(){
	
	// Get global variables
	global $int_wp_version;
	global $int_php_version;
	global $int_curl_version;
	
	// Get version data
	$arr_curl_version = curl_version();
	
	echo '<div id="message" class="error">
	
	<p><b>The &quot;Simple Twitter Status Updates&quot; plugin does not work in this WordPress environment!</b></p>
	
	<table>
	<tr>
		<td style="width: 25px;"><img src="'.STSU_PLUGIN_WWW.'images/'.(($int_wp_version >= 300000000) ? 'success' : 'alert').'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
		<td>Requires at least WordPress version 3.0 <i>(Installed: '.get_bloginfo('version').')</i></td>
	</tr>
	<tr>
		<td style="width: 25px;"><img src="'.STSU_PLUGIN_WWW.'images/'.(($int_php_version >= 520000000) ? 'success' : 'alert').'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
		<td>Requires at least PHP version 5.2 <i>(Installed: '.phpversion().')</i></td>
	</tr>
	<tr>
		<td style="width: 25px;"><img src="'.STSU_PLUGIN_WWW.'images/'.(($int_curl_version >= 700000000) ? 'success' : 'alert').'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
		<td>Requires at least PHP extension CURL version 7.0 
		<i>('.((extension_loaded('curl') === true) ? 'Installed: '.$arr_curl_version['version'] : 'CURL is not installed!').')</i></td>
	</tr>
	<tr>
		<td style="width: 25px;"><img src="'.STSU_PLUGIN_WWW.'images/'.((!ini_get('safe_mode')) ? 'success' : 'alert').'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
		<td>PHP save mode must be turned OFF in your php.ini
		<i>('.((!ini_get('safe_mode')) ? 'Save mode is turned OFF' : 'Save mode is turned ON').')</i></td>
	</tr>
	<tr>
		<td style="width: 25px;"><img src="'.STSU_PLUGIN_WWW.'images/'.((!ini_get('open_basedir')) ? 'success' : 'warning').'.png" alt="" title="'.$tmp_arr_log[2].'" /></td>
		<td>The php.ini value OPEN_BASEDIR should be empty
		<i>('.((!ini_get('open_basedir')) ? 'OPEN_BASEDIR is empty' : 'OPEN_BASEDIR is set with following value ['.ini_get('open_basedir').']').') [should not be a problem but sometimes is one]</i></td>
	</tr>
	</table>
	
	<p>Do you need help? Contact us on twitter <a href="http://twitter.com/bannerweb">@bannerweb</a></p>
	
	</div>';
}

# Check overall plugin compatibility
# -----------------------------------------------------
if(	$int_wp_version >= 300000000 and 		// Wordpress version > 2.7
	$int_php_version >= 520000000 and 		// PHP version > 5.2
	$int_curl_version >= 700000000 and 		// CURL version > 7.0
	!ini_get('safe_mode') and				// SAVE_MODE is turned OFF
	defined('ABSPATH') and 					// Plugin is not loaded directly
	defined('WPINC')){						// Plugin is not loaded directly
		
	// Load class file
	require_once(dirname(__FILE__).'/stsu.class.php');
	
	// Build admin menu
	add_action('admin_menu', array('STSU', 'buildAdminMenu'), 1);
	
	// Register publish post action
	add_action('publish_post', array('STSU', 'postPagePublish'), 10, 1);

	// Register new popst comment action
	add_action('comment_post', array('STSU', 'commentPublish'), 10, 2);
	
	// Register new popst comment action
	add_action('edit_comment', array('STSU', 'commentEdit'), 10, 1);
}

// Plugin is not compatible with current configuration
else{
	
	// Display incompatibility information
	add_action('admin_notices', 'stsu_incompatibility_notification');
}

?>