<?php
class Idea extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getContentList($source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM ideas";
		}else{
			$query = "SELECT * FROM ideas";
		}
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListNoParent()
	{
			$query = "SELECT * FROM ideas WHERE parent = 0";
	
		$result = $this->genQuery($query);
		return $result;
	}
	function getContentListLimited($start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = "SELECT ideas.id, ideas.categoria, ideas.ciudad, ideas.barrio, ideas.ubicacion, ideas.idea, ideas.creado, ideas.aprobado,ideas.votos, usuarios.fbid as usuario, usuarios.nombre as nombre, ideas.user FROM ideas LEFT JOIN usuarios ON (ideas.user = usuarios.id) ORDER BY ideas.creado DESC";
			$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
			$result = $this->genQuery($query_limit);
			return $result;
		}else{
			$query = "SELECT ideas.id, ideas.categoria, ideas.ciudad, ideas.barrio, ideas.ubicacion, ideas.idea, ideas.creado, ideas.aprobado,ideas.votos, usuarios.fbid as usuario, usuarios.nombre as nombre, ideas.user FROM ideas LEFT JOIN usuarios ON (ideas.user = usuarios.id) ORDER BY ideas.creado DESC";
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
			$query = sprintf("SELECT * FROM ideas",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM ideas",
						GetSQLValueString($status, "text"));
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListByStatusLimited($status, $start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ideas",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM ideas",
						GetSQLValueString($status, "text"));
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContent($id)
	{
		$query = sprintf("SELECT ideas.id, ideas.categoria, ideas.ciudad, ideas.barrio, ideas.ubicacion, ideas.idea, ideas.creado, ideas.aprobado,ideas.votos, usuarios.fbid as usuario, usuarios.nombre as nombre, ideas.user FROM ideas LEFT JOIN usuarios ON (ideas.user = usuarios.id) WHERE ideas.id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContent($q, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ideas WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM ideas WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContentLimited($q, $start_row, $limit, $source)
	{
	if($source == "menu")
		{
			$query = sprintf("SELECT * FROM ideas WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM ideas WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newContent($idea, $ubicacion, $barrio, $ciudad, $categoria, $aprobado, $date)
	{
		$query = sprintf("INSERT INTO ideas (idea, ubicacion, barrio, ciudad, categoria, aprobado, creado) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($idea, "text"),
						GetSQLValueString($ubicacion, "text"),
						GetSQLValueString($barrio, "text"),
						GetSQLValueString($ciudad, "int"),
						GetSQLValueString($categoria, "int"),
						GetSQLValueString($aprobado, "text"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editContent($idea, $ubicacion, $barrio, $ciudad, $categoria, $aprobado, $date, $id)
	{
		$query = sprintf("UPDATE ideas SET idea=%s, ubicacion=%s, barrio=%s, ciudad=%s, categoria=%s, aprobado=%s, modificado=%s WHERE id=%s",
						GetSQLValueString($idea, "text"),
						GetSQLValueString($ubicacion, "text"),
						GetSQLValueString($barrio, "text"),
						GetSQLValueString($ciudad, "int"),
						GetSQLValueString($categoria, "int"),
						GetSQLValueString($aprobado, "text"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteContent($id)
	{
		$query = sprintf("DELETE FROM ideas WHERE id=%s",
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