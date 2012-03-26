<?php
class Admin extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getAdminList()
	{
		$query = "SELECT * FROM admin ORDER BY nombre ASC";
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getAdminListLimited($start_row, $limit)
	{
		$query = "SELECT * FROM admin ORDER BY nombre ASC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getAdmin($id)
	{
		$query = sprintf("SELECT * FROM admin WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getAdminByMail($email)
	{
		$query = sprintf("SELECT * FROM admin WHERE email=%s",
						GetSQLValueString($email, "email"));
		$result = $this->getTrueFalse($query);
		return $result;
	}
	
	function searchAdmin($q)
	{
		$query = sprintf("SELECT * FROM admin WHERE name LIKE '%%%s%%' OR email LIKE '%%%s%%'", $q, $q);
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchAdminLimited($q, $start_row, $limit)
	{
		$query = sprintf("SELECT * FROM admin WHERE name LIKE '%%%s%%' OR email LIKE '%%%s%%' ORDER BY nombre ASC", $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newAdmin($name, $name2, $email, $password, $city, $date, $token)
	{
		$query = sprintf("INSERT INTO admin (nombre, apellido, email, password, ciudad, alta, modificacion, login, token) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($name, "text"),
						GetSQLValueString($name2, "text"),
						GetSQLValueString($email, "email"),
						GetSQLValueString($password, "text"),
						GetSQLValueString($city, "int"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($token, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editAdmin($name,$name2, $email, $password,$city, $date, $token, $id)
	{
		$query = sprintf("UPDATE admin SET nombre=%s, apellido=%s, email=%s, password=%s, ciudad=%s,modificacion=%s, token=%s WHERE id=%s",
						GetSQLValueString($name, "text"),
						GetSQLValueString($name2, "text"),
						GetSQLValueString($email, "email"),
						GetSQLValueString($password, "text"),
						GetSQLValueString($city, "int"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($token, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteAdmin($id)
	{
		$query = sprintf("DELETE FROM admin WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getCategorias()
	{
		$query = "SELECT * FROM categorias ORDER BY nombre DESC";
		$result = $this->genQuery($query);
		return $result;
	}
	function getCiudades()
	{
		$query = "SELECT * FROM ciudades ORDER BY nombre ASC";
		$result = $this->genQuery($query);
		return $result;
	}
}
?>