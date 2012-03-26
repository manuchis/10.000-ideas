<?php
require_once('includes/constants.php');
require_once('includes/functions.php');

//-=-= the cms folder =-=-

$cms_folder = currentFolder($_SERVER['SCRIPT_NAME']);

//-=-= keep the session =-=-

if (!isset($_SESSION))
{
	ini_set("session.gc_maxlifetime", 10800);
	session_name(SESNAME);
	session_start();
	session_regenerate_id();
}

//-=-= unset the session data =-=-

$_SESSION['userName'] = NULL;
unset($_SESSION['userName']);

$_SESSION['userToken'] = NULL;
unset($_SESSION['userToken']);

//-=-= kill the cookie =-=-

$life_time = time()-(60*60*24*5); //5 days
setcookie("CMS_loggedin", "", $life_time, $cms_folder, "", 0, 1);

//-=-= destroy the session =-=-

session_destroy();

//-=-= redirect =-=-

$redir_url = "login.php".$pass_variables;
redir($redir_url);
?>