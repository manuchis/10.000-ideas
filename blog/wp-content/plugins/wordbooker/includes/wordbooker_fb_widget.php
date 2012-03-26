<?php

/*
Description: Facebook Fan Box Widget. Needs Wordbook installing to work.
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

global $wp_version;
if((float)$wp_version >= 2.8){

class FacebookWidget extends WP_Widget {
	
	/**
	 * constructor
	 */	 
	function FacebookWidget() {
		parent::WP_Widget('wordbookfb_widget', 'Wordbooker FB Like', array('description' => __('Allows you to have multiple Like/Fan boxes. Fan pages cane be picked from a dropdown list in the options. Each user gets the choice of all the FB Fan pages they administer','wordbooker') , 'class' => 'FacebookWidget'));	
	}
	
	/**
	 * display widget
	 */	 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		global  $wpdb, $user_ID,$table_prefix,$blog_id;
		$userid=$instance['snorl'];
		$wordbooker_settings = wordbooker_options(); 
		echo $before_widget;
         	if (strlen($instance['dname']) >0 ) $name=$instance['dname'];
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		$wplang="en_US";
		if (strlen(WPLANG) > 2) {$wplang=WPLANG;}
		echo "<p>";
		$height = $instance['height'];
		$width = $instance['width'];
		$connections=$instance['connections'];
		$border_colour=$instance['border_colour'];
		$scheme=$instance['scheme'];
		$stream="false";
		$header="false";
		$faces="false";
		if ($instance['stream']=='on') {
			$height = $height+300;
			$stream="true";
		} 
		if ($instance['header']=='on') {
			$height = $height + 18;
			$header="true";
		}

		if ($instance['faces']=='on') {
			$faces="true";
			$height=$height+40;
		} 
		$fanpages=unserialize(stripslashes($instance['fanpages']));
		$url=$fanpages[$instance['pid']];
		if ( (!isset($wordbooker_settings['wordbooker_like_button_show']) && !isset($wordbooker_settings['wordbooker_like_share_too'])) || isset($wordbooker_settings['wordbooker_iframe'])) {
	echo'<iframe src="http://www.facebook.com/plugins/likebox.php?href='.urlencode($url).'&amp;width='.$width.'&amp;colorscheme='.$scheme.'&amp;show_faces='.$faces.'&amp;border_color=%23'.$border_colour.'&amp;stream='.$stream.'&amp;header='.$header.'&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
		}
		else {
		echo '<fb:like-box href="'.$url.'" width="'.$width.'" height="'.$height.'"  colorscheme="'.$scheme.'" show_faces="'.$faces.'" border_color="#'.$border_colour.'" stream="'.$stream.'" header="'.$header.'"></fb:like-box>';
		}	
	#	echo '<br /><div class="fb-add-to-timeline" data-show-faces="true" data-mode="button"></div>';
		echo $after_widget;
	}
	
	/**
	 *	update/save function
	 */	 	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['snorl'] = $new_instance['snorl'];
		$instance['dname'] = strip_tags($new_instance['dname']);
		$instance['pid'] = strip_tags($new_instance['pid']);
		$instance['stream'] = strip_tags($new_instance['stream']);
		$instance['scheme'] = strip_tags($new_instance['scheme']);
		$instance['border_colour'] = strip_tags($new_instance['border_colour']);
		$instance['header'] = strip_tags($new_instance['header']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['faces'] = strip_tags($new_instance['faces']);
		$instance['fanpages'] = $new_instance['fanpages'];
		return $instance;
	}
	
	/**
	 *	admin control form
	 */	 	
	function form($instance) {
		global $user_ID,$wpdb,$table_prefix,$blog_id;
		$result = wordbooker_get_cache($user_ID,'pages',1);
		$fanpages=unserialize($result->pages);
		$xx=array('id'=>'FW:254577506873','name'=>'Wordbooker','url'=>'http://www.facebook.com/Wordbooker');
		$fanpages[]=$xx;
		$default = array( 'title' => __('Fan Page','wordbooker'), 'snorl'=>$user_ID, 'dname'=>'', 'pid'=>'254577506873', 'stream'=>'false', 'connections'=>6, 'width'=>188, 'height'=>260, 'header'=>'false', 'scheme'=>'light' );
		$instance = wp_parse_args( (array) $instance, $default );

		$title_id = $this->get_field_id('title');
		$title_name = $this->get_field_name('title');

		$snorl_id = $this->get_field_id('snorl');
		$snorl_name = $this->get_field_name('snorl');

		$fanpages_id = $this->get_field_id('fanpages');
		$fanpages_name = $this->get_field_name('fanpages');

		$dname_id = $this->get_field_id('dname');
		$dname_name = $this->get_field_name('dname');

		$df_id = $this->get_field_id('pid');
		$df_name = $this->get_field_name('pid');

		$stream_id = $this->get_field_id('stream');
		$stream_name = $this->get_field_name('stream');

		$faces_id = $this->get_field_id('faces');
		$faces_name = $this->get_field_name('faces');

		$scheme_id = $this->get_field_id('scheme');
		$scheme_name = $this->get_field_name('scheme');

		$header_id = $this->get_field_id('header');
		$header_name = $this->get_field_name('header');

		$width_id = $this->get_field_id('width');
		$width_name = $this->get_field_name('width');

		$height_id = $this->get_field_id('height');
		$height_name = $this->get_field_name('height');

		$border_id = $this->get_field_id('border_colour');
		$border_name = $this->get_field_name('border_colour');

		$colorscheme=array('dark'=>'Dark','light'=>'Light');
		$checked_flag=array('on'=>'checked','off'=>'', 'true'=>'checked', 'false'=>'');
		if (!is_numeric($instance['width']) || $instance['width'] <0) {$instance['width']=188;}
		if (!is_numeric($instance['height']) || $instance['height'] <0) {$instance['height']=260;}
		echo '<input type="hidden" class="widefat" id="'.$snorl_id.'" name="'.$snorl_name.'" value="'.attribute_escape( $instance['snorl'] ).'" /></p>';

		echo '<p><label for="'.$title_id.'">'.__('Title of Widget','wordbooker').': </label> <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.attribute_escape( $instance['title'] ).'" /></p>';
		$fanpagelist='';
		echo "\r\n".'<p><label for="'.$df_id.'">'.__('Fan Page','wordbooker').':  </label>'; 
		echo '<select id=id="'.$df_id.'"  name="'.$df_name.'" >';
		foreach ($fanpages as $fan_page) {
			$fanpagelist[$fan_page['id']]=$fan_page['url'];
			if ($fan_page[id]==attribute_escape( $instance['pid'])){ 
				print '<option selected="yes" value="'.$fan_page[id].'" >'.$fan_page[name].'</option>';}
			else {
				print '<option value="'.$fan_page[id].'" >'.$fan_page[name].'</option>';
			}
		}
		echo '</select></p>';
		echo "<input type='hidden' class='widefat' id='".$fanpages_id."' name='".$fanpages_name."' value='".mysql_real_escape_string(serialize($fanpagelist))."' />";
		echo '<p><label for="'.$stream_id.'">'.__("Include Stream ", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX class="widefat"id="'.$stream_id.'" name="'.$stream_name.'" '.$checked_flag[attribute_escape( $instance['stream'])].' /></p>';

		echo '<p><label for="'.$stream_id.'">'.__("Include Header ", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX class="widefat"id="'.$header_id.'" name="'.$header_name.'" '.$checked_flag[attribute_escape( $instance['header'])].' /></p>';
		
		echo '<p><label for="'.$scheme_id.'">'.__('Colour Scheme','wordbooker').': </label> <select id="'.$scheme_id.'" name="'.$scheme_name.'"  >';
		foreach ($colorscheme as $i => $value) {
			if ($i==$instance['scheme']){ print '<option selected="yes" value="'.$i.'" >'.$colorscheme[$i].'</option>';}
		       else {print '<option value="'.$i.'" >'.$colorscheme[$i].'</option>';}}
		echo "</select><br />";

		echo '<p><label for="'.$faces_id.'">'.__("Show Faces ", 'wordbooker'). ' : </label>';
		echo '<INPUT TYPE=CHECKBOX class="widefat"id="'.$faces_id.'" name="'.$faces_name.'" '.$checked_flag[attribute_escape( $instance['faces'])].' /></p>';

		echo '<p><label for="'.$width_id.'">'.__('Widget Width','wordbooker').': </label> <input type="text" size="7" id="'.$width_id.'" name="'.$width_name.'" value="'.attribute_escape( $instance['width'] ).'" /></p>';

		echo '<p><label for="'.$height_id.'">'.__('Widget Height','wordbooker').': </label> <input type="text" size="7" id="'.$height_id.'" name="'.$height_name.'" value="'.attribute_escape( $instance['height'] ).'" /></p>';

		echo '<p><label for="'.$height_id.'">'.__('Widget Border Colour','wordbooker').': </label>#<input type="text" size="6" maxlength=6 id="'.$border_id.'" name="'.$border_name.'" value="'.attribute_escape( $instance['border_colour'] ).'" /></p>';
	}
}

function fbwordbooker_widgets(){
	register_widget('FacebookWidget');
}

/* register widget when loading the WP core */
add_action('widgets_init', 'fbwordbooker_widgets');

}

?>
