<?php
define(HOME_URL, 'http://10.000ideas.com/');
function GetSQLValueString($theValue, $theType) 
{
  
  global $message;
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
	case "email":
      $theValue = is_email_valid($theValue) ? "'" . $theValue . "'" : "NULL";
      break;
  }
  
  return $theValue;
}

/* comprobacion de email */

function is_email_valid($email) {
  if(eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $email)) return TRUE;
  else return FALSE;
}

/* redireccion de la pagina */

function redir($destino)
{
	header("location: " . $destino);
	exit();
}

/* ahora */
function now()
{
	return date('Y-m-d G:i:s');
}

/* hoy */

function today()
{
	return date('Y-m-d');
}

/* chekear */

function getChecked($val1, $val2){
	if ($val1 == $val2) return 'checked="checked"';
	else return "";
}

/* seleccionar */

function getSelected($option, $value)
{
	if($option == $value){
		return 'selected="selected"';
	}
}

/* activar pagina actual : navegacion */

function setCurrent($is, $actual)
{
	if($is == $actual)
		return "current";
}

/* get the content of a file */

function getBody($filename)
{
	$handle = fopen ($filename, "r");
	$contents = fread ($handle, filesize ($filename));
	fclose ($handle);
	return $contents;
}

/* random password */

function rndPasswd($intPassLength) {
	 $araChars = array(); 
	 $araChars[0] = "A";
	 $araChars[1] = "B";
	 $araChars[2] = "C";
	 $araChars[3] = "D";
	 $araChars[4] = "E";
	 $araChars[5] = "F";
	 $araChars[6] = "G";
	 $araChars[7] = "H";
	 $araChars[8] = "I";
	 $araChars[9] = "J";
	 $araChars[10] = "K";
	 $araChars[11] = "L";
	 $araChars[12] = "M";
	 $araChars[13] = "N";
	 $araChars[14] = "O";
	 $araChars[15] = "P";
	 $araChars[16] = "Q";
	 $araChars[17] = "R";
	 $araChars[18] = "S";
	 $araChars[19] = "T";
	 $araChars[20] = "U";
	 $araChars[21] = "V";
	 $araChars[22] = "W";
	 $araChars[23] = "X";
	 $araChars[24] = "Y";
	 $araChars[25] = "Z";
	 $araChars[26] = "a";
	 $araChars[27] = "b";
	 $araChars[28] = "c";
	 $araChars[29] = "d";
	 $araChars[30] = "e";
	 $araChars[31] = "f";
	 $araChars[32] = "g";
	 $araChars[33] = "h";
	 $araChars[34] = "i";
	 $araChars[35] = "j";
	 $araChars[36] = "k";
	 $araChars[37] = "l";
	 $araChars[38] = "m";
	 $araChars[39] = "n";
	 $araChars[40] = "o";
	 $araChars[41] = "p";
	 $araChars[42] = "q";
	 $araChars[43] = "r";
	 $araChars[44] = "s";
	 $araChars[45] = "t";
	 $araChars[46] = "u";
	 $araChars[47] = "v";
	 $araChars[48] = "w";
	 $araChars[49] = "x";
	 $araChars[50] = "y";
	 $araChars[51] = "z";
	 $araChars[52] = "1";
	 $araChars[53] = "2";
	 $araChars[54] = "3";
	 $araChars[55] = "4";
	 $araChars[56] = "5";
	 $araChars[57] = "6";
	 $araChars[58] = "7";
	 $araChars[59] = "8";
	 $araChars[60] = "9";
	 $araChars[61] = "0";

	 $strPassword = "";
	 while (!(strlen($strPassword) == $intPassLength)):
		   $strPassword = $strPassword . $araChars[(rand(0,61))];
	 endwhile;
	 return $strPassword;
}

/* color of the td */

function getColor($count)
{
	if($count > 0)
		return "blue";
}

/* status */

function getStatus($status)
{
	if($status == 0)
	{
		return "No publicado";
	} else if($status == 1){
		return "Publicado";
	}
}

/* shor date */

function justDate($date){
	if(($date != "0000-00-00") && ($date != "00/00/0000") && isset($date))
		return date("Y.m.d", strtotime($date));
}

function dateHour($date){
	if(($date != "0000-00-00 00:00:00") && ($date != "00/00/0000 00:00:00") && isset($date))
		return date("d.m.Y", strtotime($date))." a las ".date("H:i:s", strtotime($date));
}

/* the pager */

function getPager($total, $per_page, $page, $link)
{
	include('includes/pager.php');
}

/* the search field */

function getSearch($search_title, $action, $cat, $section, $sub, $q)
{
	include('includes/search.php');
}

/* the gravatar */

function gravatar($email, $size)
{
	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&size=".$size;
	return $grav_url; 
}

/* get the type of profesional */

function getProType($type)
{
	$full_type;
	switch($type)
	{
		case '0':
			$full_type = "Estudiante";
			break;
		case '1':
			$full_type = "Egresado";
			break;
		case '2':
			$full_type = "Profesional";
			break;
	}
	
	return $full_type;
}

/* errors and messages */

function getError($error_num, $custom_error)
{	
	$messages_list = $GLOBALS['messages_list'];
	
	$common_msg = $error_list[0];
	
	switch($error_num)
	{
		case '401':
			$msg = $common_msg.$messages_list['errors'][1];
			break;
		case '403':
			$msg = $messages_list['errors'][2];
			break;
		case '404':
			$msg = $common_msg.$messages_list['errors'][3];
			break;
		case '405':
			$msg = $common_msg.$messages_list['errors'][4];
			break;
		case '408':
			$msg = $messages_list['errors'][5];
			break;
		case '500':
			$msg = $messages_list['errors'][6];
			break;
		case '501':
			$msg = $messages_list['errors'][7];
			break;
		case '101':
			$msg = $custom_error.$messages_list['errors'][8];
			break;
		case '102':
			$msg = $messages_list['errors'][9];
			break;
		case '103':
			$msg = $messages_list['errors'][10];
			break;
		default:
			$msg = $messages_list['errors'][11];
	}
		
	if($error_num)
		echo '<p class="error rounded">'.$msg.'</p>';
}

function getMessage($message)
{		
	if($message != '')
		echo '<p class="message rounded">'.$message.'</p>';
}


/* the priority */

function getPriority($priority)
{
	switch($priority)
	{
		case 0:
			$class = "low";
			$name = "Baja";
			break;
		case 1:
			$class = "medium";
			$name = "Media";
			break;
		case 2:
			$class = "high";
			$name = "Alta";
			break;
		case 3:
			$class = "urgent";
			$name = "Urgente";
			break;
	}
	
	return '<span class="'.$class.'">'.$name.'</span>';
}

/* the message status */

function getMessageStatus($status)
{
	if($status == 0)
	{
		return "Cerrado";
	} else if($status == 1){
		return "Abierto";
	}
}

/* the current folder of a page */

function currentFolder($current_file)
{
	$folder_pieces = explode("/", $current_file);
	$depth = count($folder_pieces);
	$current_folder;
	
	for($i=0; $i<$depth-1; $i++)
	{
		$current_folder .= $folder_pieces[$i]."/";
	}
	
	return $current_folder;
}

/* the images */

function getImage($image, $prefix)
{
	list($name, $ext) = explode(".", $image);
	return $name.$prefix.".".$ext;
}
function meses($mes){
	$elmes;
	switch($mes)
	{
		case 1:
			$elmes = "enero";
			break;
		case 2:
			$elmes = "febrero";
			break;
		case 3:
			$elmes = "marzo";
			break;
		case 4:
			$elmes = "abril";
			break;
		case 5:
			$elmes = "mayo";
			break;
		case 6:
			$elmes = "junio";
			break;
		case 7:
			$elmes = "julio";
			break;
		case 8:
			$elmes = "agosto";
			break;
		case 9:
			$elmes = "septiembre";
			break;
		case 10:
			$elmes = "octubre";
			break;
		case 11:
			$elmes = "noviembre";
			break;
		case 12:
			$elmes = "diciembre";
			break;
	}
	
	return $elmes;
}
function get_facebook_cookie($app_id, $app_secret) {
 if ($_COOKIE['fbsr_' . $app_id] != '') {
        return get_new_facebook_cookie($app_id, $app_secret);
    } else {
        return get_old_facebook_cookie($app_id, $app_secret);
    }
}
function get_old_facebook_cookie($app_id, $app_secret) {
    $args = array();
    parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
    ksort($args);
    $payload = '';
    foreach ($args as $key => $value) {
        if ($key != 'sig') {
            $payload .= $key . '=' . $value;
        }
    }
    if (md5($payload . $app_secret) != $args['sig']) {
        return array();
    }
    return $args;   
}

function get_new_facebook_cookie($app_id, $app_secret) {
    $signed_request = parse_signed_request($_COOKIE['fbsr_' . $app_id], $app_secret);
    // $signed_request should now have most of the old elements
    $signed_request[uid] = $signed_request[user_id]; // for compatibility 
    if (!is_null($signed_request)) {
        // the cookie is valid/signed correctly
        // lets change "code" into an "access_token"
        $access_token_response = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=&client_secret=$app_secret&code=$signed_request[code]");
        parse_str($access_token_response);
        $signed_request[access_token] = $access_token;
        $signed_request[expires] = time() + $expires;
    }
    return $signed_request;
}
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}
function make_links_clickable($text){
    return preg_replace('!(((f|ht)tp://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a target="_blank" href="$1">$1</a>', $text);
}
?>