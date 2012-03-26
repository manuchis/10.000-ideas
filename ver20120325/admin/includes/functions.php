<?php
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

/* send an email with the user data */

function sendUserData($from_name, $from_email, $host, $file, $subject, $name, $email, $password, $project, $url)
{
	$mail = new PHPMailer();
	//$mail->IsSMTP(); // telling the class to use SMTP
	$mail->IsMail(); // telling the class to use SMTP
	$mail->Host = $host; // SMTP server
	$mail->From = $from_email;
	$mail->FromName = $from_name;
	$mail->Subject = $subject;
		
	$body = getBody($file);
	$body = str_replace("<#PROJECT#>", $project, $body);
	$body = str_replace("<#URL#>", $url, $body);
	$body = str_replace("<#NAME#>", $name, $body);
	$body = str_replace("<#EMAIL#>", $email, $body);
	$body = str_replace("<#PASSWORD#>", $password, $body);
	
	$mail->MsgHTML($body);
	$mail->AddAddress($email, $name);
	
	if(!$mail->Send()) {
	  $sended = false;
	} else {
	  $sended = true;
	}
	
	return $sended;
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

/* the messages */

function getMessage($message)
{		
	if($message != '')
		echo '<p class="message rounded">'.$message.'</p>';
}


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

function compararFechas($primera, $segunda)
 {
	$primera = substr($primera, 0, -9);
	$segunda = substr($segunda, 0, -9);
  $valoresPrimera = explode ("-", $primera);   
  $valoresSegunda = explode ("-", $segunda); 
  $dia1    = $valoresPrimera[2];  
  $mes1  = $valoresPrimera[1];  
  $ano1   = $valoresPrimera[0]; 
  $dia2   = $valoresSegunda[2];  
  $mes2= $valoresSegunda[1];  
  $ano2  = $valoresSegunda[0];
$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); 
$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2); 

//resto a una fecha la otra 
$segundos_diferencia = $timestamp1 - $timestamp2; 
//echo $segundos_diferencia; 

//convierto segundos en días 
$dias_diferencia = $segundos_diferencia / (60 * 60 * 24); 

//obtengo el valor absoulto de los días (quito el posible signo negativo) 
$dias_diferencia = abs($dias_diferencia); 

//quito los decimales a los días de diferencia 
$dias_diferencia = floor($dias_diferencia);
	
  return $dias_diferencia;
}
function getMonth($month){
	$result = "";
	switch ($month){
		case "1":
			$result = "Ene";
			break;
		case "2":
			$result = "Feb";
			break;
		case "3":
			$result = "Mar";
			break;
		case "4":
			$result = "Abr";
			break;
		case "5":
			$result = "May";
			break;
		case "6":
			$result = "Jun";
			break;
		case "7":
			$result = "Jul";
			break;
		case "8":
			$result = "Ago";
			break;	
		case "9":
			$result = "Sep";
			break;
		case "10":
			$result = "Oct";
			break;
		case "11":
			$result = "Nov";
			break;
		case "12":
			$result = "Dic";
			break;	
	}
	return $result;
}
function pasarFecha($fecha){
	$fecha = substr($fecha,0,9);
	$fecha = explode("-",$fecha);
	$fecha = $fecha[2]."/".$fecha[1]."/".$fecha[0];
	return $fecha;
}
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
?>