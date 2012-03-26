<?php
//-=-=-=-= load the data =-=-=-=-
include('../includes/header.php');
require_once('../classes/class.images.php');
require_once("../classes/class.imageshandler.php");
	
//-=-=-=-= image variables =-=-=-=-
	
$size_arr = Array(
	Array("240", "", ""),
	Array("180", "", "_m"),
	Array("75", "75", "_t")
);
	
$images_folder = IMAGES_UPLOAD;
	
//-=-= variables used on the application =-=-

$this_page = $_SERVER['PHP_SELF'];

//-=-=-=-= if we have an action =-=-=-=-

if(isset($_POST['action']) && $_POST['action'] != '')
{	
	//get the variables
	
	$action = $_POST['action'];
	
	//set the redir pages
	
	$redir_page = "images.php";
	
	//start the admin class
	
	$image = new Images();
	
//-=-=-=-= new image =-=-=-=-
	
	if($action == 'new_image')
	{
	$image_file = $_FILES['image'];
		$name = $_POST['name'];
		$description = $_POST['description'];
		$tags = $_POST['tags'];
		$date = now();
		
		$continue = true;
		
		//upload the image
		
		$handle = new Upload($image_file);
		
		if ($handle->uploaded) {
			
			$image_server_name = date('U');
			$image_ext = $handle->file_src_name_ext;
			$image_type = $handle->file_src_mime;
			$handle->file_max_size = '1572864'; //1.5 Mb
			
			//here we have the loop
			
			$resized_count = count($size_arr);
			
			for($i = 0; $i < $resized_count; $i++)
			{
				$width = $size_arr[$i][0];
				$height = $size_arr[$i][1];
				$prefix = $size_arr[$i][2];
				
				$handle->image_resize = true;
				
				//check if we have a height
				
				if(!$height)
				{
					$handle->image_ratio_y = true;
				}else{
					$handle->image_ratio_crop = true;
					$handle->image_y = $height;
				}
					
				//image size control
				
	       		$handle->image_x = $width;
	        	if($width >= $handle->image_src_x)
	        		$handle->image_x = $handle->image_src_x;
	        		
				$handle->file_new_name_body = $image_server_name.$prefix;
	        	$handle->allowed = array('image/jpg', 'image/png', 'image/jpeg', 'image/pjpeg', 'image/x-png');
	        	$handle->Process($images_folder);
	        	
				if ($handle->processed) {
		            //everything was fine !
		        	if($i == $resized_count-1)
		        		$handle->Clean();
		        } else {
		            // one error occured
		            $continue = false;
		            $redir_page .= "&m=edit&error=101&cm=".$handle->error;
		            break;
		        }
			}
			
        	//save the data on the database
			if($continue)
			{
				$insert_query = $image->newImage($name, $image_server_name, $image_ext, $image_type, $description, $tags, $date);
				
				if(!$insert_query)
					$redir_page = $redir_page."m=edit&error=500";
			}
			
		}else{
			$redir_page .= "&m=edit&error=101&cm=".$messages_list['images'][0];
		}
	}
	
	redir($redir_page);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo PROJECT; ?> &raquo; Subir im&aacute;genes</title>

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
<!-- cms jquery ini -->
<script type="text/javascript" src="../js/jquery.cms.js"></script>
<!-- functions -->
<script type="text/javascript" src="../js/functions.js"></script>

</head>
<body>

<h3>Nueva imagen</h3>
				
<!-- error and messages -->
			
<?php getError($error_num, $custom_error); ?>
				
<?php getMessage($message); ?>
				
<form id="data" name="data" action="<?php echo $this_page; ?>" method="post" enctype="multipart/form-data">
						
	<ul>
		<li class="title"><label for="name">Nombre de la imagen</label></li>
		<li><input class="required rounded title" type="text" name="name" id="name" value="<?php echo $row_image->name; ?>" /></li>
							
		<li class="title"><label for="image">Archivo</label></li>
		
		<!-- archivo -->
		<li><small>Se aceptan archivos jpg, jpeg y png de 1024x768 pixels y 1.5Mb como peso maximo</small></li>
		<li><input class="required rounded file" type="file" id="image" name="image" /></li>
							
		<li class="title"><label for="description">Descripci&oacute;n</label></li>
		<li><textarea class="rounded" id="description" name="description"><?php echo $row_image->description; ?></textarea></li>
		
		<li class="title"><label for="tags">Tags</label> <small>separados por comas</small></li>
		<li><input class="rounded text" type="text" id="tags" name="tags" value="<?php echo $row_image->tags; ?>" /></li>
	</ul>
	
	<!-- hidden data -->
	<input type="hidden" name="action" id="action" value="new_image" />
						
<input class="button" type="submit" id="submit" name="submit" value="Guardar datos" />
						
</form>

</body>
</html>