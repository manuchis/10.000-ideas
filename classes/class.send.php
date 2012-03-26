<?php
class Send extends Mysql
{
	//===========================================================
	//functions
	//===========================================================
		
	function newContent($usuario, $ciudad, $idea, $categoria, $barrio, $ubicacion, $date)
	{
		$query = sprintf("INSERT INTO ideas (user, ciudad, idea, categoria, barrio, ubicacion, creado) VALUES (%s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($usuario, "int"),
						GetSQLValueString($ciudad, "int"),
						GetSQLValueString($idea, "text"),
						GetSQLValueString($categoria, "int"),
						GetSQLValueString($barrio, "text"),
						GetSQLValueString($ubicacion, "text"),
						GetSQLValueString($date, "date"));
		$result = $this->genQuery($query);
		return $result;
	}
	function checkVote($usuario)
	{
		$query = sprintf("SELECT votos FROM usuarios WHERE id=%s",
						GetSQLValueString($usuario, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	function newVote($id, $votes)
	{
		$query = sprintf("UPDATE ideas SET votos =%s WHERE id=%s",
						GetSQLValueString($votes, "text"),
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
	}
	function newVoteUser($usuario, $votos)
	{
		$query = sprintf("UPDATE usuarios SET votos =%s WHERE id=%s",
						GetSQLValueString($votos, "text"),
						GetSQLValueString($usuario, "int"));
		$result = $this->genQuery($query);
	}
	
}
?>