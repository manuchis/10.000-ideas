<?php
class Related extends mysql
{	
	//===========================================================
	//functions
	//===========================================================
	
	function getRelatedFiles($type, $id)
	{
		$query = sprintf("SELECT * FROM files_rel WHERE type=%s AND content_id=%s",
						GetSQLValueString($type, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function deleteRelatedBatch($type, $id)
	{
		$query = sprintf("DELETE FROM files_rel WHERE type=%s AND content_id=%s",
						GetSQLValueString($type, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function insertRelated($type, $content_id, $file_id)
	{
		$query = sprintf("INSERT INTO files_rel (type, content_id, file_id) VALUES (%s, %s, %s)",
						GetSQLValueString($type, "text"),
						GetSQLValueString($content_id, "int"),
						GetSQLValueString($file_id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	
	function getTotalFiles($type, $id)
	{
		$query = sprintf("SELECT COUNT(*) AS total_files FROM files_rel WHERE type=%s AND content_id=%s",
						GetSQLValueString($type, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
}
?>