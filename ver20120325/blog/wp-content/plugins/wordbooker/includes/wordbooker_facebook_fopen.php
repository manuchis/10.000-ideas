<?php

/*
Description: Facebook Interface functions - using Fopen related calls
Author: Stephen Atty
Author URI: http://canalplan.blogdns.com/steve
Version: 2.0.4
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

function wordbooker_make_fopen_post_call($url, $data, $optional_headers = null) {
	  $params = array('http' => array(
		      'method' => 'POST',
		      'content' => $data
		    ));
	  if ($optional_headers !== null) {
	    $params['http']['header'] = $optional_headers;
	  }
	  $ctx = stream_context_create($params);
	  $fp = @fopen($url, 'rb', false, $ctx);
	  if (!$fp) {
	    throw new Exception("Problem with $url, $php_errormsg");
	  }
	  $response = @stream_get_contents($fp);
	  if ($response === false) {
	    throw new Exception("Problem reading data from $url, $php_errormsg");
	  }
	  return $response;
}



function wordbooker_make_fopen_call($url, $params, $optional_headers = null) {
	  $params = array('http' => array(
		      'content' => $data
		    ));
	  if ($optional_headers !== null) {
	    $params['http']['header'] = $optional_headers;
	  }
	  $ctx = stream_context_create($params);
	  $fp = @fopen($url, 'rb', false, $ctx);
	  if (!$fp) {
	    throw new Exception("Problem with $url, $php_errormsg");
	  }
	  $response = @stream_get_contents($fp);
	  if ($response === false) {
	    throw new Exception("Problem reading data from $url, $php_errormsg");
	  }
	  return $response;
}
?>
