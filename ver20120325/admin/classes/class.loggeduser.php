<?php
class LoggedUser extends mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getUserByToken($token)
	{
		$query = sprintf("SELECT * FROM admin WHERE token=%s",
					GetSQLValueString($token, "text"));
		$result = $this->getQueryOrFalse($query);
		return $result;
	}
	
	function lastLogin($user)
	{
		$login_query = sprintf("UPDATE admin SET login=NOW() WHERE id=%s",
						GetSQLValueString($user, "int")); 
		$result = $this->genQuery($login_query);
		return $result;
	}
}
?>