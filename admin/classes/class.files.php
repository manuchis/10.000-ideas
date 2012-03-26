<?php
class Files extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
	
	function getFilesList(){
		$query = "SELECT * FROM files ORDER by created ASC";
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getFilesListLimited($start_row, $limit){
		$query = "SELECT * FROM files ORDER by name ASC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function getFile($id){
		$query = sprintf("SELECT * FROM files WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function searchFiles($q){
		$query = sprintf("SELECT * FROM files WHERE name LIKE '%%%s%%' OR description LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER by name ASC", $q, $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function searchFilesLimited($q){
		$query = sprintf("SELECT * FROM files WHERE name LIKE '%%%s%%' OR description LIKE '%%%s%%' OR tags LIKE '%%%s%%' ORDER by name ASC", $q, $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	
	function newFile($name, $file, $ext, $type, $description, $tags, $date){
		$query = sprintf("INSERT INTO files (name, file, ext, type, description, tags, created, modified) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($name, "text"),
						GetSQLValueString($file, "text"),
						GetSQLValueString($ext, "text"),
						GetSQLValueString($type, "text"),
						GetSQLValueString($description, "text"),
						GetSQLValueString($tags, "text"),
						GetSQLValueString($date, "text"),
						GetSQLValueString($date, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function editFile($name, $file, $ext, $type, $description, $tags, $date, $id){
		$query = sprintf("UPDATE files SET name=%s, file=%s, ext=%s, type=%s, description=%s, tags=%s, modified=%s WHERE id=%s",
						GetSQLValueString($name, "text"),
						GetSQLValueString($file, "text"),
						GetSQLValueString($ext, "text"),
						GetSQLValueString($type, "text"),
						GetSQLValueString($description, "text"),
						GetSQLValueString($tags, "text"),
						GetSQLValueString($date, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteFile($id){
		$query = sprintf("DELETE FROM files WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;	
	}
}
?>