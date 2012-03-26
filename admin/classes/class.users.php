<?php
class User extends Mysql
{	
	//===========================================================
	//functions
	//===========================================================
	
	function getUserList()
	{
		$query = "SELECT * FROM usuarios ORDER BY nombre ASC";
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getUserListLimited($start_row, $limit)
	{
		$query = "SELECT * FROM usuarios ORDER BY nombre ASC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getUser($id)
	{
		$query = sprintf("SELECT * FROM usuarios WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getUserByMail($email)
	{
		$query = sprintf("SELECT * FROM usuarios WHERE email=%s",
						GetSQLValueString($email, "email"));
		$result = $this->getTrueFalse($query);
		return $result;
	}
	
	function searchUser($q)
	{
		$query = sprintf("SELECT * FROM usuarios WHERE nombre LIKE '%%%s%%' OR last LIKE '%%%s%%' OR email LIKE '%%%s%%'", $q, $q, $q);
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchUserLimited($q, $start_row, $limit)
	{
		$query = sprintf("SELECT * FROM usuarios WHERE nombre LIKE '%%%s%%' OR last LIKE '%%%s%%' OR email LIKE '%%%s%%' ORDER BY nombre ASC", $q, $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	function editUser($banned, $id)
	{
		$query = sprintf("UPDATE usuarios SET banned=%s WHERE id=%s",
						GetSQLValueString($banned, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteUser($id)
	{
		$query = sprintf("DELETE FROM usuarios WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
}

?>