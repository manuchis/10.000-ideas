<?php
include('includes/header.php');

//-=-= get the user data =-=-

$user_data = $user->getUserByToken($_SESSION['userToken']);
$row_user = $user->fetchObject($user_data);

//-=-= get the section variables =-=-

$c;
if(isset($_GET['c']) && $_GET['c'] != '')
	$c = $_GET['c'];

$m = "dashboard";
if(isset($_GET['m']) && $_GET['m'] != '')
	$m = $_GET['m'];

$load_page = "";

if($c != '')
	$load_page .= "_".$c."/";

if($m != '')
	$load_page .= $m.".php";

if(!file_exists($load_page))
	$load_page = "404.php";

//-=-= variables used on the application =-=-

$this_page = $_SERVER['PHP_SELF']; //this page

$actions_page = "_".$c."/actions.php";

$error_num;
if(isset($_GET['error']) && $_GET['error'] != '') //error number
	$error_num = $_GET['error'];

$custom_error;
if(isset($_GET['cm']) && $_GET['cm'] != '') //custom message
	$custom_error = $_GET['cm'];

$message;
if(isset($_GET['msg']) && $_GET['msg'] != '') //custom message
	$message = $_GET['msg'];

$page = 0;
if(isset($_GET['page']) && $_GET['page'] != '') //page where we are
	$page = $_GET['page'];

$q;
if(isset($_GET['q']) && $_GET['q'] != '') //search query
	$q = $_GET['q'];

$id = 0;
if(isset($_GET['id']) && $_GET['id'] != '') //id
	$id = $_GET['id'];

$status;
if(isset($_GET['status']) && $_GET['status'] != '') //status
	$status = $_GET['status'];

$cat;
if(isset($_GET['cat']) && $_GET['cat'] != '') //category
	$cat = $_GET['cat'];
	
$action = "new";
$page_title = "Nuevo";
if(isset($_GET['id']) && $_GET['id'] != 0) //action and page title
{
	$action = "edit";
	$page_title = "Editar";
}

$items_per_page = TOTAL_ITEMS;
$images_per_page = TOTAL_IMAGES;
$start_row = $page * $items_per_page;
$start_images_row = $page * $images_per_page;
$count = 1;

//-=-=-=-= folders =-=-=-=-

$images_folder = "http://investigacionaccion.com.ar/industrial/media/images/";//PROJECT_URL.IMAGES_FOLDER;
$files_folder = PROJECT_URL.FILES_FOLDER;
$works_folder = "..".WORKS_FOLDER."/";

//-=-=-=-= sections =-=-=-=-

if($c)
	$section = " &raquo; ".ucwords($c);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo PROJECT; ?> &raquo; CMS <?php echo $section; ?></title>
<!-- favicon -->
<link rel="icon" href="images/favicon.png" type="image/png" />
<link rel="shortcut icon" href="images/favicon.png" type="image/png" />
<!-- stylesheets -->
<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/jquery.alerts.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="css/jquery.thickbox.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="css/jquery.ui.css" type="text/css" media="screen, projection" />
<!-- jquery -->
<script type="text/javascript" src="js/jquery.min.js"></script>
<!-- jquery ui -->
<script type="text/javascript" src="js/jquery.ui.all.js"></script>
<!-- table sorter -->
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
<!-- alert box -->
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<!-- jquery validate -->
<script type="text/javascript" src="js/jquery.validate.js"></script>
<!-- jquery thickbox -->
<script type="text/javascript" src="js/jquery.thickbox.js"></script>
<!-- jquery filestyle -->
<script type="text/javascript" src="js/jquery.filestyle.js"></script>
<!-- cms jquery ini -->
<script type="text/javascript" src="js/jquery.cms.js"></script>
<!-- html editor -->
<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="js/editor.js"></script>
<!-- functions -->
<script type="text/javascript" src="js/functions.js"></script>

</head>
<body>

<!-- main container -->

<div id="wrapper">

	<div id="header">
		<div id="logo"><a href="index.php"><img src="images/logo.gif" /></a></div>
		<div id="go"><a href="../" title="Ir al sitio de <?php echo PROJECT; ?>"><?php echo PROJECT; ?> &larr; ir al sitio</a></div>
		<div id="info">Bienvenido <?php echo $row_user->nombre; ?> | <a href="<?php echo $this_page; ?>?c=admin&m=edit&id=<?php echo $row_user->id; ?>" title="Editar perfil">Perfil</a> | <a href="../logout.php" title="Salir del CMS">Salir</a></div>
	</div>
	
	<div id="container">
		
		<div id="menu">
			<?php include('includes/menu.php'); ?>
		</div>
		
		<div id="content">
			
			<?php include($load_page); ?>
			
		</div>
		
		<div id="main" class="clear"></div>
	</div>
	
</div>

<!-- footer -->

	<div id="footer">
		<p><?php echo PROJECT; ?> &copy; 2009</p>
	</div>

</body>
</html>