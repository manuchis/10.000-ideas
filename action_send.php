<?php
include('includes/functions.php');
require_once('classes/class.mysql.php');
require_once('classes/class.send.php');

//-=-=-=-= if we have an action =-=-=-=-

if(isset($_POST['action']) && $_POST['action'] != '')
{
	//get the variables
	
	$action = $_POST['action'];
	
	//set the redir pages
	
	$redir_page = "index.php";
	
	//start the class
	
	$content = new Send();
	
	//-=-=-=-= new content =-=-=-=-
	
	if($action == 'new_idea')
	{
		//get the variables
		
		$usuario = $_POST['user'];
		$ciudad = $_POST['city'];
		if(!$ciudad){
			$errormsg = "Tienes que elegir una ciudad donde dejar tu idea.";
		}
		$idea = utf8_encode ($_POST['idea']);
		if(!$idea){
			$errormsg = "No has ingresado ninguna idea.";
		}
		$categoria = $_POST['category'];
		if(!$categoria){
			$errormsg = "Selecciona una categoría.";
		}
		$barrio = utf8_encode ($_POST['neighborhood']);
		if(!$barrio){
			$errormsg = "Falta localizar tu idea, ingresa un barrio de interés.";
		}
		$ubicacion = utf8_encode ($_POST['address']);
		if(!$ubicacion){
			$ubicacion = " ";
		}
		$date = now();
		
		if($errormsg){
			$redir_page .= "?msg=".urlencode($errormsg);
		}else{
			$insert_query = $content->newContent($usuario, $ciudad, $idea, $categoria, $barrio, $ubicacion, $date);
			$redir_page .= "?msg=". urlencode("Tu idea será analizada antes de publicarse, gracias por participar!");
			if(!$insert_query)
				$redir_page .= "&error=500";
		}
		
	}
	
	redir($redir_page);
	
}else{
	redir("index.php?error=404");
}
?>