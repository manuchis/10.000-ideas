<?php
include('../includes/header.php');
require_once('../classes/class.images.php');

//-=-= variables used on the application =-=-

$page = 0;
if(isset($_GET['page']) && $_GET['page'] != '') //page where we are
	$page = $_GET['page'];

$q;
if(isset($_GET['q']) && $_GET['q'] != '') //search query
	$q = $_GET['q'];

$this_page = $_SERVER['PHP_SELF'];
$images_folder = "../../".IMAGES_FOLDER;
$pager_page = $this_page."?id=1";
$source = "gallery";
$items_per_page = 10; //TOTAL_ITEMS;
$start_row = $page * $items_per_page;

//-=-=-=-= the query =-=-=-=-

$images = new Images();

$images_list = $images->getImagesList();
$images_list_limited = $images->getImagesListLimited($start_row, $items_per_page);

//-=-=-=-= search =-=-=-=-

if($q)
{
	$images_list = $images->searchImages($q);
	$images_list_limited = $images->SearchImagesLimited($q, $start_row, $items_per_page);
	$pager_page = $this_page."?id=1&q=".$q;
}

$total_items = $images->numRows($images_list);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo PROJECT; ?> &raquo; Im&aacute;genes</title>

<link rel="stylesheet" href="../css/reset.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../css/pop-ups.css" type="text/css" media="screen, projection">

<!-- jquery -->
<script type="text/javascript" src="../js/jquery.min.js"></script>
<!-- jquery ui -->
<script type="text/javascript" src="../js/jquery.ui.all.js"></script>
<!-- table sorter -->
<script type="text/javascript" src="../js/jquery.tablesorter.js"></script>
<!-- alert box -->
<script type="text/javascript" src="../js/jquery.alerts.js"></script>
<!-- jquery validate -->
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<!-- jquery thickbox -->
<script type="text/javascript" src="../js/jquery.thickbox.js"></script>
<!-- jquery filestyle -->
<script type="text/javascript" src="../js/jquery.filestyle.js"></script>
<!-- cms jquery ini -->
<script type="text/javascript" src="../js/jquery.cms.js"></script>
<!-- functions -->
<script type="text/javascript" src="../js/functions.js"></script>

</head>
<body>

<h3>Im&aacute;genes</h3>

<div class="actions rounded">
	
	<a class="add-button left" href="add_image.php">Nueva imagen</a>
	
	<form class="right rounded" id="search" name="search" action="<?php echo $this_page; ?>" method="get">
		<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
		Buscar: <input class="rounded required" type="text" id="q" name="q" value="<?php echo $q; ?>"> <input class="search" type="submit" />
	</form>
	
	<div class="clear"></div>
			
</div>

<div id="images">
	<?php while($row_image = $images->fetchObject($images_list_limited)){ ?>
	<div class="image rounded">
		<img src="<?php echo $images_folder."/".$row_image->image."_t.".$row_image->ext; ?>" alt="<?php echo $row_image->name; ?>" />
		<ul class="actions">
			<li><a href="#" title="Insertar imagen" onclick="javascript:setImage('<?php echo $row_image->image; ?>', '<?php echo $row_image->ext; ?>', '<?php echo $row_image->id; ?>');">Insertar imagen</a></li>
		</ul>
	</div>
	<?php } ?>
	
	<div class="clear"></div>
	
</div>

<div class="actions rounded">
	<div id="pager">
		<?php getGalleryPager($total_items, $items_per_page, $page, $pager_page, $source); ?>
	</div>
	<div class="clear"></div>
</div>

</body>
</html>