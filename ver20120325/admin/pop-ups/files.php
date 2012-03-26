<?php
include('../includes/header.php');
require_once('../classes/class.files.php');
require_once('../classes/class.related.php');

//the page variables

$id;
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}

$type;
if(isset($_GET['type']))
	$type = $_GET['type'];

$title;
if(isset($_GET['title']))
	$title = $_GET['title'];

$this_page = $_SERVER['PHP_SELF'];

//load the data

$files = new Files();
$related = new Related();

//update the related files

if(isset($_POST['action']) && $_POST['action'] == 'related_files')
{
	//get the variables
	
	$files_arr = $_POST['files'];
	$id = $_POST['id'];
	$type = $_POST['type'];
	$total_files = count($files_arr);
	
	//the redir page
	
	$redir_page = $this_page."?id=".$id."&type=".$type."&title=".$title;
	
	//so we erase all the data related with the id
	
	$delete_query = $related->deleteRelatedBatch($type, $id);
	
	//now we insert the data on the DB if there's files selected
	
	if($total_files > 0)
	{
		foreach($files_arr as $file) {
			
			$insert_query = $related->insertRelated($type, $id, $file);
			
			if(!$insert_query)
				{
					$redir_page .= "&error=500";
					break;
				}
		}
	}
	
	redir($redir_page);
}

//the queries

$files_list = $files->getFilesList(); //the files list
$related_list = $related->getRelatedFiles($type, $id);//the related items

//the related files array

$files_arr = Array();

while($row_related = $related->fetchObject($related_list))
{
	array_push($files_arr, $row_related->file_id);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo PROJECT; ?> &raquo; Archivos relacionados &raquo; <?php echo $title; ?></title>

<link rel="stylesheet" href="../css/reset.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../css/pop-ups.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../css/jquery.asmselect.css" type="text/css" media="screen, projection">

<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery.ui.all.js"></script>
<script type="text/javascript" src="../js/jquery.asmselect.js"></script>

<script type="text/javascript">
<!--
$(document).ready(function() {
	$("#files").asmSelect({
		addItemTarget: 'bottom',
		animate: true,
		highlight: true,
		sortable: true
	});			
}); 
-->
</script>

</head>
<body>

<h3>Archivos relacionados &raquo; <?php echo $title; ?></h3>

<form action="<?php echo $this_page; ?>" method="post">

<label for="files">Listado de archivos:</label>

<select class="rounded" id="files" multiple="multiple" name="files[]" title="Seleccionar archivo(s)">
	<?php while($row_file = $files->fetchObject($files_list)){ ?>
	<option value="<?php echo $row_file->id; ?>" <?php echo getSelectedFromArray($files_arr, $row_file->id); ?>><?php echo $row_file->name; ?> (id:<?php echo $row_file->id; ?>)</option>
	<?php } ?>
</select>

<!-- hidden data -->
<input type="hidden" id="action" name="action" value="related_files" />
<input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
<input type="hidden" id="title" name="title" value="<?php echo $title; ?>" />
<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />

<input class="button" type="submit" name="submit" value="Guardar datos" />

</form>

</body>
</html>