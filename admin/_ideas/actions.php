<?php
include('../includes/header.php');
require_once('../classes/class.ideas.php');

//-=-=-=-= if we have an action =-=-=-=-

if(isset($_POST['action']) && $_POST['action'] != '')
{
	//get the variables
	
	$action = $_POST['action'];
	$c = $_POST['c'];
	$m = $_POST['m'];
	
	//set the redir pages
	
	$redir_page = "../index.php?c=".$c;
	
	//start the class
	
	$content = new Idea();
	
	//-=-=-=-= new content =-=-=-=-
	
	if($action == 'new_ideas')
	{
		//get the variables
		
		$idea = utf8_encode($_POST['idea']);
		$ubicacion = utf8_encode($_POST['ubicacion']);
		$barrio = utf8_encode($_POST['barrio']);
		$ciudad = $_POST['ciudad'];
		$categoria = $_POST['categoria'];
		$aprobado = $_POST['aprobado'];
		$date = now();
		
		$insert_query = $content->newContent($idea, $ubicacion, $barrio, $ciudad, $categoria, $aprobado, $date);
		
		if(!$insert_query)
			$redir_page .= "&m=edit&error=500";
	}
	
	//-=-=-=-= edit content =-=-=-=-
	
	if($action == 'edit_ideas')
	{
		//get the variables
		
		$idea = utf8_encode($_POST['idea']);
		$ubicacion = utf8_encode($_POST['ubicacion']);
		$barrio = utf8_encode($_POST['barrio']);
		$ciudad = $_POST['ciudad'];
		$categoria = $_POST['categoria'];
		$aprobado = $_POST['aprobado'];
		$id = $_POST['id'];
		$date = now();
		
		$redir_page .= "&m=edit&id=".$id."&msg=".$messages_list['content'][0];
		
		$edit_query = $content->editContent($idea, $ubicacion, $barrio, $ciudad, $categoria, $aprobado, $date, $id);
		
		if(!$edit_query)
			$redir_page .= "&m=edit&error=500";
	}
	
	//-=-=-=-= delete content =-=-=-=-
	
	if($action == 'delete_ideas')
	{
		//get the variables
		
		$id_arr = $_POST['id'];
		$total_items = count($id_arr);
		$error_count = 0;
		
		//the loop to delete it
		
		foreach($id_arr as $id) {
			$delete_query = $content->deleteContent($id);
			
			if(!$delete_query)
			{
				$error_count++;
				break;
			}
		}
		
		if($error_count)
			$redir_page .= "&error=500";
		else
			$redir_page .= "&status=".$status."&msg=".$messages_list['content'][1];
	}
	
	redir($redir_page);
	
}else{
	redir("../");
}
?>