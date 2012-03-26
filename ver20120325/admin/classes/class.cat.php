<?php
class Cat extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getContentList($source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM categorias";
		}else{
			$query = "SELECT * FROM categorias";
		}
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListNoParent()
	{
			$query = "SELECT * FROM categorias WHERE parent = 0";
	
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListLimited($start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM categorias";
			$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
			$result = $this->genQuery($query_limit);
			return $result;
		}else{
			$query = "SELECT * FROM categorias";
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
			$query = sprintf("SELECT * FROM categorias",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM categorias",
						GetSQLValueString($status, "text"));
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListByStatusLimited($status, $start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM categorias",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM categorias",
						GetSQLValueString($status, "text"));
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContent($id)
	{
		$query = sprintf("SELECT * FROM categorias WHERE id =%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContent($q, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM categorias WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM categorias WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContentLimited($q, $start_row, $limit, $source)
	{
	if($source == "menu")
		{
			$query = sprintf("SELECT * FROM categorias WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM categorias WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newContent($nombre, $descripcion, $color, $date)
	{
		$query = sprintf("INSERT INTO categorias (nombre, descripcion, color, created) VALUES (%s, %s, %s, %s)",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($descripcion, "text"),
						GetSQLValueString($color, "text"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editContent($nombre, $descripcion, $color, $id)
	{
		$query = sprintf("UPDATE categorias SET nombre=%s, descripcion=%s, color=%s  WHERE id=%s",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($descripcion, "text"),
						GetSQLValueString($color, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteContent($id)
	{
		$query = sprintf("DELETE FROM categorias WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}	
}
?>