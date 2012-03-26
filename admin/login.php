<?php
//-=-= import the neccesary classes =-=-

require_once('includes/constants.php');
require_once('includes/messages_list.php');
require_once('includes/functions.php');
require_once('classes/class.mysql.php');
require_once('classes/class.login.php');
require_once('classes/class.phpmailer.php');

//-=-= start the session =-=-

if (!isset($_SESSION))
{
	ini_set("session.gc_maxlifetime", 10800);
	session_name(SESNAME);
	session_start();
}

//-=-= ini the class =-=-

$login = new Login();

//-=-= variables =-=-

$cms_folder = currentFolder($_SERVER['SCRIPT_NAME']); //the folder where the cookie is valid

$this_page = $_SERVER['PHP_SELF']; //this page

$attempts = $login->access('login',5); //check the number ot attemps failed to log-in

if(!$attempts)//if there's more than 5 attempts
	$msg = "403";

//-=-= actions =-=-

$action= "login";
if(isset($_GET['action']) && $_GET['action'] != '')
	$action = $_GET['action'];

//-=-= previous page =-=-
	
$accesscheck = "index.php";
if(isset($_GET['accesscheck']) && $_GET['accesscheck'] != '')
	$accesscheck = $_GET['accesscheck'];
	
//-=-= get messages/errors =-=-

$msg;
if(isset($_GET['msg']) && $_GET['msg'] != '')
	$msg = $_GET['msg'];

//-=-= get custom message =-=-

$custom_msg;
if(isset($_GET['cm']) && $_GET['cm'] != '')
	$custom_msg = $_GET['cm'];

//-=-= get the post data to login =-=-

if(isset($_POST['action']) && $_POST['action'] == 'login')
{
	if($attempts)
	{
		//the variables
		
		$user = $_POST['user_login'];
		$password = md5($_POST['user_pass']);
		$remember = $_POST['rememberme'];
		
		$verify = $login->loginUser($user, $password);
		
		if($verify)
		{
			//get the user data
			
			$user_data = $login->getUser($user);
			$row_user = $login->fetchObject($user_data);
			
			$user_name = $row_user->name;
			$user_token = $row_user->token;
			
			//set the session data
			
			$_SESSION['userName'] = $user_name;
			$_SESSION['userToken'] = $user_token;
			
			//set a cookie if the user wanna be remembered
			
			if($remember)
			{
				$life_time = time()+(60*60*24*15); //15 days
				$user_data = $user_token;
				$cookie = setcookie(COOKIE_NAME, $user_data, $life_time, $cms_folder, "", 0, 1);
			}
			
			$login->lastLogin($row_user->id);
			
			$redir_page = $accesscheck;
		}else{
			
			//register the ip, so we could vanish him 20 minutes ;)
			
			$login->fail('login', 20);
			$redir_page = $this_page."?msg=401";
		}
	}else{
		$redir_page = $this_page."?msg=403";
	}
	
	//now redir
	
	redir($redir_page);
}

//-=-= get the post data to recover =-=-

if(isset($_POST['action']) && $_POST['action'] == 'lostpassword')
{
	//get the variables
	
	$email = $_POST['user_login'];
	
	//check if the user entered an email
	
	if($email)
	{
		//check if the user exists
		
		$user_data = $login->getUser($email);
		$row_user = $login->fetchObject($user_data);
		
		if($row_user)
		{
			//the user and email data
			
			$from_name = FROM_NAME;
			$from_email = FROM_EMAIL;
			$host = HOST;
			$file = ROOT.TEXT_FOLDER."/recover.txt";
			$subject = $messages_list['email'][1];
			$name = $row_user->name;
			$email = $row_user->email;
			$password = rndPasswd(8);
			$pswd_md5 = md5($password);
			$project= PROJECT;
			$url = PROJECT_URL;
			
			//reset the password
			
			$change_password = $login->changePassword($row_user->id, $pswd_md5);
			
			if($change_password)
			{
				//send the email
				
				$sended = sendUserData($from_name, $from_email, $host, $file, $subject, $name, $email, $password, $project, $url);
				
				if($sended)
				{
					$redir_page = $this_page."?action=lostpassword&msg=103";
				}else{
					$redir_page = $this_page."?action=lostpassword&msg=501";
				}
			} else {
				$redir_page = $this_page."?action=lostpassword&msg=500";	
			}
		}else{
			$redir_page = $this_page."?action=lostpassword&msg=404";
		}
	}else{
		$redir_page = $this_page."?action=lostpassword&msg=102";
	}
	//now redir
	
	redir($redir_page);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo PROJECT; ?> &rsaquo; Login</title>
<link rel="stylesheet" href="css/login.css" type="text/css" media="screen" />
<!-- icon -->
<link rel="icon" href="images/favicon.png" type="image/png" />
<link rel="shortcut icon" href="images/favicon.png" type="image/png" />
<!-- focus on the user login -->
<script type="text/javascript">
<!--
	function focusit() {
		document.getElementById('user_login').focus();
	}
	window.onload = focusit;
-->
</script>
</head>

<body class="login">

<div id="login"><h1><a href="../index.php" title="<?php echo PROJECT; ?>"><?php echo PROJECT; ?></a></h1>
	
<?php echo getError($msg, $custom_msg); ?>

<?php if($action == "login"){ ?>

<form class="rounded" name="loginform" id="loginform" action="<?php echo $this_page; ?>" method="post">
	<p>
		<label>Usuario<br />
		<input type="text" name="user_login" id="user_login" class="input rounded" value="" size="20" tabindex="10" /></label>
	</p>

	<p>
		<label>Contrase&ntilde;a<br />

		<input type="password" name="user_pass" id="user_pass" class="input rounded" value="" size="20" tabindex="20" /></label>
	</p>

	<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="1" tabindex="90" /> Recordarme</label></p>

	<p class="submit">
		<input type="hidden" id="action" name="action" value="login" />
		<input type="hidden" id="accesscheck" name="accesscheck" value="<?php echo $accesscheck; ?>" />
		<input type="submit" name="submit" id="submit" value="Login" tabindex="100" />
	</p>
	
</form>

<p id="nav">
<a href="<?php echo $this_page ?>?action=lostpassword">Recuperar contrase&ntilde;a</a>
</p>

<?php } ?>

<?php if($action == "lostpassword"){ ?>

<!-- recover password -->

<form class="rounded" name="lostpasswordform" id="lostpasswordform" action="<?php echo $this_page; ?>" method="post">
	<p>
		<label>E-mail:<br />
		<input type="text" name="user_login" id="user_login" class="input rounded" value="" size="20" tabindex="10" /></label>
	</p>
	<p class="submit"><input type="submit" name="submit" id="submit" value="Enviar" tabindex="100" /></p>
	<input type="hidden" id="action" name="action" value="lostpassword" />

</form>

<p id="nav">
	<a href="<?php echo $this_page; ?>">Ingresar al sitio</a>
</p>

<?php } ?>

</div>

<p id="backtoblog"><a href="../" title="Regresar al sitio">&laquo; Regresar al sitio</a></p>

</body>
</html>
