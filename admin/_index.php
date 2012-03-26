<?php
include('includes/header.php');

//=====================================
// the classes
//=====================================
require_once('classes/class.site.php');
require_once('classes/class.admin.php');
//=====================================
// get the variables
//=====================================
$user_data = $user->getUserByToken($_SESSION['userToken']);
$row_user = $user->fetchObject($user_data);

$this_page = $_SERVER['PHP_SELF'];

$s = "admin";
if(isset($_GET['s']) && $_GET['s'] != '')
	$s = $_GET['s'];

$id = 0;
if(isset($_GET['id']) && $_GET['id'] != '')
	$id = $_GET['id'];
	
$l;
if(isset($_GET['l']) && $_GET['l'] != '')
	$l = $_GET['l'];

$y;
if(isset($_GET['y']) && $_GET['y'] != '')
	$y = $_GET['y'];

$page = 0;
if(isset($_GET['page']) && $_GET['page'] != '')
	$page = $_GET['page'];

$pd;
if(isset($_GET['pd']) && $_GET['pd'] != '')
	$pd = $_GET['pd'];
$spd;
if(isset($_GET['spd']) && $_GET['spd'] != '')
	$spd = $_GET['spd'];

//=====================================
// start the site connection
//=====================================

$site = new site();
$admin = new admin();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BINARIA Tracker</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../css/datePicker.css" rel="stylesheet" type="text/css" media="screen" />
<script src="../js/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/highcharts.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/exporting.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/jquery.date.js" type="text/javascript" charset="utf-8"></script>

<script src="../js/jquery.datePicker.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/functions.js" type="text/javascript" charset="utf-8"></script>



</head>

<body>
<div id="container">
	<div id="top">
		<div class="login"><?php echo $row_user->nombre; ?> <a href="../logout.php">salir</a></div>
		<h1>Binaria Tracker</h1>
	</div>
	<div id="sidebar">
		<ul>
			<li><a></a></li>
		</ul>
	</div>
	<div id="main">
		
	<div id="grafico">
	
	
	
	</div>
	</div>
	<div id="footer">2011 ® Binaria</div>
</div>

</body>

</html>