<?php
class Access extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getProductListNoParent()
	{
			$query = "SELECT * FROM productos WHERE parent=0";
			$result = $this->genQuery($query);
			return $result;
	}
	function getSubProductList($id)
	{
			$query = "SELECT * FROM productos WHERE parent= $id";
			$result = $this->genQuery($query);
			return $result;
	}
	function getAccessByUser($user_id)
	{
			$query = "SELECT * FROM permisos WHERE user_id= $user_id";
			$result = $this->genQuery($query);
			return $result;
	}
	function getAccessByUserAndProduct($user_id, $pd)
	{
			$query = "SELECT * FROM permisos WHERE user_id= $user_id AND product_id=$pd";
			$result = $this->genQuery($query);
			return $result;
	}
	
	function getContentList($source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM permisos";
		}else{
			$query = "SELECT * FROM permisos";
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListLimited($start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = "SELECT * FROM permisos";
		}else{
			$query = "SELECT * FROM permisos";
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContentListByStatus($status, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM permisos",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM permisos",
						GetSQLValueString($status, "text"));
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getContentListByStatusLimited($status, $start_row, $limit, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM permisos",
						GetSQLValueString($status, "text"));
		}else{
			$query = sprintf("SELECT * FROM permisos",
						GetSQLValueString($status, "text"));
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getContent($id)
	{
		$query = sprintf("SELECT * FROM permisos WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContent($q, $source)
	{
		if($source == "menu")
		{
			$query = sprintf("SELECT * FROM permisos WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM permisos WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchContentLimited($q, $start_row, $limit, $source)
	{
	if($source == "menu")
		{
			$query = sprintf("SELECT * FROM permisos WHERE level='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}else{
			$query = sprintf("SELECT * FROM permisos WHERE level!='0' AND title LIKE '%%%s%%' OR content LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER BY pos ASC", $q, $q, $q);
		}
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	function addAccess($user,$product)
	{
		$query = sprintf("INSERT INTO permisos (user_id,product_id) VALUES (%s, %s)",
						GetSQLValueString($user, "text"),
						GetSQLValueString($product, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function removeAccess($user,$product)
	{
		$query = sprintf("DELETE FROM permisos WHERE user_id=%s AND product_id=%s",
						GetSQLValueString($user, "text"), GetSQLValueString($product, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function newContent($title, $permisos, $author, $pos, $status, $level, $date)
	{
		$query = sprintf("INSERT INTO permisos (title, content, author, pos, status, level, created, modified) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($title, "text"),
						GetSQLValueString($permisos, "text"),
						GetSQLValueString($author, "text"),
						GetSQLValueString($pos, "int"),
						GetSQLValueString($status, "text"),
						GetSQLValueString($level, "text"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editContent($title, $permisos, $author, $pos, $status, $date, $id)
	{
		$query = sprintf("UPDATE permisos SET title=%s, content=%s, author=%s, pos=%s, status=%s, modified=%s WHERE id=%s",
						GetSQLValueString($title, "text"),
						GetSQLValueString($permisos, "text"),
						GetSQLValueString($author, "text"),
						GetSQLValueString($pos, "int"),
						GetSQLValueString($status, "text"),
						GetSQLValueString($date, "date"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteContent($id)
	{
		$query = sprintf("DELETE FROM permisos WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}	
}
?>