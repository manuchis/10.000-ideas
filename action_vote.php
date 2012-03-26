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
	$errorcode;
	$redir_page = "index.php?";
	
	//start the class
	
	$content = new Send();
	
	//-=-=-=-= new content =-=-=-=-
	
	if($action == 'new_vote')
	{
		//get the variables
		$checked = FALSE;
		$votos = ""; 
		$usuario = $_POST['usuario'];
		$id = $_POST['id'];	
		$votes = $_POST['votes'];		
		$check_query = $content->checkVote($usuario); //checkea el usuario
			if($check_query){
				 while($row_check = $content->fetchObject($check_query)){ 
					 $votos = $row_check->votos; // declaracion de losvotos del usuario
				}
				$votados = explode(',', $votos);
				if (in_array($id, $votados)) { // chequea en el array si está el id de la idea
				    $checked = FALSE;
					$errorcode="650"; 
					$redir_page .= "&error=650"; //si ya votó o no existe usuario
				}else{
					$checked = TRUE;
						$votos .= ",".$id;
						$votes = $votes+1;
						$newvote_query = $content->newVote($id, $votes); //agrega el voto
						$newvoteuser_query = $content->newVoteUser($usuario, $votos); //actualiza el ususario
					//	if(!$newvote_query){ $redir_page .= "&error=500";$errorcode="500";}elseif(!$newvoteuser_query){	$redir_page .= "&error=500";$errorcode="500";}
				}
			}else{	$redir_page .= "&error=651";$errorcode="651"; }
						
	
	}
	
//redir($redir_page);
	
}else{
	redir("index.php?error=404");
}
echo $errorcode;
?>