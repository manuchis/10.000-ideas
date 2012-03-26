<?php
class Ciudad extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getContentList($source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM ciudades";
		}else{
			$query = "SELECT * FROM ciudades";
		}
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListNoParent()
	{
			$query = "SELECT * FROM ciudades WHERE parent = 0";
	
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListLimited($start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = "SELECT ciudades.id, ciudades.nombre, ciudades.pais as paisid, paises.nombre as pais, ciudades.created FROM ciudades LEFT JOIN paises ON (ciudades.pais = paises.id)";
			$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
			$result = $this->genQuery($query_limit);
			return $result;
		}else{
			$query = "SELECT * FROM ciudades";
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
			$query = sprintf("SELECT ciudades.id,ciudades.nombre, ciudades.pais as paisid, paises.nombre as pais, ciudades.created FROM ciudades  LEFT JOIN paises ON (ciudades.pais = paises.id)",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT ciudades.id,ciudades.nombre, ciudades.pais as paisid, paises.nombre as pais, ciudades.created FROM ciudades  LEFT JOIN paises ON (ciudades.pais = paises.id)",
						GetSQLValueString($status, "text"));
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListByStatusLimited($status, $start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ciudades",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM ciudades",
						GetSQLValueString($status, "text"));
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContent($id)
	{
		$query = sprintf("SELECT * FROM ciudades WHERE id =%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContent($q, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ciudades WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM ciudades WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContentLimited($q, $start_row, $limit, $source)
	{
	if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ciudades WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM ciudades WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newContent($nombre, $pais, $barrios, $date)
	{
		$query = sprintf("INSERT INTO ciudades (nombre, pais, barrios, created) VALUES (%s, %s, %s, %s)",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($pais, "int"),
						GetSQLValueString($barrios, "text"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editContent($nombre, $pais, $barrios, $id)
	{
		$query = sprintf("UPDATE ciudades SET nombre=%s, pais=%s, barrios=%s WHERE id=%s",
						GetSQLValueString($nombre, "text"),
						GetSQLValueString($pais, "int"),
						GetSQLValueString($barrios, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteContent($id)
	{
		$query = sprintf("DELETE FROM ciudades WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}	
	function getPaises()
	{
		$query = "SELECT * FROM paises ORDER BY nombre DESC";
		$result = $this->genQuery($query);
		return $result;
	}
}
?>