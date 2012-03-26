<?php
/*
Plugin Name: Wordbooker
Plugin URI: http://wordbooker.tty.org.uk
Description: Provides integration between your blog and your Facebook account. Navigate to <a href="options-general.php?page=wordbooker">Settings &rarr; Wordbooker</a> for configuration.
Author: Steve Atty 
Author URI: http://wordbooker.tty.org.uk
Version: 2.1.8
*/

 /*
 *
 *
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


@include("includes/premium.php");
global $table_prefix, $wp_version,$wpdb,$db_prefix,$wbooker_user_id;
$wbooker_user_id=0;

$wordbooker_settings = wordbooker_options(); 
if (! isset($wordbooker_settings['wordbooker_extract_length'])) $wordbooker_settings['wordbooker_extract_length']=256;

define('WORDBOOKER_DEBUG', false);
define('WORDBOOKER_TESTING', false);
define('WORDBOOKER_CODE_RELEASE',"2.1.8 - Call me Eugene");

# For Troubleshooting 
define('ADVANCED_DEBUG',false);

#$facebook_config['debug'] = WORDBOOKER_TESTING && !$_POST['action'];
#Wordbooker2 - Dev
#define('WORDBOOKER_FB_APIKEY', '111687885534181');
#define('WORDBOOKER_FB_SECRET', '6d5df44f0a22c735737e7975aa6be57f');
#define('WORDBOOKER_FB_ID', '111687885534181');

# Wordbooker - live
if (!defined('WORDBOOKER_PREMIUM')) {
define('APP TITLE','Wordbooker');
define('WORDBOOKER_FB_APIKEY', '0cbf13c858237f5d74ef0c32a4db11fd');
define('WORDBOOKER_FB_SECRET', 'df04f22f3239fb75bf787f440e726f31');
define('WORDBOOKER_FB_ID', '254577506873');
define('WORDBOOKER_APPLICATION_NAME','Wordbooker');
define('OPENGRAPH_NAMESPACE','wordbooker');
define('OPENGRAPH_ACCESS_TOKEN','AAAAAO0YAejkBAE3gGR2KjCr6WhUO1ZBNyXHP6vaQoQLbwvlDyKDK0BIMZBb6mVyk2ZAbvPEXyrZCLNd6Bb8TA0HJCKGkotUZD');
}

define('WORDBOOKER_FB_APIVERSION', '1.0');
define('WORDBOOKER_FB_DOCPREFIX','http://wiki.developers.facebook.com/index.php/');
define('WORDBOOKER_FB_PUBLISH_STREAM', 'publish_stream');
define('WORDBOOKER_FB_READ_STREAM', 'read_stream');
define('WORDBOOKER_FB_STATUS_UPDATE',"status_update");
define('WORDBOOKER_FB_CREATE_NOTE',"create_note");
define('WORDBOOKER_FB_OFFLINE_ACCESS',"offline_access");
define('WORDBOOKER_FB_MANAGE_PAGES',"manage_pages");
define('WORDBOOKER_FB_PHOTO_UPLOAD',"photo_upload");
define('WORDBOOKER_FB_VIDEO_UPLOAD',"video_upload");
define('WORDBOOKER_FB_READ_FRIENDS',"read_friendlists");
define('WORDBOOKER_SETTINGS', 'wordbooker_settings');
define('WORDBOOKER_OPTION_SCHEMAVERS', 'schema_vers');
define('WORDBOOKER_SCHEMA_VERSION', '4');

$new_wb_table_prefix=$wpdb->base_prefix;
if (isset ($db_prefix) ) { $new_wb_table_prefix=$db_prefix;}

define('WORDBOOKER_ERRORLOGS', $new_wb_table_prefix . 'wordbooker_errorlogs');
define('WORDBOOKER_POSTLOGS', $new_wb_table_prefix . 'wordbooker_postlogs');
define('WORDBOOKER_USERDATA', $new_wb_table_prefix . 'wordbooker_userdata');
define('WORDBOOKER_USERSTATUS', $new_wb_table_prefix . 'wordbooker_userstatus');
define('WORDBOOKER_POSTCOMMENTS', $new_wb_table_prefix . 'wordbooker_postcomments');
define('WORDBOOKER_PROCESS_QUEUE', $new_wb_table_prefix . 'wordbooker_process_queue');
define('WORDBOOKER_FB_FRIENDS', $new_wb_table_prefix . 'wordbooker_fb_friends');
define('WORDBOOKER_FB_FRIEND_LISTS', $new_wb_table_prefix . 'wordbooker_fb_friend_lists');

define('WORDBOOKER_MINIMUM_ADMIN_LEVEL', 'edit_posts');	/* Contributor role or above. */
define('WORDBOOKER_SETTINGS_PAGENAME', 'wordbooker');
define('WORDBOOKER_SETTINGS_URL', 'options-general.php?page=' . WORDBOOKER_SETTINGS_PAGENAME);

$wordbooker_wp_version_tuple = explode('.', $wp_version);
define('WORDBOOKER_WP_VERSION', $wordbooker_wp_version_tuple[0] * 10 + $wordbooker_wp_version_tuple[1]);

if (function_exists('json_encode')) {
	define('WORDBOOKER_JSON_ENCODE', 'PHP');
} else {
	define('WORDBOOKER_JSON_ENCODE', 'Wordbook');
}

if (function_exists('json_decode') ) {
	define('WORDBOOKER_JSON_DECODE', 'PHP');
} else {
	define('WORDBOOKER_JSON_DECODE', 'Wordbooker');
}
if (function_exists('simplexml_load_string') ) {
	define('WORDBOOKER_SIMPLEXML', 'provided by PHP');
} else {
	define('WORDBOOKER_SIMPLEXML', 'is missing - this is a problem');
}


function wordbooker_load_apis() {
	if (WORDBOOKER_JSON_DECODE == 'Wordbooker') {
	function json_decode($json)
	{ 
	    $comment = false;
	    $out = '$x=';
	   
	    for ($i=0; $i<strlen($json); $i++)
	    {
		if (!$comment)
		{
		    if ($json[$i] == '{')        $out .= ' array(';
		    else if ($json[$i] == '}')    $out .= ')';
		    else if ($json[$i] == ':')    $out .= '=>';
		    else                         $out .= $json[$i];           
		}
		else $out .= $json[$i];
		if ($json[$i] == '"')    $comment = !$comment;
	    }
	    eval($out . ';');
	    return $x;
	} 
	} 

	if (WORDBOOKER_JSON_ENCODE == 'Wordbooker') {
		function json_encode($var) {
			if (is_array($var)) {
				$encoded = '{';
				$first = true;
				foreach ($var as $key => $value) {
					if (!$first) {
						$encoded .= ',';
					} else {
						$first = false;
					}
					$encoded .= "\"$key\":"
						. json_encode($value);
				}
				$encoded .= '}';
				return $encoded;
			}
			if (is_string($var)) {
				return "\"$var\"";
			}
			return $var;
		}
	}
}


/******************************************************************************
 * Wordbook options.
 */

function wordbooker_options() {
	return get_option(WORDBOOKER_SETTINGS);
}

function wordbooker_set_options($options) {
	update_option(WORDBOOKER_SETTINGS, $options);
}

function wordbooker_get_option($key) {
	$options = wordbooker_options();
	return isset($options[$key]) ? $options[$key] : null;
}

function wordbooker_set_option($key, $value) {
	$options = wordbooker_options();
	$options[$key] = $value;	
	wordbooker_set_options($options);
}

function wordbooker_delete_option($key) {
	$options = wordbooker_options();
	unset($options[$key]);
	update_option(WORDBOOKER_SETTINGS, $options);
}


/******************************************************************************
 * DB schema.
 */

function wordbooker_activate() {
	global $wpdb, $table_prefix;
	wp_cache_flush();
	$errors = array();
	$result = $wpdb->query('
		CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_POSTLOGS . '  (
			  `post_id` bigint(20) NOT NULL,
			  `blog_id` bigint(20) NOT NULL,
			  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (`blog_id`,`post_id`)
			) DEFAULT CHARSET=utf8;
		');
	if ($result === false)
		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_POSTLOGS;

	$result = $wpdb->query('
		CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_ERRORLOGS . ' (
			`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `user_ID` bigint(20) unsigned NOT NULL,
			  `method` longtext NOT NULL,
			  `error_code` int(11) NOT NULL,
			  `error_msg` longtext NOT NULL,
			  `post_id` bigint(20) NOT NULL,
			  `blog_id` bigint(20) NOT NULL,
			   `sequence_id` bigint(20) NOT NULL auto_increment,
		           `diag_level` int(4) default NULL,
		           PRIMARY KEY  (`sequence_id`),
		           KEY `timestamp_idx` (`timestamp`),
		           KEY `blog_idx` (`blog_id`)	
			) DEFAULT CHARSET=utf8;
		');
	if ($result === false)
		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_ERRORLOGS;

	$result = $wpdb->query('
		CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_USERDATA . ' (
			`user_ID` bigint(20) unsigned NOT NULL,
			  `uid` varchar(80) default NULL,
			  `expires` varchar(80) default NULL,
			  `access_token` varchar(255) default NULL,
			  `sig` varchar(80) default NULL,
			  `use_facebook` tinyint(1) default 1,
			  `onetime_data` longtext,
			  `facebook_error` longtext,
			  `secret` varchar(80) default NULL,
			  `session_key` varchar(80) default NULL,
			  `facebook_id` varchar(40) default NULL,
			  `name` varchar(250) default NULL,
			  `status` varchar(2048) default NULL,
			  `updated` int(20) default NULL,
			  `url` varchar(250) default NULL,
			  `pic` varchar(250) default NULL,
			  `pages` longtext,
			  `auths_needed` int(1) default NULL,
			  `blog_id` bigint(20) default NULL,
			  PRIMARY KEY  (`user_ID`),
			  KEY `facebook_idx` (`facebook_id`)
			) DEFAULT CHARSET=utf8;
		');
	if ($result === false)
		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_USERDATA;

	$result = $wpdb->query('
		CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_POSTCOMMENTS . ' (
		  `fb_post_id` varchar(40) NOT NULL,
		  `user_id` bigint(20) NOT NULL,
		  `comment_timestamp` int(20) NOT NULL,
		  `wp_post_id` int(11) NOT NULL,
		  `blog_id` bigint(20) NOT NULL,
		  `wp_comment_id` int(20) NOT NULL,
		  `fb_comment_id` varchar(40) default NULL,
		  `in_out` varchar(20) default NULL,
		  UNIQUE KEY `fb_comment_id_idx` (`fb_comment_id`),
		  KEY `in_out_idx` (`in_out`),
		  KEY `main_index` (`blog_id`,`wp_post_id`,`fb_post_id`,`wp_comment_id`)
			)  DEFAULT CHARSET=utf8;
		');
	if ($result === false)
		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_POSTCOMMENTS;

	$result = $wpdb->query('
		CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_USERSTATUS . ' (
			  `user_ID` bigint(20) unsigned NOT NULL,
			  `name` varchar(250)  default NULL,
			  `status` varchar(2048)  default NULL,
			  `updated` int(20) default NULL,
			  `url` varchar(250)  default NULL,
			  `pic` varchar(250)  default NULL,
			  `blog_id` bigint(20) NOT NULL default 0,
			  `facebook_id` varchar(40) default NULL,
			  PRIMARY KEY  (`user_ID`,`blog_id`)
			)  DEFAULT CHARSET=utf8;
		');
		if ($result === false)

		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_USERSTATUS;
	$result = $wpdb->query(' CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_FB_FRIENDS . ' (
	  `user_id` int(11) NOT NULL,
	  `blog_id` bigint(20) NOT NULL,
	  `facebook_id` varchar(20) NOT NULL,
	  `name` varchar(200) NOT NULL,
	  PRIMARY KEY  (`user_id`,`facebook_id`,`blog_id`),
	  KEY `user_id_idx` (`user_id`),
	  KEY `fb_id_idx` (`facebook_id`)
	)  DEFAULT CHARSET=utf8;
		');
		if ($result === false)
		$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_FB_FRIENDS;

		$result = $wpdb->query('
			CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_FB_FRIEND_LISTS . ' (
	  `user_id` int(11) NOT NULL,
	  `flid` varchar(80) NOT NULL,
	  `owner` varchar(80) NOT NULL,
	  `name` varchar(240) NOT NULL,
	  PRIMARY KEY  (`user_id`,`flid`)
	)  DEFAULT CHARSET=utf8;

		');
		if ($result === false)
			$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_FB_FRIEND_LISTS;


	$result = $wpdb->query(' CREATE TABLE IF NOT EXISTS ' . WORDBOOKER_PROCESS_QUEUE . ' (
	  `entry_type` varchar(20) NOT NULL,
	  `blog_id` int(11) NOT NULL,
	  `post_id` int(11) NOT NULL,
	  `priority` int(11) NOT NULL,
	  `status` varchar(20) NOT NULL,
	  PRIMARY KEY  (`blog_id`,`post_id`)
	) DEFAULT CHARSET=utf8;
		');
		if ($result === false)
			$errors[] = __('Failed to create ', 'wordbooker') . WORDBOOKER_PROCESS_QUEUE ;

	if ($errors) {
		echo '<div id="message" class="updated fade">' . "\n";
		foreach ($errors as $errormsg) {
			_e("$errormsg<br />\n", 'wordbooker');
		}
		echo "</div>\n";
		#return;
	}
	wordbooker_set_option('schema_vers', WORDBOOKER_SCHEMA_VERSION );
	$wordbooker_settings=wordbooker_options();
	#Setup the cron. We clear it first in case someone did a dirty de-install.
	$dummy=wp_clear_scheduled_hook('wb_cron_job');
	$dummy=wp_schedule_event(time(), 'hourly', 'wb_cron_job');
}

function wordbooker_rrmdir($dir)
{
    if (is_dir($dir)) // ensures that we actually have a directory
    {
        $objects = scandir($dir); // gets all files and folders inside
        foreach ($objects as $object)
        {
            if ($object != '.' && $object != '..')
            {	
                if (filetype($dir . '/' . $object) === 'dir')
                {
                    // if we find a directory, do a recursive call
                    wordbooker_rrmdir($dir . '/' . $object);
                }
                else
                {
                    // if we find a file, simply delete it
                    unlink($dir . '/' . $object);
                }
            }
        }
        // the original directory is now empty, so delete it
        rmdir($dir);
    }
}

function wordbooker_upgrade() {
	global $wpdb, $table_prefix,$blog_id;
	$errors = array();
	#tidies up after an SVN crapout
	if(file_exists(dirname(__FILE__).'/trunk')) {if (filetype(dirname(__FILE__).'/trunk') == 'dir') {wordbooker_rrmdir(dirname(__FILE__).'/trunk');}}
	# Removes an unwanted file.
	if(file_exists(dirname(__FILE__).'/includes/wordbooker_channel.php')) {unlink(dirname(__FILE__).'/includes/wordbooker_channel.php'); }
	# We use this to make changes to Schema versions. We need to get the current schema version the user is using and then "upgrade" the various tables.
	$wordbooker_settings=wordbooker_options();
	if (! isset($wordbooker_settings['schema_vers'])) {wordbooker_activate(); return;}
	if ($wordbooker_settings['schema_vers']< (float) WORDBOOKER_SCHEMA_VERSION ) { 
		 _e("Database changes being applied", 'wordbooker');
	} else {
		return;
	}
	
	if ($wordbooker_settings['schema_vers']=='2') {

		$result = $wpdb->query('
		ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' DROP PRIMARY KEY , DROP INDEX fb_comment_id, 
		ADD PRIMARY KEY ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) 
		');
		wordbooker_set_option('schema_vers', "2.1");
	}

	if ($wordbooker_settings['schema_vers']=='2.1') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `user_id` BIGINT( 20 ) NOT NULL  ');
		wordbooker_set_option('schema_vers', "2.2");
	}

	if ($wordbooker_settings['schema_vers']=='2.2') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `sequence_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT , ADD `diag_level` INT(4) NULL, ADD PRIMARY KEY ( `sequence_id` ) ');
		wordbooker_set_option('schema_vers', "2.3");
	}

	if ($wordbooker_settings['schema_vers']=='2.3') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `sequence_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT , ADD `diag_level` INT(4) NULL, ADD PRIMARY KEY ( `sequence_id` ) ');
		wordbooker_set_option('schema_vers', "2.4");
	}

	if ($wordbooker_settings['schema_vers']=='2.4') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `fb_comment_id` VARCHAR( 40 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' DROP PRIMARY KEY,ADD PRIMARY KEY ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');	 
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `diag_level` INT(4) NULL ');	
		wordbooker_set_option('schema_vers', "2.5");
	}

	if ($wordbooker_settings['schema_vers']=='2.5') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  DROP PRIMARY KEY , ADD INDEX `main_index` ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `in_out` VARCHAR( 20 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `in_out_idx` ( `in_out` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `fb_comment_id_idx` (`fb_comment_id`) '); 
		$result = $wpdb->query('DELETE FROM '. WORDBOOKER_POSTCOMMENTS. '  where user_id=0');
		$result = $wpdb->query('DELETE FROM '. WORDBOOKER_POSTCOMMENTS. '  where wp_post_id=0');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `diag_level` INT(4) NULL ');	
		wordbooker_set_option('schema_vers', "2.6");
	}

	if ($wordbooker_settings['schema_vers']=='3') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' DROP PRIMARY KEY , DROP INDEX fb_comment_id, ADD PRIMARY KEY ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `user_id` BIGINT( 20 ) NOT NULL  ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `sequence_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT , ADD `diag_level` INT(4) NULL, ADD PRIMARY KEY ( `sequence_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `fb_comment_id` VARCHAR( 40 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `diag_level` INT(4) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  DROP PRIMARY KEY , ADD INDEX `main_index` ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `in_out` VARCHAR( 20 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `in_out_idx` ( `in_out` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `fb_comment_id_idx` (`fb_comment_id`) '); 
		$result = $wpdb->query('DELETE FROM '. WORDBOOKER_POSTCOMMENTS. '  where user_id=0');
		$result = $wpdb->query('DELETE FROM '. WORDBOOKER_POSTCOMMENTS. '  where wp_post_id=0');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `diag_level` INT(4) NULL ');
		wordbooker_set_option('schema_vers', "3");
	}	

	if ($wordbooker_settings['schema_vers']!='4') {
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' DROP PRIMARY KEY ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' DROP INDEX fb_comment_id,');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD PRIMARY KEY ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. ' ADD `user_id` BIGINT( 20 ) NOT NULL  ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. '  ADD `sequence_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. '  ADD PRIMARY KEY ( `sequence_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '   ADD `fb_comment_id` VARCHAR( 40 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  DROP PRIMARY KEY ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `main_index` ( `blog_id` , `wp_post_id` , `fb_post_id` , `wp_comment_id` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD `in_out` VARCHAR( 20 ) NULL ');	
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `in_out_idx` ( `in_out` ) ');
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_POSTCOMMENTS. '  ADD INDEX `fb_comment_id_idx` (`fb_comment_id`) '); 
		$result = $wpdb->query('ALTER TABLE '. WORDBOOKER_ERRORLOGS. ' ADD `diag_level` INT(4) NULL ');
		wordbooker_set_option('schema_vers', "4");
	}
	$dummy=wp_clear_scheduled_hook('wb_cron_job');
	$dummy=wp_schedule_event(time(), 'hourly', 'wb_cron_job');
	#wordbooker_set_option('schema_vers', WORDBOOKER_SCHEMA_VERSION );
	wp_cache_flush();
}

function wordbooker_delete_user($user_id,$level) {
	global $wpdb;
	$errors = array();
	$table_array[1]=array(WORDBOOKER_USERDATA);
	$table_array[2]=array(WORDBOOKER_USERDATA,WORDBOOKER_USERSTATUS);
	$table_array[3]=array(WORDBOOKER_USERDATA,WORDBOOKER_USERSTATUS,WORDBOOKER_FB_FRIENDS,WORDBOOKER_FB_FRIEND_LISTS);
	foreach ($table_array[$level] as $tablename) {
		$result = $wpdb->query('DELETE FROM ' . $tablename . ' WHERE user_ID = ' . $user_id . '');
	}
	if ($errors) {
		echo '<div id="message" class="updated fade">' . "\n";
		foreach ($errors as $errormsg) {
			_e("$errormsg<br />\n", 'wordbooker');
		}
		echo "</div>\n";
	}
}

/******************************************************************************
 * Wordbook user data.
 */
function wordbooker_get_userdata($user_id) {
	global $wpdb;
	$sql='SELECT onetime_data,facebook_error,secret,session_key,user_ID,access_token,facebook_id,pages,name FROM ' . WORDBOOKER_USERDATA . ' WHERE user_ID = ' . $user_id ;
	$rows = $wpdb->get_results($sql);
	if ($rows) {
		$rows[0]->onetime_data = unserialize($rows[0]->onetime_data);
		$rows[0]->facebook_error = unserialize($rows[0]->facebook_error);
		$rows[0]->secret = unserialize($rows[0]->secret);
		$rows[0]->session_key = unserialize($rows[0]->session_key);
		$rows[0]->access_token = unserialize($rows[0]->access_token);
		$rows[0]->pages = unserialize($rows[0]->pages);
		return $rows[0];
	}
	return null;
}

function wordbooker_set_userdata($onetime_data, $facebook_error,$secret, $session,$facebook_id) {
	global $user_ID, $wpdb,$blog_id;
	wordbooker_delete_userdata();
	$sql= "	INSERT INTO " . WORDBOOKER_USERDATA . " (
			user_ID
			, onetime_data
			, facebook_error
			, secret
			, session_key
			, uid
			, expires
			, access_token
			, sig
			,blog_id
			,facebook_id
		) VALUES (
			" . $user_ID . "
			, '" . serialize($onetime_data) . "'
			, '" . serialize($facebook_error) . "'
			, '" . serialize($secret) . "'
			, '" . serialize($session->session_key)."'
			, '". serialize($session->uid)."'
			, '". serialize($session->expires)."'
			, '". serialize($session->access_token)."'
			, '". serialize($session->sig)."'
			, " . $blog_id . "
			, '". $facebook_id."'
	)";
	$result = $wpdb->query($sql);
}

function wordbooker_set_userdata2( $onetime_data, $facebook_error, $secret, $session_key,$user_ID) {
	global $wpdb;
	$sql= "Update " . WORDBOOKER_USERDATA . " set
 			  onetime_data =  '" . serialize($onetime_data) . "'
			, facebook_error = '" . serialize($facebook_error) . "'
			, secret = '" . serialize($secret) . "'
			, session_key = '" . serialize($session_key) . "'
		 where user_id=".$user_ID;
	$result = $wpdb->query($sql);
}


function wordbooker_update_userdata($wbuser) {
	return wordbooker_set_userdata2( $wbuser->onetime_data, $wbuser->facebook_error, $wbuser->secret, $wbuser->session_key,$wbuser->user_ID);
}

function wordbooker_set_userdata_facebook_error($wbuser, $method, $error_code, $error_msg, $post_id) {
	$wbuser->facebook_error = array(
		'method' => $method,
		'error_code' => mysql_real_escape_string ($error_code),
		'error_msg' => mysql_real_escape_string ($error_msg),
		'postid' => $post_id,
		);
	wordbooker_update_userdata($wbuser);
	wordbooker_append_to_errorlogs($method, $error_code, $error_msg, $post_id,$wbuser->user_ID);
}

function wordbooker_clear_userdata_facebook_error($wbuser) {
	$wbuser->facebook_error = null;
	return wordbooker_update_userdata($wbuser);
}

function wordbooker_remove_user(){
	global $user_ID;
	# Delete the user's meta
	$wordbooker_user_settings_id="wordbookuser".$blog_id;
	delete_usermeta( $user_ID, $wordbooker_user_settings_id);
	# Then go and delete their data from the tables
	wordbooker_delete_user($user_ID,3);
}

function wordbooker_delete_userdata() {
	global $user_ID;
	wordbooker_delete_user($user_ID,2);
}

/******************************************************************************
 * Post logs - record time of last post to Facebook
 */

function wordbooker_trim_postlogs() {
	# Forget that something has been posted to Facebook if it's been there  more than a year. 
	global $wpdb;
	$result = $wpdb->query('
		DELETE FROM ' . WORDBOOKER_POSTLOGS . '
		WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 365 DAY)
		');
}

function wordbooker_postlogged($post_id,$tstamp=0) {
	global $wpdb,$wordbooker_post_options,$post;
	$wordbooker_settings = wordbooker_options();
	$wbo=get_post_meta($post_id, '_wordbooker_options', true); 
	#if ($wbo["wordbooker_publish_default"]!='published') {
	$time=time() ;
	if (! isset($wordbooker_settings['wordbooker_republish_time_frame'])) $wordbooker_settings['wordbooker_republish_time_frame']='3';
	$sql='SELECT '. $time ." - UNIX_TIMESTAMP(post_date) as time, post_date_gmt,post_date,post_modified, post_modified_gmt,post_status FROM $wpdb->posts WHERE ID = " . $post_id;
	$rows = $wpdb->get_results($sql);
	
	wordbooker_debugger("Post is this old (Seconds) : ",$rows[0]->time,$post_id) ;
	#wordbooker_debugger("Post date : ",$rows[0]->post_date,$post_id) ;
	#wordbooker_debugger("Post modified : ",$rows[0]->post_modified,$post_id) ;
	#wordbooker_debugger("Post status : ",$rows[0]->post_status,$post_id) ;
	#wordbooker_debugger("Post status flag : ",$wbo['wordbooker_new_post'],$post_id) ;
	#wordbooker_debugger("Scheduled Post: ",$wbo['wordbooker_scheduled_post'],$post_id) ;
	if ($tstamp==1) { return $rows[0]->time;}	
	if ($tstamp==1 && !isset($_POST['original_post_status']) && !isset($_POST['screen'])) {return 0;}
	
	# If the post isn't actually being published we give up - just in case
	if ($rows[0]->post_status!='publish') {	return true;}
	# If the post is new then return false
	if ($rows[0]->post_date == $rows[0]->post_modified) {return false;}
	if ($wbo['wordbooker_scheduled_post']!=0) {
		$wbo['wordbooker_scheduled_post']=0;
		$y=update_post_meta($post_id, '_wordbooker_options', $wbo); 
		return false;
	}
	if ($wbo['wordbooker_new_post']!=0) { 
		$wbo['wordbooker_new_post']=0;
		$y=update_post_meta($post_id, '_wordbooker_options', $wbo); 
		return false;
	}
	#}
	wordbooker_debugger("This post has already been published. So do checks "," ",$post_id) ;
	#if (!isset($_POST['original_post_status']) && !isset($_POST['screen'])) {return false;}
	// See if the user has overridden the repost on edit - i.e. they want to publish and be damned!
	if (isset ($wordbooker_post_options["wordbooker_publish_default"])) { 
		wordbooker_debugger("Publish Post is set so user wants to republish "," ",$post_id) ;
		return false;
	}
	return true;
}


function wordbooker_insert_into_postlogs($post_id,$blog_id) {
	global $wpdb;
	wordbooker_delete_from_postlogs($post_id,$blog_id);
	if (!WORDBOOKER_TESTING) {
		$result = $wpdb->query(' INSERT INTO ' . WORDBOOKER_POSTLOGS . ' (post_id,blog_id) VALUES (' . $post_id . ','.$blog_id.')');
	}
}

function wordbooker_insert_into_process_queue($post_id,$blog_id,$entry_type) {
	global $wpdb;
		$result = $wpdb->query(' INSERT INTO ' . WORDBOOKER_PROCESS_QUEUE . ' (entry_type,blog_id,post_id,status) VALUES ("' . $entry_type. '",' .$blog_id .',' . $post_id . ',"B")');
}

function wordbooker_delete_from_process_queue($post_id,$blog_id) {
	global $wpdb,$blog_id;
		$result = $wpdb->query(' DELETE FROM ' . WORDBOOKER_PROCESS_QUEUE . ' where post_id='.$post_id.' and blog_id='.$blog_id);
}

function wordbooker_delete_from_postlogs($post_id,$blog_id) {
	global $wpdb,$blog_id;
	$result = $wpdb->query('DELETE FROM ' . WORDBOOKER_POSTLOGS . '	WHERE post_id = ' . $post_id . ' and blog_id='.$blog_id);
}

function wordbooker_delete_from_commentlogs($post_id,$blog_id) {
	global $wpdb;
	$result = $wpdb->query('DELETE FROM ' . WORDBOOKER_POSTCOMMENTS . ' WHERE wp_post_id = ' . $post_id . ' and blog_id='.$blog_id);
}

/******************************************************************************
 * Error logs - record errors
 */

function wordbooker_hyperlinked_method($method) {
	return '<a href="'. WORDBOOKER_FB_DOCPREFIX . $method . '"'. ' title="Facebook API documentation" target="facebook"'. '>'. $method. '</a>';
}

function wordbooker_trim_errorlogs() {
		global $user_ID, $wpdb,$blog_id;
	$result = $wpdb->query('
		DELETE FROM ' . WORDBOOKER_ERRORLOGS . '
		WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 2 DAY)  and blog_id ='.$blog_id);
}

function wordbooker_clear_errorlogs() {
	global $user_ID, $wpdb,$blog_id;
	$result = $wpdb->query('
		DELETE FROM ' . WORDBOOKER_ERRORLOGS . '
		WHERE user_ID = ' . $user_ID . ' and error_code > -1  and blog_id ='.$blog_id);
	if ($result === false) {
		echo '<div id="message" class="updated fade">';
		_e('Failed to clear error logs.', 'wordbooker');
		echo "</div>\n";
	}
}


function wordbooker_clear_diagnosticlogs() {
	global $user_ID, $wpdb,$blog_id;
	$result = $wpdb->query('
		DELETE FROM ' . WORDBOOKER_ERRORLOGS . '
		WHERE blog_id ='.$blog_id.' and user_ID='.$user_ID);
	if ($result === false) {
		echo '<div id="message" class="updated fade">';
		_e('Failed to clear Diagnostic logs.', 'wordbooker');
		echo "</div>\n";
	}
}
function wordbooker_append_to_errorlogs($method, $error_code, $error_msg,$post_id,$user_id) {
	global $user_ID, $wpdb,$blog_id;
	if ($post_id == null) {
		$post_id = 0;
	} else {
		$post = get_post($post_id);
	}
	$result = $wpdb->insert(WORDBOOKER_ERRORLOGS,
		array('user_ID' => $user_id,
			'method' => $method,
			'error_code' => $error_code,
			'error_msg' => $error_msg,
			'post_id' => $post_id,
			'blog_id' => $blog_id,
			'diag_level'=> 900
			),
		array('%d', '%s', '%d', '%s', '%d','%d')
		);	 
}

function wordbooker_delete_from_errorlogs($post_id) {
	global $wpdb,$blog_id;
	$result = $wpdb->query('DELETE FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE post_id = ' . $post_id .' and blog_id ='.$blog_id );
}

function wordbooker_render_errorlogs() {
	global $user_ID, $wpdb,$blog_id;
	$diaglevel=wordbooker_get_option('wordbooker_advanced_diagnostics_level');
	#echo "!!!!".$user_ID;
	$count_rows = $wpdb->get_results('SELECT count(*) as count FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE user_ID = ' . $user_ID . '  and blog_id='.$blog_id);
	$rows = $wpdb->get_results('SELECT * FROM ' . WORDBOOKER_ERRORLOGS . ' WHERE user_ID = ' . $user_ID . '  and blog_id='.$blog_id.' and diag_level >='.$diaglevel.' order by sequence_id asc');
	if ($count_rows[0]->count >= 1) {
?>
	<h3>
<?php _e('Diagnostic Messages', 'wordbooker');
 $x=sprintf(__('(Showing %1$s from a total of %2$s rows)'), count($rows), $count_rows[0]->count);
 echo ' '.$x;
 ?></h3>
	<div class="wordbooker_errors">
	<p>
	</p>
<?php if (count($rows) > 0 ) {
?>
	<table class="wordbooker_errorlogs">
		<tr>
			<th>Post</th>
			<th>Time</th>
			<th>Action</th>
			<th>Message</th>
			<th>Error Code</th>
		</tr>
<?php
	foreach ($rows as $row) {
		$row_type=array(-1=>"Cache Refresh",-2=>"Comment Processing (Admin Diag)",-3=>"Comment Processing (User Diag)",-4=>"Post Deletion"); 
		$hyperlinked_post = '';
		if (($post = get_post($row->post_id))) {
			$hyperlinked_post = '<a href="'. get_permalink($row->post_id) . '">'. apply_filters('the_title',get_the_title($row->post_id)) . '</a>';
		}
		$hyperlinked_method= wordbooker_hyperlinked_method($row->method);
		if ($row->error_code>1){ echo "<tr class='error'>";} else {echo "<tr class='diag'>";}
?>
			<td><?php if ($row->post_id>0) { echo $hyperlinked_post;} else { echo $row_type[$row->post_id];}  ?></td>
			<td><?php echo $row->timestamp; ?></td>
			<td><?php echo $row->method; ?></td>
			<td><?php echo stripslashes($row->error_msg); ?></td>
			<td><?php if ($row->error_code>1) {echo $row->error_code;} else { echo "-";}  ?></td>
		</tr>

<?php
		}

	echo "</table> ";
	}
?>
	<form action="<?php echo WORDBOOKER_SETTINGS_URL; ?>" method="post">
		<input type="hidden" name="action" value="clear_errorlogs" />
		<p class="submit" style="text-align: center;">
		<input type="submit" value="<?php _e('Clear Diagnostic Messages', 'wordbooker'); ?>" />
		</p>
	</form>
	</div>
	<hr>
<?php
	}
}


/******************************************************************************
 * Wordbooker setup and administration.
 */

function wordbooker_admin_load() {

	if (isset($POST['reset_user_config'])){
		wordbooker_delete_userdata();
	return;}
	if (!$_POST['action'])
		return;

	switch ($_POST['action']) {

	case 'delete_userdata':
		# Catch if they got here using the perm_save/cache refresh
		
		if ( ! isset ($_POST["perm_save"])) {
			wordbooker_delete_userdata();
		}
		wp_redirect(WORDBOOKER_SETTINGS_URL);
		break;

	case 'clear_errorlogs':
		wordbooker_clear_diagnosticlogs();
		wp_redirect(WORDBOOKER_SETTINGS_URL);
		break;

	case 'clear_diagnosticlogs':
		wordbooker_clear_diagnosticlogs();
		wp_redirect(WORDBOOKER_SETTINGS_URL);
		break;

	case 'no_facebook':
		wordbooker_set_userdata(false, null, null, null,null,null);
		wp_redirect('/wp-admin/index.php');
		break;
	}

	exit;
}

function wordbooker_admin_head() {
?>
	<style type="text/css">
	.wordbooker_setup { margin: 0 3em; }
	.wordbooker_notices { margin: 0 3em; }
	.wordbooker_status { margin: 0 3em; }
	.wordbooker_errors { margin: 0 3em; }
	.wordbooker_thanks { margin: 0 3em; }
	.wordbooker_thanks ul { margin: 1em 0 1em 2em; list-style-type: disc; }
	.wordbooker_support { margin: 0 3em; }
	.wordbooker_support ul { margin: 1em 0 1em 2em; list-style-type: disc; }
	.facebook_picture {
		float: right;
		border: 1px solid black;
		padding: 2px;
		margin: 0 0 1ex 2ex;
	}
	.wordbooker_errorcolor { color: #c00; }
	table.wordbooker_errorlogs { text-align: center; }
	table.wordbooker_errorlogs th, table.wordbooker_errorlogs td {
		padding: 0.5ex 1.5em;
	}
	table.wordbooker_errorlogs th { background-color: #999; }
	table.wordbooker_errorlogs tr.error td { background-color: #f66; }
	table.wordbooker_errorlogs tr.diag td { background-color: #CCC; }
	.DataForm label
	{
	    display: inline-block;
	    vertical-align:top;
	}
	</style>
<?php
}

function wordbooker_option_notices() {
	global $user_ID, $wp_version,$blog_id;
	wordbooker_upgrade();
	wordbooker_trim_postlogs();
	wordbooker_trim_errorlogs();
	$errormsg = null;
	if (!function_exists('curl_init')) {
		$errormsg .=  __('Wordbooker needs the CURL PHP extension to work. Please install / enable it and try again','wordbooker').' <br />';
	}
	if (!function_exists('json_decode')) {
	 	$errormsg .=   __('Wordbooker needs the JSON PHP extension.  Please install / enable it and try again ','wordbooker').'<br />';
	}

	if (!function_exists('simplexml_load_string')) {
		$errormsg .=   __('Your PHP install is missing <code>simplexml_load_string()</code> ','wordbooker')."<br />";
	}
	$wbuser = wordbooker_get_userdata($user_ID);
	if (strlen($wbuser->access_token)< 50 ) {
		$errormsg .=__("Wordbooker needs to be set up", 'wordbooker')."<br />";
	} else if ($wbuser->facebook_error) {
		$method = $wbuser->facebook_error['method'];
		$error_code = $wbuser->facebook_error['error_code'];
		$error_msg = $wbuser->facebook_error['error_msg'];
		$post_id = $wbuser->facebook_error['postid'];
		$suffix = '';
		if ($post_id != null && ($post = get_post($post_id))) {
			wordbooker_delete_from_postlogs($post_id,$blog_id);
			$suffix = __('for', 'wordbooker').' <a href="'. get_permalink($post_id) . '">'. get_the_title($post_id) . '</a>';
		}
		$errormsg .= sprintf(__("<a href='%s'>Wordbooker</a> failed to communicate with Facebook" . $suffix . ": method = %s, error_code = %d (%s). Your blog is OK, but Facebook didn't get the update.", 'wordbooker'), " ".WORDBOOKER_SETTINGS_URL," ".wordbooker_hyperlinked_method($method)," ".$error_code," ".$error_msg)."<br />";
		wordbooker_clear_userdata_facebook_error($wbuser);
	}

	if ($errormsg) {
?>

	<h3><?php _e('Notices', 'wordbooker'); ?></h3>

	<div class="wordbooker_notices" style="background-color: #f66;">
	<p><?php echo $errormsg; ?></p>
	</div>

<?php
	}
}

function wordbooker_renew_access_token ($userid=null) {
	global $wpdb,$user_ID;
	if(is_null($userid)){$userid=$user_ID;}
	$sql="select user_ID,access_token,updated from ".WORDBOOKER_USERDATA." where user_ID=".$userid;
	$result = $wpdb->get_results($sql);
	$today=date('z');
	foreach($result as $user_row){
		if ($user_row->updated==$today) {
	#	wordbooker_debugger("Access token already updated today"," ",-1,99) ; 
		} else {
		$ret_code=wordbooker_get_access_token(unserialize($user_row->access_token));
		$x=split('&',$ret_code);
		$x=split('=',$x[0]);
		$access_token=$x[1];
		$sql= "Update " . WORDBOOKER_USERDATA . " set access_token = '" . serialize($access_token) . "', updated=".$today." where user_id=".$userid;
		var_dump($sql);
		$result = $wpdb->query($sql);
		#wordbooker_debugger("Access token updated"," ",-1,99) ;
		}
	}
}

function get_check_session(){
	global $facebook2,$user_ID;
	# This function basically checks for a stored session and if we have one it returns it, If we have no stored session then it gets one and stores it
	# OK lets go to the database and see if we have a session stored

	wordbooker_debugger("Getting Userdata "," ",0) ;
	$session = wordbooker_get_userdata($user_ID);
	if (strlen($session->access_token)>5) {
		wordbooker_debugger("Session found. Check validity "," ",0) ;
		# We have a session ID so lets not get a new one
		# Put some session checking in here to make sure its valid
		try {
		wordbooker_debugger("Calling Facebook API : get current user "," ",0) ;
		$ret_code=wordbooker_me($session->facebook_id,$session->access_token);
		}	
		catch (Exception $e) {
		# We don't have a good session so
		wordbooker_debugger("User Session invalid - clear down data "," ",0) ;
		#wordbooker_delete_user($user_ID,1);
		return;
	}
		return $session->access_token;
	} 
	else 
	{
		# Are we coming back from a login with a session set? 
		$zz=htmlspecialchars_decode ($_POST['session'])."<br>";
		$oldkey=explode("|",$zz);
		$newkey=explode("&expires",$zz);
		$session->access_token=$newkey[0];
		$session->session_key=$oldkey[1];
		$session->expires=0;	
		$ret_code=wordbooker_me_status($session->facebook_id,$session->access_token);
		wordbooker_debugger("Checking session (2) "," ",0) ;
		if (strlen($session->access_token)>5){
		wordbooker_debugger("Session found. Store it "," ",0) ;
			# Yes! so lets store it
		wordbooker_set_userdata($onetime_data, $facebook_error, $secret,$session,$ret_code->id);
			return $session->access_token;
		}
		
	} 
}


function wordbooker_option_setup($wbuser) {
?>

	<h3><?php _e('Setup', 'wordbooker'); ?></h3>
	<div class="wordbooker_setup">
<?php
	$access_token=get_check_session();
	$loginUrl2='https://www.facebook.com/dialog/oauth?client_id='.WORDBOOKER_FB_ID.'&redirect_uri=https://wordbooker.tty.org.uk/index2.html?br='.urlencode(get_bloginfo('wpurl').'&fbid='.WORDBOOKER_FB_ID).'&scope=publish_actions,publish_stream,offline_access,user_status,read_stream,email,user_groups,manage_pages,read_friendlists&response_type=token';

	if ( is_null($access_token) ) {
	wordbooker_debugger("No session found - lets login and authorise "," ",0,99) ;
			echo "<br />".__("Secure link ( may require you to add a new certificate for wordbooker.tty.org.uk ) Also you may get a warning about passing data on a non secure connection :",'wordbooker').'<br /><br /> <a href="'. $loginUrl2.'"> <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif" alt="Facebook Login Button" /> </a><br />';
	}
	 else  {
		wordbooker_debugger("Everything looks good so lets ask them to refresh "," ",0,99) ;
			echo __("Wordbooker should now be authorised. Please click on the Reload Page Button",'wordbooker').'<br> <form action="options-general.php?page=wordbooker" method="post">';
		echo '<p style="text-align: center;"><input type="submit" name="perm_save" class="button-primary" value="'. __('Reload Page', 'wordbooker').'" /></p>';
		echo '</form> '; 
	}
	echo "</div></div>";
}

function wordbooker_status($user_id)
{
	echo '<h3>'.__('Status', 'wordbooker').'</h3>';
	global  $wpdb, $user_ID,$table_prefix,$blog_id;
	$wordbooker_user_settings_id="wordbookuser".$blog_id;
	$wordbookuser=get_usermeta($user_ID,$wordbooker_user_settings_id);
	if ($wordbookuser['wordbooker_disable_status']=='on') {return;}
	global $shortcode_tags;
	$result = wordbooker_get_cache($user_id);
?>		
	<div class="wordbooker_status">
	<div class="facebook_picture">
		<a href="<?php echo $result->url; ?>" target="facebook">
		<img src="<?php echo $result->pic; ?>" /></a>
		</div>
		<p>
		<a href="<?php echo $result->url; ?>"><?php echo $result->name; ?></a> ( <?php echo $result->facebook_id; ?> )<br /><br />
		<i><?php echo "<p>".$result->status; ?></i></p>
		(<?php 
			$current_offset=0;
			$current_offset = get_option('gmt_offset');
			echo date('D M j, g:i a', $result->updated+(3600*$current_offset)); ?>).
		<br /><br />
<?php

}

function wordbooker_option_status($wbuser) {
	global  $wpdb,$user_ID;
	# Go to the cache and try to pull details
	$fb_info=wordbooker_get_cache($user_ID,'use_facebook,facebook_id',1);
	# If we're missing stuff lets kick the cache.
	if (! isset($fb_info->facebook_id)) {
		 wordbooker_cache_refresh ($user_ID,$fbclient);
		$fb_info=wordbooker_get_cache($user_ID,'use_facebook,facebook_id',1); 
	}
		if ($fb_info->use_facebook==1) {
			echo"<p>".__('Wordbooker appears to be configured and working just fine', 'wordbooker');
			wordbooker_check_permissions($wbuser,$user);	
			echo "</p><p>".__("If you like, you can start over from the beginning (this does not delete your posting and comment history)", 'wordbooker').":</p>";
		} 
		else 
		{
			echo "<p>".__('Wordbooker is able to connect to Facebook', 'wordbooker').'</p>';
		} 

	echo'<form action="" method="post">';
	echo '<p style="text-align: center;"><input type="submit"  class="button-primary" name="reset_user_config"  value="'.__('Reset User Session', 'wordbooker').'" />';
	echo '&nbsp;&nbsp;<input type="submit" name="perm_save" class="button-primary" value="'. __('Refresh Status', 'wordbooker').'" /></p>';
	echo '</form> </div>';

	#wordbooker_renew_access_token();
    $description=__("Recent Facebook Activity for this site", 'wordbooker');

    $iframe='<iframe src="http://www.facebook.com/plugins/activity.php?site='.get_bloginfo('url').'&amp;width=600&amp;height=400&amp;header=true&amp;colorscheme=light&amp;font&amp;border_color&amp;recommendations=true"  scrolling="no" frameborder="no" style="border:none; overflow:hidden; width:600px; height:400px"></iframe>';
    $activity="<hr><h3>".$description.'</h3><p>'.$iframe."</p></div>";
	$options = wordbooker_options();
   if (isset($options["wordbooker_fb_rec_act"])) { echo $activity; }
}

function wordbooker_version_ok($currentvers, $minimumvers) {
	#Lets strip out the text and any other bits of crap so all we're left with is numbers.
	$currentvers=trim(preg_replace("/[^0-9.]/ ", "", $currentvers ));
	$current = preg_split('/\D+/', $currentvers);
	$minimum = preg_split('/\D+/', $minimumvers);
	for ($ii = 0; $ii < min(count($current), count($minimum)); $ii++) {	
		if ($current[$ii] < $minimum[$ii])
			return false;
	}
	if (count($current) < count($minimum))
		return false;
	return true;
}


function wordbooker_option_support() {
	global $wp_version,$wpdb,$user_ID,$facebook2;
	$wordbooker_settings=wordbooker_options();
?>
	<h3><?php _e('Support', 'wordbooker'); ?></h3>
	<div class="wordbooker_support">
	<?php _e('For feature requests, bug reports, and general support :', 'wordbooker'); ?>
	<ul>	
	<li><?php _e('Check the ', 'wordbooker'); ?><a href="../wp-content/plugins/wordbooker/documentation/wordbooker_user_guide.pdf" target="wordpress"><?php _e('User Guide', 'wordbooker'); ?></a>.</li>
	<li><?php _e('Check the ', 'wordbooker'); ?><a href="http://wordpress.org/extend/plugins/wordbooker/other_notes/" target="wordpress"><?php _e('WordPress.org Notes', 'wordbooker'); ?></a>.</li>
	<li><?php _e('Try the ', 'wordbooker'); ?><a href="http://wordbooker.tty.org.uk/forums/" target="facebook"><?php _e('Wordbooker Support Forums', 'wordbooker'); ?></a>.</li>
		<li><?php _e('Enhancement requests can be made at the ', 'wordbooker'); ?><a href="http://code.google.com/p/wordbooker/" target="facebook"><?php _e('Wordbooker Project on Google Code', 'wordbooker'); ?></a>.</li>
	<li><?php _e('Consider upgrading to the ', 'wordbooker'); ?><a href="http://wordpress.org/download/"><?php _e('latest stable release', 'wordbooker'); ?></a> <?php _e(' of WordPress. ', 'wordbooker'); ?></li>
	<li><?php _e('Read the release notes for Wordbooker on the ', 'wordbooker'); ?><a href="http://wordbooker.tty.org.uk/current-release/">Wordbooker</a> <?php _e('blog.', 'wordbooker'); ?></li>
	<li><?php _e('Check the Wordbooker ', 'wordbooker'); ?><a href="http://wordbooker.tty.org.uk/faqs/">Wordbooker</a> <?php _e('FAQs', 'wordbooker'); ?></li>
	</ul>
	<br />
	<?php _e('Please provide the following information about your installation:', 'wordbooker'); ?>
	<ul>
<?php
	$active_plugins = get_option('active_plugins');
	$plug_info=get_plugins();
	$phpvers = phpversion();
	$jsonvers=phpversion('json');
	if (!phpversion('json')) { $jsonvers="Installed but version not being returned";}
	$sxmlvers=phpversion('simplexml');
	if (!phpversion('simplexml')) { $sxmlvers=" No version being returned";} 
	$mysqlvers = function_exists('mysql_get_client_info') ? mysql_get_client_info() :  'Unknown';
	# If we dont have the function then lets go and get the version the old way
	if ($mysqlvers=="Unknown") {
		$t=mysql_query("select version() as ve");
		$r=mysql_fetch_object($t);
		$mysqlvers =  $r->ve;
	}
	$http_coding="No Multibyte support";
	$int_coding="No Multibyte support";
	$mb_language="No Multibyte support";
	if (function_exists('mb_convert_encoding')) {
		$http_coding=mb_http_output();
		$int_coding=mb_internal_encoding();
		$mb_language=mb_language();
	}
	$curlcontent=__("Curl is not installed",'wordbooker');
	if (function_exists('curl_init')) {
	  $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/platform');
	   curl_setopt($ch, CURLOPT_HEADER, 0);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	   curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/includes/fb_ca_chain_bundle.crt');
	   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
	   $curlcontent = @curl_exec($ch);
	   $x=json_decode($curlcontent);
	   $curlstatus=__("Curl is available but cannot access Facebook - This is a problem (",'wordbooker').curl_errno($ch)." - ". curl_error($ch) ." )";
	   if ($x->name=="Facebook Platform") {$curlstatus=__("Curl is available and can access Facebook - All is OK",'wordbooker');}
  	 curl_close($ch);
	}

	$new_wb_table_prefix=$wpdb->base_prefix;
	if (isset ($db_prefix) ) { $new_wb_table_prefix=$db_prefix;}
	$info = array(	
		'Wordbooker' => $plug_info['wordbooker/wordbooker.php']['Version'],
		'Wordbooker ID'=>WORDBOOKER_FB_ID,
		'Wordbooker Schema' => $wordbooker_settings['schema_vers'],
		'WordPress' => $wp_version,
		'Table prefix' =>$new_wb_table_prefix,
		 'PHP' => $phpvers,
		 'PHP Memory Limit' => ini_get('memory_limit'),
		 'PHP Memory Usage (MB)' => memory_get_usage(true)/1024/1024,
		'JSON Encode' => WORDBOOKER_JSON_ENCODE,
		'JSON Decode' => WORDBOOKER_JSON_DECODE,
		'Curl Status' => $curlstatus,
#		'Fopen Status' => $fopenstat2.$fopenstat,
		'JSON Version' => $jsonvers,
		'SimpleXML library' => $sxmlvers." (". WORDBOOKER_SIMPLEXML.")",
		'HTTP Output Character Encoding'=>$http_coding,
		'Internal PHP Character Encoding'=>$int_coding,
		'MySQL' => $mysqlvers,
		);
	$version_errors = array();
	$phpminvers = '5.0';
	$mysqlminvers = '4.0';
	if (!wordbooker_version_ok($phpvers, $phpminvers)) {
		$version_errors['PHP'] = $phpminvers;
	}
	if ($mysqlvers != 'Unknown' && !wordbooker_version_ok($mysqlvers, $mysqlminvers)) {
		$version_errors['MySQL'] = $mysqlminvers;
	}

	foreach ($info as $key => $value) {
		$suffix = '';
		if (($minvers = $version_errors[$key])) {
			$suffix = " <span class=\"wordbooker_errorcolor\">" . " (need $key version $minvers or greater)" . " </span>";
		}
		echo "<li>$key: <b>$value</b>$suffix</li>";
	}
	if (!function_exists('simplexml_load_string')) {
		_e("<li>XML: your PHP is missing <code>simplexml_load_string()</code></li>", 'wordbooker');
	}

	$rows = $wpdb->get_results("show variables like 'character_set%'");
	foreach ($rows as $chardata){
		echo "<li> Database ". $chardata->Variable_name ." : <b> ".$chardata->Value ."</b></li>";
	}
	$rows = $wpdb->get_results("show variables like 'collation%'");
	foreach ($rows as $chardata){
		echo "<li> Database ". $chardata->Variable_name ." : <b> ".$chardata->Value ."</b></li>";
	}
	echo "<li> Server : <b>".$_SERVER['SERVER_SOFTWARE']."</b></li>";
	_e("<li> Active Plugins : <b></li>", 'wordbooker');	
	 foreach($active_plugins as $name) {
		if ( $plug_info[$name]['Title']!='Wordbooker') {
		echo "&nbsp;&nbsp;&nbsp;".$plug_info[$name]['Title']." ( ".$plug_info[$name]['Version']." ) <br />";}
	}
	echo "</b><br /><li> Wordbooker Table Status :</li><b>";
	$table_array= array (WORDBOOKER_ERRORLOGS,WORDBOOKER_POSTLOGS,WORDBOOKER_USERDATA,WORDBOOKER_USERSTATUS,WORDBOOKER_POSTCOMMENTS,WORDBOOKER_PROCESS_QUEUE,WORDBOOKER_FB_FRIENDS,WORDBOOKER_FB_FRIEND_LISTS);
	foreach ($table_array as $table) {
		$sql="select count(*) from ".$table;
		$result=@mysql_query($sql);
		if (!$result)
	{
	$tstat_string= sprintf("ERROR : table </b>'%s'<b> is missing ! - Please Deactivate and Re-activate the plugin from the Plugin Options Page", $table);
	}
	else {	
	$row=mysql_fetch_row($result);
	$tstat_string= sprintf("&nbsp;&nbsp;&nbsp;Table </b>'%s'<b> is present and contains %s rows", $table,$row[0]);
	 }
	echo "&nbsp;&nbsp;&nbsp;".$tstat_string."<br />";
	}
	echo "</b>";
	if (ADVANCED_DEBUG) { phpinfo(INFO_MODULES);}
?>
	</ul>

<?php
	if ($version_errors) {
?>

	<div class="wordbooker_errorcolor">
	<?php _e('Your system does not meet the', 'wordbooker'); ?> <a href="http://wordpress.org/about/requirements/"><?php _e('WordPress minimum requirements', 'wordbooker'); ?></a>. <?php _e('Things are unlikely to work.', 'wordbooker'); ?>
	</div>

<?php
	} else if ($mysqlvers == 'Unknown') {
?>

	<div>
	<?php _e('Please ensure that your system meets the', 'wordbooker'); ?> <a href="http://wordpress.org/about/requirements/"><?php _e('WordPress minimum requirements', 'wordbooker'); ?></a>.
	</div>

<?php
	}
?>
	</div>

<?php
}


/******************************************************************************
 * Facebook API wrappers.
 */


function wordbooker_fbclient_publishaction($wbuser,$post_id) 
{	
	global $wordbooker_post_options,$wpdb;
	$wordbooker_settings =wordbooker_options(); 
	$post = get_post($post_id);
	$post_link_share = get_permalink($post_id);
	$post_link=wordbooker_short_url($post_id);
	$post_title=$post->post_title;
	$post_content = $post->post_content;
	# Grab the content of the post once its been filtered for display - this converts app tags into HTML so we can grab gallery images etc.
	$processed_content ="!!!  ".apply_filters('the_content', $post_content)."    !!!";
	$yturls  = array();
	$matches_tn=array();
	# Get the Yapb image for the post
	if (class_exists('YapbImage')) {
	   $siteUrl = get_option('siteurl');
	   if (substr($siteUrl, -1) != '/') $siteUrl .= '/';
	    $uri = substr($url, strpos($siteUrl, '/', strpos($url, '//')+2));
	    $WordbookerYapbImageclass = new YapbImage(null,$post->ID,$uri);
	    $WordbookerYapbImage=$WordbookerYapbImageclass->getInstanceFromDb($post_id);
	    if (strlen($WordbookerYapbImage->uri)>6) {$yturls[]=get_bloginfo('url').$WordbookerYapbImage->uri;}
	}

	if ( function_exists( 'get_the_post_thumbnail' ) ) { 
		wordbooker_debugger("Getting the thumnail image"," ",$post->ID,80) ;
		preg_match_all('/<img \s+ ([^>]*\s+)? src \s* = \s* [\'"](.*?)[\'"]/ix',get_the_post_thumbnail($post_id), $matches_tn); 
	}
	
	$meta_tag_scan=explode(',',$wordbooker_settings['wordbooker_meta_tag_scan']);
	foreach($meta_tag_scan as $meta_tag) {
		wordbooker_debugger("Getting image from custom meta : ",$meta_tag,$post->ID,80) ;
		$matches_ct[]=get_post_meta($post->ID, $meta_tag, TRUE);
	}
	$matches=$matches_ct;
	if ( function_exists( 'get_the_post_thumbnail' ) ) { 
		$matches=array_merge($matches_ct,$matches_tn[2]);
	}
   
	# If the user only wants the thumbnail then we can simply not do the skim over the processed images
	if (! isset($wordbooker_post_options["wordbooker_thumb_only"]) ) {
		wordbooker_debugger("Getting the rest of the images "," ",$post->ID) ;
		preg_match_all('/<img \s+ ([^>]*\s+)? src \s* = \s* ["](.*?)["]/ix',$processed_content, $matched);
		$x=strip_shortcodes($post_content);
		preg_match_all( '#http://(www.youtube|youtube|[A-Za-z]{2}.youtube)\.com/(watch\?v=|w/\?v=|\?v=|embed/)([\w-]+)(.*?)#i', $x, $matches3 );
		if (is_array($matches3[3])) {
			foreach ($matches3[3] as $key ) {
				$yturls[]='http://img.youtube.com/vi/'.$key.'/0.jpg';
			}
		}
		
	}
	if ( function_exists( 'get_the_post_thumbnail' ) ) {
		# If the thumb only is set then pulled images is just matches
		if (!isset($wordbooker_settings["wordbooker_meta_tag_thumb"])) {
			if (! isset($wordbooker_post_options["wordbooker_thumb_only"]) ) {
				wordbooker_debugger("Setting image array to be both thumb and the post images "," ",$post->ID,80) ;
			 	$pulled_images=@array_merge($matches[2],$matched[2],$yturls,$matches);
			}
			else {
				wordbooker_debugger("Setting image array to be just thumb "," ",$post->ID,80) ;
				$pulled_images[]=$matches[2];
			} 
		}
	}

	if (isset($wordbooker_settings["wordbooker_meta_tag_thumb"]) && isset($wordbooker_post_options["wordbooker_thumb_only"]) ) {
	wordbooker_debugger("Setting image array to be just thumb from meta. "," ",$post->ID,80) ;
	$pulled_images[]=$matches_ct[2];}

	else {
		wordbooker_debugger("Setting image array to be post and thumb images. "," ",$post->ID,80) ;
		if (is_array($matched[2])) {$pulled_images[]=array_merge($matches,$matched[2]);}
		if (is_array($matched[2]) && is_array($yturls)) {$pulled_images=array_merge($matches,$matched[2],$yturls);}
	}

	$images = array();
	if (is_array($pulled_images)) {
		foreach ($pulled_images as $ii => $imgsrc) {
			if ($imgsrc) {
				if (stristr(substr($imgsrc, 0, 8), '://') ===false) {
					/* Fully-qualify src URL if necessary. */
					$scheme = $_SERVER['HTTPS'] ? 'https' : 'http';
					$new_imgsrc = "$scheme://". $_SERVER['SERVER_NAME'];
					if ($imgsrc[0] == '/') {
						$new_imgsrc .= $imgsrc;
					}
					$imgsrc = $new_imgsrc;
				}
				$images[] =  $imgsrc;
			}
		}
	}
	/* Pull out <wpg2> image tags. */
	$wpg2_g2path = get_option('wpg2_g2paths');
	if ($wpg2_g2path) {
		$g2embeduri = $wpg2_g2path['g2_embeduri'];
		if ($g2embeduri) {
			preg_match_all('/<wpg2>(.*?)</ix', $processed_content,
				$wpg_matches);
			foreach ($wpg_matches[1] as $wpgtag) {
				if ($wpgtag) {
					$images[] = $g2embeduri.'?g2_view='.'core.DownloadItem'."&g2_itemId=$wpgtag";
				}
			}
		}
	}
	$wordbooker_settings =wordbooker_options(); 
	if (count($images)>0){
		# Remove duplicates
		$images=array_unique($images);
		# Strip images from various plugins
		$images=wordbooker_strip_images($images);
		# And limit it to 5 pictures to keep Facebook happy.
		$images = array_slice($images, 0, 5);

	} else { 
		if (isset($wordbooker_settings['wordbooker_use_this_image']))  {
			$images[]=$wordbooker_settings['wb_wordbooker_default_image'];
			wordbooker_debugger("No Post images found so using open graph default to keep Facebook happy ",'',$post->ID,90) ;
			} 
		else {
			$x=get_bloginfo('wpurl').'/wp-content/plugins/wordbooker/includes/wordbooker_blank.jpg';
			$images[]=$x;
			wordbooker_debugger("No Post images found so loading blank to keep Facebook happy ",'',$post->ID,90) ;	
			}
		}
	# Then shove the image on again just for good measure.
	$x=get_bloginfo('wpurl').'/wp-content/plugins/wordbooker/includes/wordbooker_blank.jpg';
	$images[]=$x;
	$images=array_unique($images);
	foreach ($images as $single) {
		$images_array[]=array(
				'type' => 'image', 
				'src' => $single,
				'href' => $post_link_share,
				);
	}
	$images=$images_array;
		foreach ($images as $key){
		wordbooker_debugger("Post Images : ".$key['src'],'',$post->ID) ;
	}
	// Set post_meta to be first image
	update_post_meta($post->ID,'_wordbooker_thumb',$images[0]['src']);
	wordbooker_debugger("Getting the Excerpt"," ",$post->ID,80) ;
	unset ($processed_content);
	if (isset($wordbooker_post_options["wordbooker_use_excerpt"])  && (strlen($post->post_excerpt)>3)) { 
		$post_content=$post->post_excerpt; 
		$post_content=wordbooker_translate($post_content);
	}
	else {	$post_content=wordbooker_post_excerpt($post_content,$wordbooker_post_options['wordbooker_extract_length']);}
	update_post_meta($post->ID,'_wordbooker_extract',$post_content);
	# this is getting and setting the post attributes
	$post_attribute=parse_wordbooker_attributes(stripslashes($wordbooker_post_options["wordbooker_attribute"]),$post_id,strtotime($post->post_date));
	$post_data = array(
		'media' => $images,
		'post_link' => $post_link,
		'post_link_share' => $post_link_share,
		'post_title' => $post_title,
		'post_excerpt' => $post_content,
		'post_attribute' => $post_attribute,
		'post_id'=>$post->ID,
		'post_date'=>$post->post_date
		);
/*
	# This is the tagging code - 
	if (strlen($wordbooker_post_options['wordbooker_tag_list']) > 6 ) {
		$wordbooker_tag_list=str_replace('[','@[',$wordbooker_post_options['wordbooker_tag_list']);
		$message=$message."   ". __("and tagged : ",'wordbooker').$wordbooker_tag_list;
	}
*/
		
	if (function_exists('qtrans_use')) {
		global $q_config;
		$post_data['post_title']=qtrans_use($q_config['default_language'],$post_data['post_title']);
	}
	$post_id=$post->ID;

	$wordbooker_fb_post = array(
	  'name' => $post_data['post_title'],
	  'link' => $post_data['post_link'],
	  'message'=> $post_data['post_attribute'],
	  'description' => $post_data['post_excerpt'],
	  'picture'=>$images[0]['src'],
	   'caption' => get_bloginfo('description')
	#  'media' => json_encode($images)
	);
	if (isset($wordbooker_settings['wordbooker_use_url_not_slug'])) { unset($wordbooker_fb_post['caption']);}
	wordbooker_debugger("Post Titled : ",$post_data['post_title'],$post_id,90) ;
	wordbooker_debugger("Post URL : ",$post_data['post_link'],$post_id,90) ;
	
	if ($wordbooker_post_options['wordbooker_actionlink']==100) {
		// No action link
		wordbooker_debugger("No action link being used","",$post_id,80) ;
	}
	if ($wordbooker_post_options['wordbooker_actionlink']==200) {
		// Share This
		wordbooker_debugger("Share Link being used"," ",$post_id,80) ;
		$action_links = array('name' => __('Share', 'wordbooker'),'link' => 'http://www.facebook.com/share.php?u='.urlencode($post_data['post_link_share']));
		$wordbooker_fb_post['actions']=json_encode($action_links);
	}
	if ($wordbooker_post_options['wordbooker_actionlink']==300) {
		// Read Full
		wordbooker_debugger("Read Full link being used"," ",$post_id,80) ;
		$action_links = array('name' => __('Read entire article', 'wordbooker'),'link' => $post_data['post_link_share']);
		$wordbooker_fb_post['actions'] =json_encode($action_links);
	}

	$posting_array[] = array('target_id'=>__("Primary", 'wordbooker'),
				'target'=>$wordbooker_post_options['wordbooker_primary_target'], 
				 'target_type'=>$wordbooker_post_options['wordbooker_primary_type'],
				 'target_active'=>$wordbooker_post_options['wordbooker_primary_active']);
	$posting_array[] = array('target_id'=>__("Secondary", 'wordbooker'),
				'target'=>$wordbooker_post_options['wordbooker_secondary_target'], 
				 'target_type'=>$wordbooker_post_options['wordbooker_secondary_type'],
				 'target_active'=>$wordbooker_post_options['wordbooker_secondary_active']);;
	$target_types = array('PW' => "",'FW' => __('Fan Wall', 'wordbooker'), 'GW'=>__('Group wall', 'wordbooker'));

	$posting_type=array("1"=>"Wall Post","2"=>"Note","3"=>"Status Update");
	foreach($posting_array as $posting_target) {
		$access_token='dummy access token';
		$wbuser->pages[]=array( 'id'=>'PW:'.$wbuser->facebook_id, 'name'=>"Personal Wall",'access_token'=>$wbuser->access_token);
		if(is_array($wbuser->pages)){
			foreach ($wbuser->pages as $pager) {
				if ($pager['id']==$posting_target['target']) {
					$target_name=$pager['name'];
					$access_token=$pager['access_token'];
				}
			}
		}
 		if (isset($posting_target['target_active'])) {
			$target_type=substr($posting_target['target'],0,2);
			wordbooker_debugger("Posting to ".$target_types[$target_type]." ".$target_name." (".$posting_target['target_id'].") as a ".$posting_type[$posting_target['target_type']],"",$post_id,90) ; 
			if ($access_token=='dummy access token') {$access_token=$wbuser->access_token;}
			$target=substr($posting_target['target'],3);
			$is_dummy=$wordbooker_settings['wordbooker_fake_publish'];
			switch($posting_target['target_type']) {
				# Wall Post
				case 1 : 
				wordbooker_wall_post($post_id,$access_token,$post_title,$wordbooker_fb_post ,$target,$is_dummy,$target_name);
				break;
				# Note
				case 2 :
				wordbooker_notes_post($post_id,$access_token,$post_title,$target,$is_dummy,$target_name);
				break;
				# Status Update
				case 3 : 
				wordbooker_status_update($post_id,$access_token,$post_data['post_date'],$target,$is_dummy,$target_name);
				break ;
				# Link Post
				case 4 : 
				wordbooker_link_post($post_id,$access_token,$post_title,$wordbooker_fb_post ,$target,$is_dummy,$target_name);
				break ;
			}

		} else {wordbooker_debugger("Posting to ".$posting_target['target_id']." target (".$target_name.") not active","",$post_id,90) ; }

	}
}

function wordbooker_strip_images($images)
{
	global $post;
	$newimages = array();
	$image_types= array ('jpg','jpeg','gif','png','tif','bmp');
	$strip_array= array ('addthis.com','gravatar.com','zemanta.com','wp-includes','plugins','favicon.ico','facebook.com','themes','mu-plugins','fbcdn.net');
	foreach($images as $single){
		$file_extension = strtolower(substr($single , strrpos($single , '.') +1)); 
		if (in_array($file_extension,$image_types)){
		foreach ($strip_array as $strip_domain) {
			wordbooker_debugger("Looking for ".$strip_domain." in ".$single," ",$post->ID,80) ;
 		  	if (stripos($single,$strip_domain)) {wordbooker_debugger("Found a match so dump the image",$single,$post->ID,80) ;} else { if (!in_array($single,$newimages)){$newimages[]=$single;}}
		}} else {wordbooker_debugger("Image URL ".$single." not valid "," ",$post->ID,90) ;}
	}
	return $newimages;
}

function wordbooker_get_language() {
	global $q_config;
	$wplang="en_US";
	if (strlen(WPLANG) > 2) {$wplang=WPLANG;}
	if (isset ($q_config["language"])) {
		$x=get_option('qtranslate_locales');
		$wplang=$x[$q_config["language"]];
	}
	if (strlen($wplang)< 5) {$wplang='en_US';}
	if ($wplang=="WPLANG" ) {$wplang="en_US";}
	return $wplang;
}


function wordbooker_short_url($post_id) {
	# This provides short_url responses by checking for various functions and using 
	$wordbooker_settings =wordbooker_options(); 
	
	if (isset($wordbooker_settings["wordbooker_disable_shorties"])) {
		$url = get_permalink($post_id);
		return $url;
	}
	$url = get_permalink($post_id);
	$url2 = $url;
	if (function_exists(fts_show_shorturl)) {	
		$post = get_post($post_id);
		$url=fts_show_shorturl($post,$output = false);
	}
	if (function_exists(wp_ozh_yourls_geturl)) {
		$url=wp_ozh_yourls_geturl($post_id);
	}
	if ("!!!".$url."XXXX"=="!!!XXXX") {$url = $url2;}
	return $url;
}


function parse_wordbooker_attributes($attribute_text,$post_id,$timestamp) {
	# Changes various "tags" into their WordPress equivalents.
	$post = get_post($post_id);
	$user_id=$post->post_author; 
	$title=$post->post_title;
	$perma=get_permalink($post->ID);
	$perma_short=wordbooker_short_url($post_id);
	$user_info = get_userdata($user_id);
	$blog_url= get_bloginfo('url');
	$wp_url= get_bloginfo('wpurl');
	$blog_name = get_bloginfo('name');
	$author_nice=$user_info->display_name;
	$author_nick=$user_info->nickname;
	$author_first=$user_info->first_name;
	$author_last=$user_info->last_name;

	# Format date and time to the blogs preferences.
	$date_info=date_i18n(get_option('date_format'),$timestamp);
	$time_info=date_i18n(get_option('time_format'),$timestamp);

	# Now do the replacements
	$attribute_text=str_ireplace( '%author%',$author_nice,$attribute_text );
	$attribute_text=str_ireplace( '%first%',$author_first,$attribute_text );
	$attribute_text=str_ireplace( '%wpurl%',$wp_url,$attribute_text );
	$attribute_text=str_ireplace( '%burl%',$blog_url,$attribute_text );
	$attribute_text=str_ireplace( '%last%',$author_last,$attribute_text );
	$attribute_text=str_ireplace( '%nick%',$author_nick,$attribute_text );
	$attribute_text=str_ireplace( '%title%',$title,$attribute_text );
	$attribute_text=str_ireplace( '%link%',$perma,$attribute_text );
	$attribute_text=str_ireplace( '%slink%',$perma_short,$attribute_text );
	$attribute_text=str_ireplace( '%date%', $date_info ,$attribute_text);
	$attribute_text=str_ireplace( '%time%', $time_info,$attribute_text );

	return $attribute_text;
}


function wordbooker_footer($blah)
{
	if (is_404()) {
		echo "\n<!-- Wordbooker code revision : ".WORDBOOKER_CODE_RELEASE." -->\n";
		return;
	}
	$wplang=wordbooker_get_language();

$efb_script = <<< EOGS
 <div id="fb-root"></div>
     <script type="text/javascript">
      window.fbAsyncInit = function() {
	FB.init({
	 appId  : '254577506873',
	  status : true, // check login status
	  cookie : true, // enable cookies to allow the server to access the session
	  xfbml  : true,  // parse XFBML
	  oauth:true
	});
      };

      (function() {
	var e = document.createElement('script');
EOGS;
$efb_script.= "e.src = document.location.protocol + '//connect.facebook.net/".$wplang."/all.js';";
$efb_script.= <<< EOGS
	e.async = true;
	document.getElementById('fb-root').appendChild(e);
      }());
    </script>
EOGS;
	$wordbooker_settings = wordbooker_options(); 
	if  (isset($wordbooker_settings['wordbooker_like_button_show']) || isset($wordbooker_settings['wordbooker_like_share_too'] ) || isset($wordbooker_settings['wordbooker_use_fb_comments'])) 
		{
		echo $efb_script;
		 if ( isset($wordbooker_settings['wordbooker_iframe'])) {
			echo '<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>';
		}
	}
#	echo '\n<script type="text/javascript " defer="defer" > setTimeout("wordbooker_read()",3000); </script> \n';
	echo "\n<!-- Wordbooker code revision : ".WORDBOOKER_CODE_RELEASE." -->\n";
return $blah;
}

function wordbooker_og_tags(){
	global $post;
	$bname=get_bloginfo('name');
	$bdesc=get_bloginfo('description');
	$wplang=wordbooker_get_language();
	$wordbooker_settings = wordbooker_options(); 	
	# Always put out the tags because even if they are not using like/share it gives Facebook stuff to work with.
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true); 
	$wpuserid=$post->post_author;
	if (is_array($wordbooker_post_options)){
		if  ($wordbooker_post_options["wordbooker_default_author"] > 0 ) {$wpuserid=$wordbooker_post_options["wordbooker_default_author"];}
	}

	$blog_name=get_bloginfo('name');
	echo '<!-- Wordbooker generated tags -->';
	echo '<meta property="og:locale" content="'.$wplang.'"/> ';
	echo '<meta property="og:site_name" content="'.$bname.' - '.$bdesc.'"/> ';
	if (strlen($wordbooker_settings["fb_comment_app_id"])<6) {
	if ($wordbooker_settings['wordbooker_fb_comments_admin']) {
		$xxx=wordbooker_get_cache(-99,facebook_id,1);
			if (!is_null($xxx)) {
			echo '<meta property="fb:admins" content="'.$xxx.'"/> ';
		}
	} else {
		 $xxx=wordbooker_get_cache( $wpuserid,facebook_id,1);
		if (!is_null($xxx->facebook_id)) {
			echo '<meta property="fb:admins" content="'.$xxx->facebook_id.'"/> ';
		}
	 }
	}
	if (strlen($wordbooker_settings["fb_comment_app_id"])>6) {
		echo '<meta property = "fb:app_id" content = "'.$wordbooker_settings["fb_comment_app_id"].'" /> ';
	}
	if (defined('WORDBOOKER_PREMIUM')) {
		echo '<meta property = "fb:app_id" content = "'.WORDBOOKER_FB_ID.'" /> ';
	}	
	if ( (is_single() || is_page()) && !is_front_page() && !is_category() && !is_home() ) {
		$post_link = get_permalink($post->ID);
		$post_title=$post->post_title;
		echo '<meta property="og:title" content="'.htmlspecialchars(strip_tags($post_title),ENT_QUOTES).'"/> ';
		echo '<meta property="og:url" content="'.$post_link.'"/> ';
		echo '<meta property="og:type" content="article"/> ';
		
		$ogimage=get_post_meta($post->ID, '_wordbooker_thumb', TRUE);
		if (strlen($ogimage)<4 && strlen($wordbooker_settings['wb_wordbooker_default_image'])>4) {
			$ogimage=$wordbooker_settings['wb_wordbooker_default_image'];
		}
		if (strlen($ogimage)<4) {$ogimage=get_bloginfo('wpurl').'/wp-content/plugins/wordbooker/includes/wordbooker_blank.jpg';}
		if (strlen($ogimage)>4) {
			echo '<meta property="og:image" content="'.$ogimage.'"/> ';
			
		}
	} 
	else
	{ # Not a single post so we only need the og:type tag
		echo '<meta property="og:type" content="blog"/> ';
		echo '<meta property="og:type" content="'.get_bloginfo('description').'"/> ';
	}
	if ($meta_length = wordbooker_get_option('wordbooker_description_meta_length')) {
		if (is_single() || is_page()) {
			$excerpt=get_post_meta($post->ID, '_wordbooker_extract', TRUE);
			if(strlen($excerpt) < 5 ) {
				$excerpt=wordbooker_post_excerpt($post->post_content,$wordbooker_settings['wordbooker_extract_length']);
				update_post_meta($post->ID, '_wordbooker_extract', $excerpt);
			}
			# If we've got an excerpt use that instead
			if ((strlen($post->post_excerpt)>3) && (strlen($excerpt) <=5)) { 
				$excerpt=$post->post_excerpt; 
				$description = str_replace('"','&quot;',$post->post_content);
				$excerpt = wordbooker_post_excerpt($description,$meta_length);
				$excerpt = preg_replace('/(\r|\n)+/',' ',$excerpt);
				$excerpt = preg_replace('/\s\s+/',' ',$excerpt);
	
				update_post_meta($post->ID, '_wordbooker_extract', $excerpt);
			}
			# Now if we've got something put the meta tag out.
			if (isset($excerpt)){ 
				$meta_string = sprintf("<meta name=\"description\" content=\"%s\"/> ", htmlspecialchars($excerpt,ENT_QUOTES));
				$meta_string .= sprintf("<meta property=\"og:description\" content=\"%s\"/> ", htmlspecialchars($excerpt,ENT_QUOTES));
				echo $meta_string;
			}
		}
	else
		{		
			$meta_string = sprintf("<meta name=\"description\" content=\"%s\"/> ", get_bloginfo('description'));
			$meta_string .= sprintf("<meta property=\"og:description\" content=\"%s\"/> ", htmlspecialchars($excerpt,ENT_QUOTES));
			echo $meta_string;
		}
	}
	echo '<!-- End Wordbooker og tags -->';
}

function wordbooker_header($blah){
	if (is_404()) {return;}
	global $post;
	# Stops the code firing on non published posts
	if ('publish' != get_post_status($post->ID)) {return;}
	$wordbooker_settings = wordbooker_options(); 
	# Now we just call the wordbooker_og_tags function.
	if (!isset ( $wordbooker_settings['wordbooker_fb_disable_og'])) { wordbooker_og_tags(); }
	return $blah;
}

function display_wordbooker_fb_comment() {
	global $post;
	if(!is_single()){return;}
	$wordbooker_settings = wordbooker_options(); 
	if (!isset($wordbooker_settings['wordbooker_use_fb_comments'])) { return;}
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ( isset($wordbooker_post_options['wordbooker_use_facebook_comments'])) {
		$post_link = get_permalink($post->ID);
		$checked_flag=array('on'=>'true','off'=>'false');
		$comment_code= '<fb:comments href="'.$post_link.'" num_posts="'.$wordbooker_settings['fb_comment_box_count'].'" width="'.$wordbooker_settings['fb_comment_box_size'].'" notify="'.$checked_flag[$wordbooker_settings['fb_comment_box_notify']].'" colorscheme="'.$wordbooker_settings['wb_comment_colorscheme'].'" ></fb:comments>';
		echo $comment_code;
	}
}

function wordbooker_fb_comment_inline() {
	global $post;
	if(!is_single()){return;}
	$wordbooker_settings = wordbooker_options(); 
	if (!isset($wordbooker_settings['wordbooker_use_fb_comments'])) { return;}
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true); 
	if ( isset($wordbooker_post_options['wordbooker_use_facebook_comments'])) {
		$post_link = get_permalink($post->ID);
		$checked_flag=array('on'=>'true','off'=>'false');
		$comment_code= '<fb:comments href="'.$post_link.'" num_posts="'.$wordbooker_settings['fb_comment_box_count'].'" width="'.$wordbooker_settings['fb_comment_box_size'].'" notify="'.$checked_flag[$wordbooker_settings['fb_comment_box_notify']].'" colorscheme="'.$wordbooker_settings['wb_comment_colorscheme'].'" ></fb:comments>';
		return $comment_code;
	}
}

function display_wordbooker_fb_share() {
	global $post;
	$wordbooker_settings = wordbooker_options(); 
	$do_share=0;
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ($wordbooker_post_options['wordbooker_share_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_share_button_page']==2 && is_page()) {return ;}
	if (!isset($wordbooker_settings['wordbooker_like_share_too'])) {return ;}
	if (isset($wordbooker_settings['wordbooker_share_button_post']) && is_single()  ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_page']) && is_page() )  {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_frontpage'])  && is_front_page() ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_category']) &&  is_category()  ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_no_share_stick']) &&  is_sticky()  ) {$do_share=0; }
	if ( $do_share==1  &&
	((isset($wordbooker_settings['wordbooker_share_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_share_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_share_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_share_button_category']) &&  is_category()  ))
	  )
	{
	$post_link = get_permalink($post->ID);
	$btype="button";
	if (is_single() || is_page()) {
	$btype="button_count";
	}
	if (isset($wordbooker_settings['wordbooker_iframe'])) {
		 $share_code='<!-- Wordbooker created FB tags --> <a name="fb_share" type="'.$btype.'" share_url="'.$post_link.'"></a>';
	}
	else {
		$share_code='<!-- Wordbooker created FB tags --> <fb:share-button class="meta" type="'.$btype.'" href="'.$post_link.'" > </fb:share-button>';
	}
	if (isset($wordbooker_settings['wordbooker_time_button'])) {
		if (isset($wordbooker_settings['wordbooker_iframe'])) {
			 $share_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/add_to_timeline.php?show-faces=true&amp;mode=button&amp;appId=277399175632726" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>';
		}
		else {
			$share_code='<!-- Wordbooker created FB tags -->  <div class="fb-add-to-timeline" data-show-faces="false" data-mode="button"></div>';
		}
	}
		
	echo $share_code;
	}
}

function wordbooker_fb_share_inline() {
	global $post;
	$wordbooker_settings = wordbooker_options(); 
	$do_share=0;
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ($wordbooker_post_options['wordbooker_share_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_share_button_page']==2 && is_page()) {return ;}
	if (!isset($wordbooker_settings['wordbooker_like_share_too'])) {return ;}
	if (isset($wordbooker_settings['wordbooker_share_button_post']) && is_single() && !is_front_page() ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_page']) && is_page()  && !is_front_page() )  {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_frontpage'])  && is_front_page() ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_share_button_category']) &&  is_category()  ) {$do_share=1;}
	if (isset($wordbooker_settings['wordbooker_no_share_stick']) &&  is_sticky()  ) {$do_share=0; }
	if ( $do_share==1  &&
	((isset($wordbooker_settings['wordbooker_share_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_share_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_share_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_share_button_category']) &&  is_category()  ))
	  )
	{
	$post_link = get_permalink($post->ID);
	$btype="button";
	if (is_single() || is_page()) {
	$btype="button_count";
	}
	if (isset($wordbooker_settings['wordbooker_iframe'])) {
		 $share_code='<!-- Wordbooker created FB tags --> <a name="fb_share" type="'.$btype.'" share_url="'.$post_link.'"></a>';
	}
	else {
		$share_code='<!-- Wordbooker created FB tags --> <fb:share-button class="meta" type="'.$btype.'" href="'.$post_link.'" > </fb:share-button>';
	}
	if (isset($wordbooker_settings['wordbooker_time_button'])) {
		if (isset($wordbooker_settings['wordbooker_iframe'])) {
			 $share_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/add_to_timeline.php?show-faces=true&amp;mode=button&amp;appId=277399175632726" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>';
		}
		else {
			$share_code='<!-- Wordbooker created FB tags --> <div class="fb-add-to-timeline" data-show-faces="false" data-mode="button"></div>';

		}
	}
		
	 return $share_code;
	}
}
function display_wordbooker_fb_send() {
	global $post,$q_config;
	$wordbooker_settings = wordbooker_options(); 
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	$post_link = get_permalink($post->ID);
	if ($wordbooker_post_options['wordbooker_like_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_like_button_page']==2 && is_page()) {return ;}
	if ($wordbooker_settings['wordbooker_fblike_send_combi']=='true') {return;}

	$do_like=0;
	if (isset($wordbooker_settings['wordbooker_like_button_post']) && is_single() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() && !is_front_page())  {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_no_like_stick']) &&  is_sticky()  ) { $do_like=0;}
	if ( $do_like==1  &&
		((isset($wordbooker_settings['wordbooker_like_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category()  ))
	  )
	{
		if (isset($wordbooker_settings['wordbooker_iframe'])) { 
			$px=35;
			$wplang=wordbooker_get_language();
			if ($wordbooker_settings['wordbooker_fblike_faces']=='true') {$px=80;}
			$like_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/send.php?locale='.$wplang.'&href='.$post_link.'&amp;layout='.$wordbooker_settings['wordbooker_fblike_button'].'&amp;show_faces='.$wordbooker_settings['wordbooker_fblike_faces'].'&amp;width='.$wordbooker_settings["wordbooker_like_width"].'&amp;action='.$wordbooker_settings['wordbooker_fblike_action'].'&amp;colorscheme='.$wordbooker_settings['wordbooker_fblike_colorscheme'].'&amp;font='.$wordbooker_settings['wordbooker_fblike_font'].'&amp;height='.$px.'px" scrolling="no" frameborder="no" style="border:none; overflow:hidden; width:'.$wordbooker_settings["wordbooker_like_width"].'px; height:'.$px.'px;" allowTransparency="true"></iframe>';

		}
		else {
		$like_code='<!-- Wordbooker created FB tags --> <fb:send layout="'.$wordbooker_settings['wordbooker_fblike_button'] .'" show_faces="'.$wordbooker_settings['wordbooker_fblike_faces'].'" action="'.$wordbooker_settings['wordbooker_fblike_action'].'" font="'.$wordbooker_settings['wordbooker_fblike_font'].'" colorscheme="'.$wordbooker_settings['wordbooker_fblike_colorscheme'].'"  href="'.$post_link.'" width="'.$wordbooker_settings["wordbooker_like_width"].' "></fb:send> ';}
		echo $like_code;
	}
}

function wordbooker_fb_send_inline() {
	global $post;
	$wordbooker_settings = wordbooker_options(); 
	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ($wordbooker_post_options['wordbooker_like_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_like_button_page']==2 && is_page()) {return ;}
	if ($wordbooker_settings['wordbooker_fblike_send_combi']=='true') {return;}
	$post_link = get_permalink($post->ID);
	$do_like=0;
	if (isset($wordbooker_settings['wordbooker_like_button_post']) && is_single() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() && !is_front_page())  {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_no_like_stick']) &&  is_sticky()  ) { $do_like=0;}
	if ( $do_like==1  &&
		((isset($wordbooker_settings['wordbooker_like_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category()  ))
	  )
	{
		if (isset($wordbooker_settings['wordbooker_iframe'])) { 
			$px=35;
			$wplang=wordbooker_get_language();
			if ($wordbooker_settings['wordbooker_fblike_faces']=='true') {$px=80;}
			$like_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/send.php?locale='.$wplang.'&href='.$post_link.'&amp;layout='.$wordbooker_settings['wordbooker_fblike_button'].'&amp;show_faces='.$wordbooker_settings['wordbooker_fblike_faces'].'&amp;width='.$wordbooker_settings["wordbooker_like_width"].'&amp;action='.$wordbooker_settings['wordbooker_fblike_action'].'&amp;colorscheme='.$wordbooker_settings['wordbooker_fblike_colorscheme'].'&amp;font='.$wordbooker_settings['wordbooker_fblike_font'].'&amp;height='.$px.'px" scrolling="no" frameborder="no" style="border:none; overflow:hidden; width:'.$wordbooker_settings["wordbooker_like_width"].'px; height:'.$px.'px;" allowTransparency="true"></iframe>';

		}
		else {
		$like_code='<!-- Wordbooker created FB tags --> <fb:send layout="'.$wordbooker_settings['wordbooker_fblike_button'] .'" show_faces="'.$wordbooker_settings['wordbooker_fblike_faces'].'" action="'.$wordbooker_settings['wordbooker_fblike_action'].'" font="'.$wordbooker_settings['wordbooker_fblike_font'].'" colorscheme="'.$wordbooker_settings['wordbooker_fblike_colorscheme'].'"  href="'.$post_link.'" width="'.$wordbooker_settings["wordbooker_like_width"].' "></fb:send> ';}
		return $like_code;
	}
}

function display_wordbooker_fb_like() {
	global $post;
	$wordbooker_settings = wordbooker_options();

	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ($wordbooker_post_options['wordbooker_like_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_like_button_page']==2 && is_page()) {return ;}
	if (!isset($wordbooker_settings['wordbooker_like_button_show'])) {return;}
	$do_like=0;
	$post_link = get_permalink($post->ID);
	if (isset($wordbooker_settings['wordbooker_like_button_post']) && is_single() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() && !is_front_page())  {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_no_like_stick']) &&  is_sticky()  ) { $do_like=0;}
	if ( $do_like==1  &&
		((isset($wordbooker_settings['wordbooker_like_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category()  ))
	  )
	{
		if (isset($wordbooker_settings['wordbooker_iframe'])) { 
			$px=35;
			$wplang=wordbooker_get_language();
			if ($wordbooker_settings['wordbooker_fblike_faces']=='true') {$px=95;}
			$like_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/like.php?locale='.$wplang.'&href='.$post_link.'&amp;layout='.$wordbooker_settings['wordbooker_fblike_button'].'&amp;show_faces='.$wordbooker_settings['wordbooker_fblike_faces'].'&amp;width='.$wordbooker_settings["wordbooker_like_width"].'&amp;action='.$wordbooker_settings['wordbooker_fblike_action'].'&amp;colorscheme='.$wordbooker_settings['wordbooker_fblike_colorscheme'].'&amp;font='.$wordbooker_settings['wordbooker_fblike_font'].'&amp;height='.$px.'px" scrolling="no" frameborder="no" style="border:none; overflow:hidden; width:'.$wordbooker_settings["wordbooker_like_width"].'px; height:'.$px.'px;" allowTransparency="true"></iframe>';

		}
		else {
		$like_code='<!-- Wordbooker created FB tags --> <fb:like layout="'.$wordbooker_settings['wordbooker_fblike_button'] .'" show_faces="'.$wordbooker_settings['wordbooker_fblike_faces'].'" action="'.$wordbooker_settings['wordbooker_fblike_action'].'" font="'.$wordbooker_settings['wordbooker_fblike_font'].'" colorscheme="'.$wordbooker_settings['wordbooker_fblike_colorscheme'].'"  href="'.$post_link.'" width="'.$wordbooker_settings["wordbooker_like_width"].'" ';
		if ($wordbooker_settings['wordbooker_fblike_send_combi']=='true' ) { $like_code.=' send="'.$wordbooker_settings['wordbooker_fblike_send'].'" ';}
		$like_code.='></fb:like> ';}
		echo $like_code;
	}
}

function wordbooker_fb_like_inline() {
	global $post;
	$wordbooker_settings = wordbooker_options();

	$wordbooker_post_options= get_post_meta($post->ID, '_wordbooker_options', true);  
	if ($wordbooker_post_options['wordbooker_like_button_post']==2 && !is_page()) {return ;}
	if ($wordbooker_post_options['wordbooker_like_button_page']==2 && is_page()) {return ;}
	if (!isset($wordbooker_settings['wordbooker_like_button_show'])) {return;}
	$do_like=0;
	$post_link = get_permalink($post->ID);
	if (isset($wordbooker_settings['wordbooker_like_button_post']) && is_single() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() && !is_front_page())  {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category() && !is_front_page() ) {$do_like=1;}
	if (isset($wordbooker_settings['wordbooker_no_like_stick']) &&  is_sticky()  ) { $do_like=0;}
	if ( $do_like==1  &&
		((isset($wordbooker_settings['wordbooker_like_button_post']) && is_single()  )
          || (isset($wordbooker_settings['wordbooker_like_button_page']) && is_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_frontpage'])  && is_front_page() ) 
	  || (isset($wordbooker_settings['wordbooker_like_button_category']) &&  is_category()  ))
	  )
	{
		if (isset($wordbooker_settings['wordbooker_iframe'])) { 
			$px=35;
			$wplang="en_US";
			if (strlen(WPLANG) > 2) {$wplang=WPLANG;}
			# then we check if WPLANG is actually set to anything sensible.
			if ($wplang=="WPLANG" ) {$wplang="en_US";}
			if ($wordbooker_settings['wordbooker_fblike_faces']=='true') {$px=95;}
			$like_code='<!-- Wordbooker created FB tags --> <iframe src="http://www.facebook.com/plugins/like.php?locale='.$wplang.'&href='.$post_link.'&amp;layout='.$wordbooker_settings['wordbooker_fblike_button'].'&amp;show_faces='.$wordbooker_settings['wordbooker_fblike_faces'].'&amp;width='.$wordbooker_settings["wordbooker_like_width"].'&amp;action='.$wordbooker_settings['wordbooker_fblike_action'].'&amp;colorscheme='.$wordbooker_settings['wordbooker_fblike_colorscheme'].'&amp;font='.$wordbooker_settings['wordbooker_fblike_font'].'&amp;height='.$px.'px" scrolling="no" frameborder="no" style="border:none; overflow:hidden; width:'.$wordbooker_settings["wordbooker_like_width"].'px; height:'.$px.'px;" allowTransparency="true"></iframe>';

		}
		else {
		$like_code='<!-- Wordbooker created FB tags --> <fb:like layout="'.$wordbooker_settings['wordbooker_fblike_button'] .'" show_faces="'.$wordbooker_settings['wordbooker_fblike_faces'].'" action="'.$wordbooker_settings['wordbooker_fblike_action'].'" font="'.$wordbooker_settings['wordbooker_fblike_font'].'" colorscheme="'.$wordbooker_settings['wordbooker_fblike_colorscheme'].'"  href="'.$post_link.'" width="'.$wordbooker_settings["wordbooker_like_width"].'" ';
		if ($wordbooker_settings['wordbooker_fblike_send_combi']=='true' ) { $like_code.=' send="'.$wordbooker_settings['wordbooker_fblike_send'].'" ';}
		$like_code.='> </fb:like> ';}
		return $like_code;
	}
}

function wordbooker_fb_read_inline() {
	$wordbooker_settings = wordbooker_options();
	if (is_single() && isset($wordbooker_settings['wordbooker_read_button']) ) {
		global $post;
		$action="https://www.facebook.com/connect/uiserver.php?app_id=277399175632726&method=permissions.request&redirect_uri=http://www.tty.org.uk/readmyblog.php?return=";
		$action.=urlencode(get_permalink($post->ID));
		$action.="&response_type=code&display=async&perms=publish_stream,publish_actions&auth_referral=1";
		$read_code="<a href='".$action."'> Post Action to TimeLine </a>";
		return $read_code;
	}
}

function wordbooker_fb_read() {
	$wordbooker_settings = wordbooker_options();
	if (is_single() && isset($wordbooker_settings['wordbooker_read_button']) ) {
		global $post;
		$action="https://www.facebook.com/connect/uiserver.php?app_id=277399175632726&method=permissions.request&redirect_uri=http://www.tty.org.uk/readmyblog.php?return=";
		$action.=urlencode(get_permalink($post->ID));
		$action.="&response_type=code&display=async&perms=publish_stream,publish_actions&auth_referral=1";
		$read_code="<a href='".$action."'> Post Action to TimeLine </a>";
		echo $read_code;
	}
}

function wordbooker_append_post($post_cont) {
	global $post;
	$do_share=0;
	if ($post->post_type=='forum') { return;}
	$wordbooker_settings = wordbooker_options(); 
	if (!isset($wordbooker_settings['wordbooker_like_button_show']) && !isset($wordbooker_settings['wordbooker_like_share_too']) && !isset($wordbooker_settings['wordbooker_use_fb_comments'])) {return $post_cont;}
	$post_cont2=$post_cont;
	$post_link = get_permalink($post->ID);
	$share_code=wordbooker_fb_share_inline();
	$like_code=wordbooker_fb_like_inline();
	$send_code=wordbooker_fb_send_inline();
	$comment_code=wordbooker_fb_comment_inline();
	$read_code=wordbooker_fb_read_inline();
	if ($wordbooker_settings['wordbooker_fblike_location']!=$wordbooker_settings['wordbooker_fbshare_location']){
		if ($wordbooker_settings['wordbooker_fbshare_location']=='top'){
			$post_cont2= "<div class='wp_fbs_top'>".$share_code."</div>".$post_cont2; 
		} 
		if ($wordbooker_settings['wordbooker_fbshare_location']=='bottom') {
			$post_cont2=$post_cont2."<div class='wp_fbs_bottom'>".$share_code.'</div>';
		}


		if ($wordbooker_settings['wordbooker_fblike_send_combi']=='true'){
			if ($wordbooker_settings['wordbooker_fblike_location']=='bottom'){
				$post_cont2= $post_cont2."<div class='wp_fbl_bottom'>".$like_code.'</div>'; 
			} 
			if ($wordbooker_settings['wordbooker_fblike_location']=='top') {
				$post_cont2= "<div class='wp_fbl_top'>".$like_code.'</div>'.$post_cont2;
			}
		}
	}	else {
				if ($wordbooker_settings['wordbooker_fblike_location']=='bottom'){
					$post_cont2=$post_cont2."<div class='wb_fb_bottom'>".$like_code.'<div style="float:right;">'.$share_code.'</div></div>'; 
				} 
				if ($wordbooker_settings['wordbooker_fblike_location']=='top'){
					$post_cont2= "<div class='wb_fb_top'>".$like_code.'<div style="float:right;">'.$share_code.'</div></div>'.$post_cont2; 
				}
	}
	if ($wordbooker_settings['wordbooker_fblike_send_combi']=='false' && $wordbooker_settings['wordbooker_fblike_send']=='true' ){
		if ($wordbooker_settings['wordbooker_fblike_location']==$wordbooker_settings['wordbooker_fbshare_location']){
			if ($wordbooker_settings['wordbooker_fblike_location']=='bottom'){
				$post_cont2=$post_cont2."<div class='wb_fb_bottom'>".$send_code.'<div style="float:right;">'.$share_code.'</div></div>'; 
			} 
			if ($wordbooker_settings['wordbooker_fblike_location']=='top'){
				$post_cont2= "<div class='wb_fb_top'>".$send_code.'<div style="float:right;">'.$share_code.'</div></div>'.$post_cont; 
			}
		} else {
		if ($wordbooker_settings['wordbooker_fblike_location']=='bottom'){
			$post_cont2= $post_cont2."<div class='wp_fbl_bottom'>".$send_code.'</div>'; 
		} 
		if ($wordbooker_settings['wordbooker_fblike_location']=='top') {
			$post_cont2= "<div class='wp_fbl_top'>".$send_code.'</div>'.$post_cont2;
		}
	}
	}

	if ($wordbooker_settings['wordbooker_fbread_location']=='top'){
		$post_cont2= "<div class='wp_fbr_top'>".$read_code."</div>".$post_cont2; 
	} 
	if ($wordbooker_settings['wordbooker_fbread_location']=='bottom') {
		$post_cont2=$post_cont2."<div class='wp_fbr_bottom'>".$read_code.'</div>';
	}
	if ($wordbooker_settings['wordbooker_comment_location']=='bottom') { $post_cont2=$post_cont2."<div class='wb_fb_comment'><br/>".$comment_code."</div>"; }
	return $post_cont2;
}

function wordbooker_get_cache($user_id,$field=null,$table=0) {
	global $wpdb,$blog_id;
	#$blog_id=1;
	if (!isset($user_id)) {return;}
	$tname=WORDBOOKER_USERSTATUS;
	$query_fields='facebook_id,name,url,pic,status,updated,facebook_id';
	$blog_lim=' and blog_id='.$blog_id;
	if ($table==1) {$tname=WORDBOOKER_USERDATA;$query_fields='facebook_id,name,url,pic,status,updated,auths_needed,use_facebook';$blog_lim='';}
	if (isset($field)) {$query_fields=$field;}
	if ($user_id==-99){ 
		$query="select ".$query_fields." from ".$tname."  where blog_id = ".$blog_id; 
		$result = $wpdb->get_results($query,ARRAY_N );
		foreach($result as $key){ $newkey[]=$key[0];}
		$result = implode(",",$newkey); 
	}
	else {
	$query="select ".$query_fields." from ".$tname."  where user_ID=".$user_id.$blog_lim;
	$result = $wpdb->get_row($query); }
	return $result;
}

function wordbooker_check_permissions($wbuser,$user) {
	global $user_ID;
	$perm_miss=wordbooker_get_cache($user_ID,'auths_needed',1);
	if ($perm_miss->auths_needed==0) { return;}
	$perms_to_check= array(WORDBOOKER_FB_PUBLISH_STREAM,WORDBOOKER_FB_STATUS_UPDATE,WORDBOOKER_FB_READ_STREAM,WORDBOOKER_FB_CREATE_NOTE,WORDBOOKER_FB_PHOTO_UPLOAD,WORDBOOKER_FB_VIDEO_UPLOAD,WORDBOOKER_FB_MANAGE_PAGES,WORDBOOKER_FB_READ_FRIENDS);
	$perm_messages= array( __('Publish content to your Wall/Fan pages', 'wordbooker'),__('Update your status', 'wordbooker'), __('Read your News Feed and Wall', 'wordbooker'),__('Create notes', 'wordbooker'),__('Upload photos', 'wordbooker'),__('Upload videos', 'wordbooker'),__('Manage_pages', 'wordbooker'),__('Read friend lists', 'wordbooker'));
	$preamble= __("but requires authorization to ", 'wordbooker');
	$postamble= __(" on Facebook. Click on the following link to grant permission", 'wordbooker');
		$loginUrl2='https://www.facebook.com/dialog/oauth?client_id='.WORDBOOKER_FB_ID.'&redirect_uri=https://wordbooker.tty.org.uk/index2.html?br='.urlencode(get_bloginfo('wpurl').'&fbid='.WORDBOOKER_FB_ID).'&scope='.implode(',',$perms_to_check).'&response_type=token';
	if(is_array($perms_to_check)) {
		foreach(array_keys($perms_to_check) as $key){
			# Bit map check to put out the right text for the missing permissions.
			if (pow(2,$key) & $perm_miss->auths_needed ) {
				$midamble.=$perm_messages[$key].", ";
				}
		}
			$midamble=rtrim($midamble,",");
			$midamble=trim(preg_replace("/(.*?)((,|\s)*)$/m", "$1", $midamble));
			$midamble=substr_replace($midamble, " and ", strrpos($midamble, ","), strlen(","));	
			       echo " ".$preamble.$midamble.$postamble.'</p><div style="text-align: center;"><a href="'.$loginUrl2.'" > <img src="http://static.ak.facebook.com/images/devsite/facebook_login.gif"  alt="Facebook Login Button" /></a><br /></div>';
			
	}
	echo "and then save your settings<br />";
	echo '<form action="'.WORDBOOKER_SETTINGS_URL.'" method="post"> <input type="hidden" name="action" value="" />';
	echo '<p style="text-align: center;"><input type="submit" name="perm_save" class="button-primary" value="'. __('Save Configuration', 'wordbooker').'" /></p></form>';
}

/******************************************************************************
 * WordPress hooks: update Facebook when a blog entry gets published.
 */

function wordbooker_post_excerpt($excerpt, $maxlength,$doyoutube=1) {
	if (function_exists('strip_shortcodes')) {
		$excerpt = strip_shortcodes($excerpt);
	}
	global $wordbooker_post_options;
	if (!isset($maxlength)) {$maxlength=$wordbooker_post_options['wordbooker_extract_length'];}
	if (!isset($maxlength)) {$maxlength=256;}
	$excerpt = trim($excerpt);
	# Now lets strip any tags which dont have balanced ends
	#  Need to put NGgallery tags in there - there are a lot of them and they are all different.
	$open_tags="[simage,[[CP,[gallery,[imagebrowser,[slideshow,[tags,[albumtags,[singlepic,[album";
	$close_tags="],]],],],],],],],]";
	$open_tag=explode(",",$open_tags);
	$close_tag=explode(",",$close_tags);
	foreach (array_keys($open_tag) as $key) {
		if (preg_match_all('/' . preg_quote($open_tag[$key]) . '(.*?)' . preg_quote($close_tag[$key]) .'/i',$excerpt,$matches)) {
			$excerpt=str_replace($matches[0],"" , $excerpt);
		 }
	}
	$excerpt = preg_replace('#(<wpg.*?>).*?(</wpg2>)#', '$1$2', $excerpt);
	$excerpt=wordbooker_translate($excerpt);
	$excerpt = strip_tags($excerpt);
	# Now lets strip off the youtube stuff.
	preg_match_all( '#http://(www.youtube|youtube|[A-Za-z]{2}.youtube)\.com/(watch\?v=|w/\?v=|\?v=)([\w-]+)(.*?)player_embedded#i', $excerpt, $matches );
	$excerpt=str_replace($matches[0],"" , $excerpt);
	preg_match_all( '#http://(www.youtube|youtube|[A-Za-z]{2}.youtube)\.com/(watch\?v=|w/\?v=|\?v=|embed/)([\w-]+)(.*?)#i', $excerpt, $matches );
	$excerpt=str_replace($matches[0],"" , $excerpt);
	$excerpt = apply_filters('wordbooker_post_excerpt', $excerpt);
	if (strlen($excerpt) > $maxlength) {
		# If we've got multibyte support then we need to make sure we get the right length - Thanks to Kensuke Akai for the fix
		if(function_exists('mb_strimwidth')){$excerpt=mb_strimwidth($excerpt, 0, $maxlength, " ...");}
		else { $excerpt=current(explode("SJA26666AJS", wordwrap($excerpt, $maxlength, "SJA26666AJS")))." ...";}
	}
	
	return $excerpt;
}

function wordbooker_translate($text) {
	if (function_exists('qtrans_use')) {
		global $q_config;
		$text=qtrans_use($q_config['language'],$text);
	}	
	return $text;
}

function wordbooker_publish_action($post_id) {
	global $user_ID, $user_identity, $user_login, $wpdb,$wordbooker_post_options,$blog_id,$doing_post;
	if(isset($doing_post)) {wordbooker_debugger("Looks like we've already got a post going on so we can give up","",$post_id,99) ; return;}
	$doing_post="running";
	$x = get_post_meta($post_id, '_wordbooker_options', true); 
	$post=get_post($post_id);
	# Get the settings from the post_meta.
	if (is_array($x)){
		foreach (array_keys($x) as $key ) {
			if (substr($key,0,8)=='wordbook') {
			#	wordbooker_debugger("Replacing : ".$wordbooker_post_options[$key],$x[$key],$post->ID) ;
				$wordbooker_post_options[$key]=str_replace( array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),array('&','"','\'','<','>',"\t"),$x[$key]);
			}
		}
	}
	
		if (is_array($wordbooker_post_options)){
		foreach (array_keys($wordbooker_post_options) as $key){
			wordbooker_debugger("Post option : ".$key,$wordbooker_post_options[$key],$post->ID,80) ;
		}
	}
	
	if ($wordbooker_post_options["wordbooker_publish_default"]=="200") { $wordbooker_post_options["wordbooker_publish_default"]='on';}

	# If the user_ID is set then lets use that, if not get the user_id from the post
	$whichuser=$post->post_author;
	if ($user_ID >=1) {$whichuser=$user_ID;} 
	# If the default user is set to 0 then we use the current user (or the author of the post if that isn't set - i.e. if this is a scheduled post)
	if  ($wordbooker_post_options["wordbooker_default_author"] == 0 ) {$wpuserid=$whichuser;} else {$wpuserid=$wordbooker_post_options["wordbooker_default_author"];}
	
		if ($wordbooker_post_options["wordbooker_publish_default"]!="on") {
		wordbooker_debugger("Publish Default is not Set, Giving up ",$wpuserid,$post->ID) ;
	 	return;
	}
	wordbooker_debugger("User has been set to : ",$wpuserid,$post->ID,80) ;
	if (!$wbuser = wordbooker_get_userdata($wpuserid) ) {
		wordbooker_debugger("Unable to get FB session for : ",$wpuserid,$post->ID) ;
		return 28;
	}
	wordbooker_debugger("Posting as user : ",$wpuserid,$post->ID,80) ;

	wordbooker_debugger("Calling wordbooker_fbclient_publishaction"," ",$post->ID,99) ;
	wordbooker_fbclient_publishaction($wbuser, $post->ID);
	unset($doing_post);
	return 30;
}


function wordbooker_delete_post($post_id) {	
	global $blog_id;
	wordbooker_debugger("Deleting Post ".$post_id,"Removing Error logs",-4,99) ;
	wordbooker_delete_from_errorlogs($post_id,$blog_id);
	wordbooker_debugger("Deleting Post ".$post_id,"Removing post logs",-4,99) ;
	wordbooker_delete_from_postlogs($post_id,$blog_id);
	wordbooker_debugger("Deleting Post ".$post_id,"Removing FB comment logs",-4,99) ;
	wordbooker_delete_from_commentlogs($post_id,$blog_id);
}

function wordbooker_process_post_queue($post_id) {
	global $wpdb,$blog_id;
	# We need to get the lowest post_id from the post_queue which has the lowest priority ID
}

function wordbooker_process_post_data($newstatus, $oldstatus, $post) {
	global $user_ID, $user_identity, $user_login, $wpdb, $blog_id;
	# If this is an autosave then we give up and return as otherwise we lose user settings.
        # This is where we need to put in the custom post type checks
	if ($post->post_type=='reply') {return;}
	if ($_POST['action']=='autosave') { return;}
	if ($_POST['action']=='editpost') { 
		foreach (array_keys($_POST) as $key ) {
			if (substr($key,0,8)=='wordbook') {
				$wordbooker_sets[$key]=str_replace( array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),array('&','"','\'','<','>',"\t"),$_POST[$key]);
			}
		}
		update_post_meta($post->ID, '_wordbooker_options', $wordbooker_sets); 
	}
	if (!$newstatus=="publish") { return;}
	# If this is a password protected post we give up
	if ($post->post_password != '') {return;}
	# Check for non public custom post types.
	if ( $post->post_status == 'publish' && $post->post_type != 'post' ) {
		$post_type_info = get_post_type_object( $post->post_type );
		if ( $post_type_info && !$post_type_info->public ) { return; }
	}
	# Has this been fired by a post revision rather than a proper publish
	if (wp_is_post_revision($post->ID)) {return;}

	$wordbooker_settings=wordbooker_options();
	$wb_params = get_post_meta($post->ID, '_wordbooker_options', true); 
	if (! wordbooker_get_userdata($post->post_author)) { $wb_user_id=$wordbooker_settings["wordbooker_default_author"];}
	if  ($wordbooker_settings["wordbooker_default_author"] == 0 ) {$wb_user_id=$post->post_author;} else {$wb_user_id=$wordbooker_settings["wordbooker_default_author"];}
	if ( (!is_array($wb_params)) &&((stripos($_POST["_wp_http_referer"],'press-this')) || ( stripos($_POST["_wp_http_referer"],'index.php')) || (!isset($_POST['wordbooker_post_edited']) )) ) {
		wordbooker_debugger("Inside the press this / quick press / remote client block "," ",$post->ID) ;
		# Get the default publish setting for the post type
		if($post->post_type=='page'){
			$publish=$wordbooker_settings["wordbooker_publish_page_default"];		
		}
		else {
			$publish=$wordbooker_settings["wordbooker_publish_post_default"];
		}
		# New get the user level settings from the DB
		$wordbooker_user_settings_id="wordbookuser".$blog_id;
		$wordbookuser=get_usermeta($wb_user_id,$wordbooker_user_settings_id);
		# If we have user settings then lets go through and override the blog level defaults.
		if(is_array($wordbookuser)) {
			foreach (array_keys($wordbookuser) as $key) {
				if ((strlen($wordbookuser[$key])>0) && ($wordbookuser[$key]!="0") ) {
				#	wordbooker_debugger(" 1st replacing  ".$key." - ".$wordbooker_settings[$key]." with ",$wordbookuser[$key],$post->ID) ;
					$wordbooker_settings[$key]=$wordbookuser[$key];
				} 
			}

		}
		$wordbooker_settings['wordbooker_publish_default']=$publish;
		# Then populate the post array.
		if (is_array($wordbooker_settings)) {
			foreach (array_keys($wordbooker_settings) as $key ) {
				if (substr($key,0,8)=='wordbook') {
					$_POST[$key]=str_replace( array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),array('&','"','\'','<','>',"\t"),$wordbooker_settings[$key]);
				}
			}
		}
	}

	if ( !wordbooker_get_userdata($post->post_author)) {
		wordbooker_debugger("No Settings for ".$post->post_author." so using default author settings",' ',$post->ID,80);
		$wb_user_id=$wordbooker_settings["wordbooker_default_author"];
		# New get the user level settings from the DB
		$wordbooker_user_settings_id="wordbookuser".$blog_id;
		$wordbookuser=get_usermeta($wb_user_id,$wordbooker_user_settings_id);
		# If we have user settings then lets go through and override the blog level defaults.
		if(is_array($wordbookuser)) {
			foreach (array_keys($wordbookuser) as $key) {
				if ((strlen($wordbookuser[$key])>0) && ($wordbookuser[$key]!="0") ) {
					$wordbooker_settings[$key]=$wordbookuser[$key];
				} 
			}

		}

		# Then populate the post array.
			if(is_array($wordbooker_settings)) {
			foreach (array_keys($wordbooker_settings) as $key ) {
				if (substr($key,0,8)=='wordbook') {
					if (!isset($_POST[$key])){$_POST[$key]=str_replace( array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),array('&','"','\'','<','>',"\t"),$wordbooker_settings[$key]);}
				}
			}
		}
	}
	# OK now lets get the settings from the POST array
	foreach (array_keys($_POST) as $key ) {
		if (substr($key,0,8)=='wordbook') {
		#	wordbooker_debugger(" 2nd replacing  ".$key." - ".$wb_params[$key]." with ",$_POST[$key],$post->ID) ;
			$wb_params[$key]=str_replace(array('&','"','\'','<','>',"\t",), array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),$_POST[$key]);
		}
	}
	if ($newstatus=="future") { 
		$wb_params['wordbooker_scheduled_post']=1;
		wordbooker_debugger("This looks like a post that is scheduled for future publishing",$newstatus,$post->ID,80);
	}	
	if ($newstatus=="publish" && (!isset($oldstatus) || $oldstatus!="publish") ) { 
		wordbooker_debugger("This looks like a new post being published ",$newstatus,$post->ID,80) ;
		$wb_params['wordbooker_new_post']=1;

	}
	
	update_post_meta($post->ID, '_wordbooker_options', $wb_params); 

	if ($newstatus=="publish") {
		wordbooker_debugger("Calling Wordbooker publishing function",' ',$post->ID,90) ;
		wordbooker_publish($post->ID);
	}
} 

function wordbooker_publish($post_id) {
	global $user_ID, $user_identity, $user_login, $wpdb, $blog_id,$wordbooker_settings;
	$post = get_post($post_id);
	if ((isset($user_ID) && $user_ID>0) &&  (!current_user_can(WORDBOOKER_MINIMUM_ADMIN_LEVEL))) { wordbooker_debugger("This user doesn't have enough rights"," ",$post_id,99) ; return; }
	wordbooker_debugger("Commence Publish "," ",$post_id,99) ; 
	#$wb_params = get_post_meta($post_id, '_wordbooker_options', true); 
	$wordbooker_settings = wordbooker_options();
	# If there is no user row for this user then set the user id to the default author. If the default author is set to 0 (i.e current logged in user) then only blog level settings apply.
	if (! wordbooker_get_userdata($post->post_author)) { $wb_user_id=$wordbooker_settings["wordbooker_default_author"];}
	if  ($wordbooker_settings["wordbooker_default_author"] == 0 ) {$wb_user_id=$post->post_author;} else {$wb_user_id=$wordbooker_settings["wordbooker_default_author"];}
	# If we've no FB user associated with this ID and the blog owner hasn't overridden then we give up.
	if ((! wordbooker_get_userdata($post->post_author))  && ( !isset($wordbooker_settings['wordbooker_publish_no_user'])))  { wordbooker_debugger("Not a WB user (".$post->post_author.") and no overide - give up "," ",$post_id,99) ; return;}
	if ((! wordbooker_get_userdata($wb_user_id))  && ( !isset($wordbooker_settings['wordbooker_publish_no_user'])))  {wordbooker_debugger("Author (".$post->post_author.") not a WB user and no overide- give up "," ",$post_id,99) ;  return;}
	if ($_POST["wordbooker_default_author"]== 0 ) { wordbooker_debugger("Author of this post is the Post Author"," ",$post->ID,80);  $_POST["wordbooker_default_author"]=$post->post_author; }
	wordbooker_debugger("Options Set - call transition  "," ",$post_id,80) ;
	$retcode=wordbooker_publish_action($post_id);
	return $retcode;
}


function wordbooker_publish_remote($post_id) {
	global $blog_id;
	$post = get_post($post_id);
	wordbooker_debugger("Commence Remote publish "," ",$post->ID,80) ; 
	$wordbooker_settings = wordbooker_options();
} 


function wordbooker_debugger($method,$error_msg,$post_id,$level=9) {
	global $user_ID,$post_ID,$wpdb,$blog_id,$post,$wbooker_user_id,$comment_user;
	$usid=1;
	$usid=$user_ID;
	if (isset($user_ID)) {$usid=$user_ID;}
	if (isset($post_id) && ($post_id>=1)){
		$p=get_post($post_id);
		#we dont want to record anything if its an draft of any kind
		if (stristr($p->post_status,'draft')) {return;}
	$usid=$p->post_author;
	}
	$admin_id=wordbooker_get_option('wordbooker_diagnostic_admin');
	$row_id=1;
	if (!isset($admin_id)) {$admin_id=1;}
	if (!isset($post_id)) {$post_id=$post_ID;}
	if (!isset($post_id)) {$post_id=1;}
	if ($usid==0) {$usid=$wbooker_user_id;}
	if (!isset($usid)) {$usid=wordbooker_get_option('wordbooker_default_author');}
	if (!isset($usid)) {$usid=$admin_id;}
	if ($usid==0) {$usid=$admin_id;}
	if ($post_id==-3) {$usid=$comment_user;}
	if ($post_id==-2) {$usid=$admin_id;}
	if ($post_id==-1) {$usid=$wbooker_user_id;}
	if ($post_id==0) {$usid=$user_ID;}

	$sql=	"INSERT INTO " . WORDBOOKER_ERRORLOGS . " (
				user_id
				, method
				, error_code
				, error_msg
				, post_id
				, blog_id
				, diag_level
			) VALUES (  
				" . $usid . "
				, '" . mysql_real_escape_string($method) . "'
				, $row_id
				, '" . mysql_real_escape_string($error_msg) . "'
				, " . $post_id . "
				, " . $blog_id ."
				, " . $level ."
			)";
	$result = $wpdb->query($sql);
}

function wordbooker_remove_wordbooker(){
	$table_array= array (WORDBOOKER_ERRORLOGS,WORDBOOKER_POSTLOGS,WORDBOOKER_USERDATA,WORDBOOKER_USERSTATUS,WORDBOOKER_POSTCOMMENTS,WORDBOOKER_PROCESS_QUEUE,WORDBOOKER_FB_FRIENDS,WORDBOOKER_FB_FRIEND_LISTS);
	foreach ($table_array as $table) {
		$sql="Drop table ".$table;
		#$result = $wpdb->query($sql);
		echo "Dropping table : ".$table."<br /";
	}
	echo "Removing commment cron ";
	$dummy=wp_clear_scheduled_hook('wb_comment_job');
	echo "Removing Status Cache cron";
	$dummy=wp_clear_scheduled_hook('wb_cron_job');
	delete_option(WORDBOOKER_SETTINGS); 
	
}
/******************************************************************************
 * Register hooks with WordPress.
 */

/* Plugin maintenance. */
register_activation_hook(__FILE__, 'wordbooker_activate');
# When a user is deleted from the blog we should clear down everything they've done in Wordbooker.
add_action('delete_user', 'wordbooker_remove_user');
add_action ('init', 'wordbooker_init');
 
function wordbooker_init () {
	load_plugin_textdomain ('wordbooker',false,basename(dirname(__FILE__)).'/languages');
}

function wordbooker_schema($attr) {
        $attr .= " xmlns:fb=\"http://www.facebook.com/2008/fbml\" xmlns:og=\"http://ogp.me/ns/fb#\"  ";
        return $attr;
}

function wordbooker_get_avatar($avatar, $comment, $size="50"){
      $author_url = get_comment_author_url();
	$fb_id=get_comment_meta($comment->comment_ID,'fb_uid',true);
	if (strlen($fb_id)<1) {
	 $parse_author_url = (parse_url($author_url));
	         $fb_id_array = explode('/',$parse_author_url['path']);
          $sizer = count($fb_id_array) -1;
          $fb_id =  $fb_id_array[$sizer];
	}
	if (strlen($fb_id)>1) {
	$grav_url= "http://graph.facebook.com/".$fb_id."/picture?type=square";
    $avatar = "<img src='".$grav_url."'  height='".$size."' width='".$size."' class='avatar avatar-40 photo' /> ";}
    return $avatar;
}

function wordbooker_custom_cron_schedules($schedules){
	$schedules['10mins'] = array(
	'interval'   => 600,
	'display'   => __('Every 10 Minutes', 'wordbooker'),
	);

	$schedules['15mins'] = array(
	'interval'   => 900,
	'display'   => __('Every 15 Minutes', 'wordbooker'),
	);

	$schedules['20mins'] = array(
	'interval'   => 1200,
	'display'   => __('Every 20 Minutes', 'wordbooker'),
	);

	$schedules['30mins'] = array(
	'interval'   => 1800,
	'display'   => __('Every 30 Minutes', 'wordbooker'),
	);

	$schedules['45mins'] = array(
	'interval'   => 2700,
	'display'   => __('Every 45 Minutes', 'wordbooker'),
	);

	$schedules['2hours'] = array(
	'interval'   => 7200,
	'display'   => __('Every 2 Hours', 'wordbooker'),
	);

	return array_merge($schedules);
}
	
/* Post/page maintenance and publishing hooks. */
$wordbooker_disabled=wordbooker_get_option('wordbooker_disabled');
# Includes - trying to keep my code base tidy.
include("includes/wordbooker_options.php");

# If they've disabled Wordbooker then we don't need to load any of these.
if (!isset($wordbooker_disabled)){
	include("includes/wordbooker_wb_widget.php");
	include("includes/wordbooker_fb_widget.php");
	include("includes/wordbooker_cron.php");
	include("includes/wordbooker_posting.php");
	include("includes/wordbooker_comments.php");
	#include("includes/wordbooker_get_friend.php");
	#include("includes/custom_quick_edit.php");
}
# If they've disabled Wordbooker then we don't need any of these
if (!isset($wordbooker_disabled)){
	$wordbooker_fb_gravatars=wordbooker_get_option('wordbooker_no_facebook_gravatars');
	add_action('transition_post_status', 'wordbooker_process_post_data',10,3);
	add_action('delete_post', 'wordbooker_delete_post');
	add_action('wb_cron_job', 'wordbooker_poll_facebook');
	add_action('wb_comment_job', 'wordbooker_poll_comments');
	add_action('wp_head', 'wordbooker_header');
	add_action('wp_footer', 'wordbooker_footer');
	add_filter('language_attributes', 'wordbooker_schema');
	if (!isset($wordbooker_fb_gravatars)){
		add_filter('get_avatar','wordbooker_get_avatar',1, 3 );
	}
	add_filter('the_content', 'wordbooker_append_post');
	add_filter('the_excerpt','wordbooker_append_post');
	add_filter('cron_schedules','wordbooker_custom_cron_schedules');
	add_shortcode('wb_fb_like', 'wordbooker_fb_like_inline');
	add_shortcode('wb_fb_send', 'wordbooker_fb_send_inline');
	add_shortcode('wb_fb_share', 'wordbooker_fb_share_inline');
	add_shortcode('wb_fb_comment', 'wordbooker_fb_comment_inline');
	add_shortcode('wb_fb_read','wordbooker_fb_read_inline');

}

# This is for support for alternative posting processes. Only Curl is supported right now
#if (wordbooker_get_option('wordbooker_fopen_curl')=='fopen'){
	#	include("includes/wordbooker_facebook_fopen.php");
	#}	
#	else {
	include("includes/wordbooker_facebook_curl.php");
#	}

?>
