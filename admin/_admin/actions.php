<?php
include('../includes/header.php');
require_once('../classes/class.admin.php');

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
	
	$admin = new Admin();
	
	//-=-=-=-= new admin =-=-=-=-
	
	if($action == 'new_admin')
	{
		//get the variables
		
		$name = $_POST['nombre'];
		$name2 = $_POST['apellido'];
		$email = $_POST['email'];
		$city = $_POST['ciudad'];
		
		//check if the user already exists in the database
		
		$check_email = $admin->getAdminByMail($email);
				
		if(!$check_email)
		{
			//the user and email data
			
			$from_name = FROM_NAME;
			$from_email = FROM_EMAIL;
			$host = HOST;
			$file = ROOT.TEXT_FOLDER."/welcome.txt";
			$subject = $messages_list['email'][0].PROJECT;
			$project= PROJECT;
			$url = PROJECT_URL;
			
			$password = rndPasswd(8);
			$pswd_md5 = md5($password);
			$token = md5($email);
			$date = now();
			
			//insert the data on the DB
			
			$insert_query = $admin->newAdmin($name, $name2, $email, $pswd_md5, $city, $date, $token);
			
			if($insert_query)
			{
				$sended = sendUserData($from_name, $from_email, $host, $file, $subject, $name, $email, $password, $project, $url);
				
				if(!$sended)
					$redir_page .= "&m=edit&error=501";
			}else{
				$redir_page .= "&m=edit&error=500";
			}
			
		}else{
			$redir_page .= "&m=edit&error=405";
		}
	}
	
	//-=-=-=-= edit admin =-=-=-=-
	
	if($action == 'edit_admin')
	{
		//get the variables
		
			$name = $_POST['nombre'];
			$name2 = $_POST['apellido'];
			$email = $_POST['email'];
			$city = $_POST['ciudad'];
	    $password = $_POST['psw'];
	    $new_password = $_POST['psw_n'];
	    $token = md5($email);
		$date = now();
	    $id = $_POST['id'];
	    
	    $continue = true;
		
		//check if the password is correct
		
		$admin_data = $admin->getAdmin($id);
		$row_admin = $admin->fetchObject($admin_data);
		
		$db_pass = $row_admin->password;
		$db_email = $row_admin->email;
		
		if(md5($password) == $db_pass)
		{
			//check if we have a new password
			
			if($new_password)
				$password = md5($new_password);
			else
				$password = md5($password);
			
			//check if we have a new email
			
			if($db_email != $email)
			{
				//verify if there's another account registered with this email
				
				$check_email = $admin->getAdminByMail($email);
				
				if($check_email)
				{
					$redir_page .= "&m=edit&id=".$id."&error=405";
					$continue = false;
				}
			}
			
			//update the admin data
			
			if($continue)
			{
				$update_query = $admin->editAdmin($name, $name2, $email, $password, $city, $date, $token, $id);
				
				$redir_page .= "&m=edit&id=".$id."&msg=".$messages_list['admin'][0];
				
				if(!$update_query)
					$redir_page .= "&m=edit&id=".$id."&error=500";
			}
			
		}else{
			$redir_page .= "&m=edit&id=".$id."&error=401";
		}
	}
	
	//-=-=-=-= delete admin =-=-=-=-
	
	if($action == 'delete_admin')
	{
		//get the variables
		
		$id_arr = $_POST['id'];
		$total_items = count($id_arr);
		$error_count = 0;
		
		//the loop to delete it
		
		foreach($id_arr as $id) {
			$delete_query = $admin->deleteAdmin($id);
			
			if(!$delete_query)
			{
				$error_count++;
				break;
			}
		}
		
		if($error_count)
			$redir_page .= "&error=500";
		else
			$redir_page .= "&msg=".$messages_list['admin'][1];
	}
	
	redir($redir_page);
	
	
}else{
	redir("../");
}
?>