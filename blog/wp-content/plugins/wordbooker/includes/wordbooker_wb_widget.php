<?php

/*
Description: Facebook Status Widget. Needs Wordbook installing to work.
Author: Stephen Atty
Author URI: http://canalplan.blogdns.com/steve
Version: 2.1
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

#global $wp_version;

class WordbookWidget extends WP_Widget {
	 
	function WordbookWidget() {
		parent::WP_Widget('wordbooker_widget', 'Wordbooker FB Status ', array('description' => __('Allows you to have one or more Facebook Status widgets in your sidebar. The widget picks up the user id of the person who drags it onto the side bar','wordbooker') , 'class' => 'WordbookWidget'));	
	}
	
	/**
	 * display widget
	 */	 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		global  $wpdb, $user_ID,$table_prefix,$blog_id;
		$userid=$instance['snorl'];
		$result = wordbooker_get_cache($userid);
		echo $before_widget;
		$name=$result->name;
         	if (strlen($instance['dname']) >0 ) $name=$instance['dname'];
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
                echo '<br /><div class="facebook_picture" align="center">';
                echo '<a href="'.$result->url.'" target="facebook">';
                echo '<img src="'. $result->pic.'" alt=" FB photo for '.$name.'" /></a>';
                echo '</div>';
	
                if ($result->status) {			
			$current_offset=0;
		#	$current_offset = get_option('gmt_offset');
                	echo '<p><br /><a href="'.$result->url.'">'.$name.'</a> : ';
			echo '<i>'.$result->status.'</i><br />';
       			if ($instance['df']=='fbt') { 
         			echo '('.nicetime($result->updated+(3600*$current_offset)).').'; 
			}
         		else {
				echo '('.date($instance['df'], $result->updated+(3600*$current_offset)).').';
			}
		}
		echo "</p>".$after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['snorl'] = $new_instance['snorl'];
		$instance['dname'] = strip_tags($new_instance['dname']);
		$instance['df'] = strip_tags($new_instance['df']);
		return $instance;
	}	
	 	
	function form($instance) {
		global $user_ID;
		$default = array( 'title' => __('Facebook Status','wordbooker'), 'snorl'=>$user_ID, 'dname'=>'', 'df'=>'D M j, g:i a' );
		$instance = wp_parse_args( (array) $instance, $default );
		$title_id = $this->get_field_id('title');
		$title_name = $this->get_field_name('title');
		$snorl_id = $this->get_field_id('snorl');
		$snorl_name = $this->get_field_name('snorl');
		$dname_id = $this->get_field_id('dname');
		$dname_name = $this->get_field_name('dname');
		$df_id = $this->get_field_id('df');
		$df_name = $this->get_field_name('df');
		echo '<p><label for="'.$title_id.'">'.__('Title of Widget','wordbooker').': </label> <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.attribute_escape( $instance['title'] ).'" /></p>';
		echo '<p><label for="'.$dname_id.'">'.__('Display this name','wordbooker').': <input type="text" class="widefat" id="'.$dname_id.'" name="'.$dname_name.'" value="'.attribute_escape( $instance['dname'] ).'" /></label></p>';
		echo '<input type="hidden" class="widefat" id="'.$snorl_id.'" name="'.$snorl_name.'" value="'.attribute_escape( $instance['snorl'] ).'" /></p>';
		echo '<p><label for="'.$df_id.'">'.__('Date Format','wordbooker').':  </label>'; 
		echo '<select id=id="'.$df_id.'"  name="'.$df_name.'" >';
		$ds12=date('D M j, g:i a');
		$dl12=date('l F j, g:i a');
		$dl24=date('l F j, G:i');
		$ds24=date('D M j, G:i');
		$drfc=date('r');
		$arr = array('D M j, g:i a'=> $ds12,  'l F j, g:i a'=> $dl12, 'D M j, G:i'=>$ds24, 'l F j, G:i'=>$dl24,fbt=>__("Facebook Text style",'wordbooker'), r =>$drfc);
		foreach ($arr as $i => $value) {
		if ($i==attribute_escape( $instance['df'])){ print '<option selected="yes" value="'.$i.'" >'.$arr[$i].'</option>';}
		else {print '<option value="'.$i.'" >'.$arr[$i].'</option>';}
		}
		echo '</select></p>';
	}
}

/* register widget when loading the WP core */
add_action('widgets_init', 'wordbooker_widgets');
$plugin_dir = basename(dirname(__FILE__));
#load_plugin_textdomain( 'wordbook', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

function wordbooker_widgets(){
	register_widget('WordbookWidget');
}


function nicetime($date)
{
   
    $periods         = array(__("second",'wordbooker'), __("minute",'wordbooker'), __("hour",'wordbooker'), __("day",'wordbooker'), __("week",'wordbooker'), __("month",'wordbooker'), __("year",'wordbooker'), __("decade",'wordbooker'));
    $lengths         = array("60","60","24","7","4.35","12","10");
   
    $now             = time();
    $unix_date         = $date;
   
       // check validity of date
    if(empty($unix_date)) {   
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {   
        $difference     = $now - $unix_date;
        $tense         = __("ago", 'wordbooker');
       
    } else {
        $difference     = $unix_date - $now;
        $tense         = __("from now", 'wordbooker');
    }
   
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
   
    $difference = round($difference);
   
    if($difference != 1) {
        $periods[$j].= "s";
    } else {$difference=__("an",'wordbooker');}
   
    return __("about",'wordbooker')." $difference $periods[$j] {$tense}";
}


 
?>
