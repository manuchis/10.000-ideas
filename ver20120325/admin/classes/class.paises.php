<?php
class Pais extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getContentList($source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM paises";
		}else{
			$query = "SELECT * FROM paises";
		}
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListNoParent()
	{
			$query = "SELECT * FROM paises WHERE parent = 0";
	
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListLimited($start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM paises";
			$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
			$result = $this->genQuery($query_limit);
			return $result;
		}else{
			$query = "SELECT * FROM paises";
			$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
			$result = $this->genQuery($query_limit);
			return $result;
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContentListByStatus($status, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM paises",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM paises",
						GetSQLValueString($status, "text"));
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListByStatusLimited($status, $start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM paises",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM paises",
						GetSQLValueString($status, "text"));
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContent($id)
	{
		$query = sprintf("SELECT * FROM paises WHERE id =%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContent($q, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM paises WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM paises WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContentLimited($q, $start_row, $limit, $source)
	{
	if($source == "menu")
		{
			$query = sprintf("SELECT * FROM paises WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM paises WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newContent($nombre, $descripcion, $date)
	{
		$query = sprintf("INSERT INTO paises (nombre, descripcion, created) VALUES (%s, %s, %s)",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($descripcion, "text"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editContent($nombre, $descripcion, $id)
	{
		$query = sprintf("UPDATE paises SET nombre=%s, descripcion=%s WHERE id=%s",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($descripcion, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteContent($id)
	{
		$query = sprintf("DELETE FROM paises WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}	
}
?>