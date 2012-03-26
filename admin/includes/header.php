<?php 
//-=-= import the neccesary classes =-=-
if(basename($_SERVER['SCRIPT_FILENAME']) == 'index.php')
{
	require_once('includes/constants.php');
	require_once('includes/messages_list.php');
	require_once('includes/functions.php');
	require_once('classes/class.mysql.php');
	require_once('classes/class.loggeduser.php');
	require_once('classes/class.phpmailer.php');
}else{
	require_once('../includes/constants.php');
	require_once('../includes/messages_list.php');
	require_once('../includes/functions.php');
	require_once('../classes/class.mysql.php');
	require_once('../classes/class.loggeduser.php');
	require_once('../classes/class.phpmailer.php');
}

//-=-= start the user data =-=-

$user = new LoggedUser();

//-=-= start the session =-=-

if (!isset($_SESSION))
{
	ini_set("session.gc_maxlifetime", 10800);
	session_name(SESNAME);
	session_start();
	//session_regenerate_id();
}

//-=-= check for the session =-=-

if(!isset($_SESSION['userName']) && !isset($_SESSION['userToken']))
{
	$active_session = false;
	
	//now we check the cookie, so if we have a cookie we could star a session
	
	if(isset($_COOKIE[COOKIE_NAME]))
	{
		$token = $_COOKIE[COOKIE_NAME];
		$user_data = $user->getUserByToken($token);
		
		if($user_data)
		{
			$row_user = $user->fetchObject($user_data);
			$user_name = $row_user->name." ".$row_user->last;
			$user_token = $row_user->token;
			$_SESSION['userName'] = $user_name;
			$_SESSION['userToken'] = $user_token;
			
			//update the last login
			
			$user->lastLogin($row_user->id);
			
			$active_session = true;
		}
	}
	
	//if we can't start a session redirect to the login page
	
	if(!$active_session)
	{
		$restrictGoTo = "login.php";
		$referrer = $_SERVER['PHP_SELF']."?".$QUERY_STRING;
		$restrictGoTo .= "?accesscheck=" . urlencode($referrer);
		
		redir($restrictGoTo);	
	}
}
?>