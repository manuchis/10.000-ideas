<?php 
include('../includes/header.php');
include('../classes/class.users.php');

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
	
	$user = new User();
	
	//-=-=-=-= new user =-=-=-=-
	
	if($action == 'new_user')
	{
		//get the variables
		
		$name = $_POST['name'];
		$last = $_POST['last'];
		$email = $_POST['email'];
		$accesos = $_POST['accesos'];
		
		//check if the user already exists in the database
		
		$check_email = $user->getUserByMail($email);
				
		if(!$check_email)
		{
			//the user and email data
			
			$from_name = FROM_NAME;
			$from_email = FROM_EMAIL;
			$host = HOST;
			$file = ROOT.TEXT_FOLDER."/welcome_user.txt";
			$subject = $messages_list['email'][0].PROJECT;
			$project= PROJECT;
			$url = PROJECT_URL;
			
			$password = rndPasswd(8);
			$password = md5($password);
			$token = md5($email);
			$date = now();
			
			//insert the data on the DB
			
			$insert_query = $user->newUser($name, $last, $email, $password, $accesos, $date, $token);
			
			if($insert_query)
			{
				$sended = sendUserData($from_name, $from_email, $host, $file, $subject, $name, $email, $password, $project, $url);
				
				if(!$sended)
					$redir_page .= "&m=edit&error=501";
				else
					$redir_page .= "&msg=".$messages_list['user'][3];
			}else{
				$redir_page .= "&m=edit&error=500";
			}
			
		}else{
			$redir_page .= "&m=edit&error=405";
		}
	}
	
	//-=-=-=-= edit user =-=-=-=-
	
	if($action == 'edit_user')
	{
		//get the variables
		
		$banned = $_POST['banned'];
		$date = now();
	    $id = $_POST['id'];
	    
	    $continue = true;
	    
		//retrieve the user data
		
		$user_data = $user->getUser($id);
		$row_user = $user->fetchObject($user_data);
		
			
		//check if we have to change the password
		if($continue)
		{
			$update_query = $user->editUser($banned, $id);
			
			$redir_page .= "&m=edit&id=".$id."&msg=".$messages_list['user'][1];
			
			if(!$update_query)
				$redir_page .= "&m=edit&id=".$id."&error=500";
		}
		else{
			$redir_page .= "&m=edit&id=".$id."&error=401";
		}
	
	}
	
	//-=-=-=-= delete user =-=-=-=-
	
	if($action == 'delete_user')
	{
		//get the variables
		
		$id_arr = $_POST['id'];
		$total_items = count($id_arr);
		$error_count = 0;
		
		//the loop to delete it
		
		foreach($id_arr as $id) {
			$delete_query = $user->deleteUser($id);
			
			if(!$delete_query)
			{
				$error_count++;
				break;
			}
		}
		
		if($error_count)
			$redir_page .= "&error=500";
		else
			$redir_page .= "&msg=".$messages_list['user'][2];
	}
	
	redir($redir_page);
	
}else{
	redir("../");
}
?>