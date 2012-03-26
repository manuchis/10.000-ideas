<?php
class Login extends mysql
{	
	//===========================================================
	//functions
	//===========================================================
	
	function loginUser($username, $password)
	{
		$query = sprintf("SELECT * FROM admin WHERE email=%s AND password=%s",
						GetSQLValueString($username, "email"),
						GetSQLValueString($password, "text")); 
		$result = $this->getTrueFalse($query);
		return $result;
	}
	
	function lastLogin($user)
	{
		$login_query = sprintf("UPDATE admin SET login=NOW() WHERE id=%s",
						GetSQLValueString($user, "int")); 
		$result = $this->genQuery($login_query);
		return $result;
	}
	
	function changePassword($user_id, $password)
	{
		$password_query = sprintf("UPDATE admin SET password=%s WHERE id=%s",
						GetSQLValueString($password, "text"),
						GetSQLValueString($user_id, "int")); 
		$result = $this->genQuery($password_query);
		return $result;
	}
	
	function access($action,$limit)
    {
        $this->cleanout();
    	$ip = gethostbyname($_SERVER['REMOTE_ADDR']);
    	$amount_query = sprintf("SELECT * FROM  protects WHERE ip='%s' AND  action='%s'", $ip, $action);
        $result = $this->genQuery($amount_query);
        $amount = $this->numRows($result);
        if ($amount < $limit)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
	
	function fail($action,$expire)
    {
    	$ip = gethostbyname($_SERVER['REMOTE_ADDR']);
        $fail_query = "INSERT INTO protects (ip, action, expire) VALUES ('$ip', '$action', DATE_ADD(NOW(), INTERVAL $expire MINUTE))";
        $result = $this->genQuery($fail_query);
		return $result;
    }
	
	function cleanout()
    {
        $clean_query = "DELETE FROM protects WHERE expire<=NOW();";
        $result = $this->genQuery($clean_query);
		return $result;
    }
    
	function getUser($email)
	{
		$query = sprintf("SELECT * FROM admin WHERE email=%s",
					GetSQLValueString($email, "email"));
		$result = $this->genQuery($query);
		return $result;
	}
}
?>