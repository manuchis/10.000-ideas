<?php
//================================================
//Clase para dialogo con la base de datos
//================================================

class mysql
{
	//===========================================================
	//variables
	//===========================================================
	var $host;			//the server host 90% of being localhost
	var $user;			//the username 
	var $password;		//the password
	var $db;			//the database to select
	//===========================================================
	//constructor
	//===========================================================
	function mysql()
	{
			$this->host = "DATABASE_SERVER";
			$this->user = "DATABASE_USER";
			$this->password = "DATABASE_PASSWORD";
			$this->db = "DATABASE_NAME";
	}

	//===========================================================
	//Se conecta a la bd y hace una consulta. Devuelve el query
	//===========================================================
	
	function genQuery($sql_query)
	{
		$link = mysql_connect($this->host, $this->user, $this->password);
		if(!$link) die("No se pudo conectar a la base: " . mysql_error());
			$sel_db = mysql_select_db($this->db, $link);
		
		if(!$sel_db) die("No se pudo usar la base de datos: " . mysql_error());
			$query = mysql_query($sql_query, $link);

		if(!$query) die("Error: " . mysql_error());
			mysql_close($link);

		return $query;
	}
	
	//===========================================================
	//genera una consulta y me devuelve el query
	//o false si no hay registros
	//===========================================================

	function getQueryOrFalse($sql_query)
	{
		$query = $this->genQuery($sql_query);
		if(mysql_num_rows($query)) return $query;
		else return false;
	}
	
	//===========================================================
	//Genera una consulta y me devuelve el array del registro
	//o false si no hay. Libera la memoria del query
	//===========================================================

	function getArrayOrFalse($sql_query)
	{
		$arr = array();
		$query = $this->genQuery($sql_query);
		if(mysql_num_rows($query)) $arr = mysql_fetch_array($query);
		else $arr = false;
		mysql_free_result($query);
		
		return $arr;
	}
	
	//===========================================================
	//genera una consulta y me devuelve true or false si hay
	//o no registros. Libera la memoria del query
	//===========================================================

	function getTrueFalse($sql_query)
	{
		$query = $this->genQuery($sql_query);
		if(mysql_num_rows($query)) $result = true;
		else $result = false;
		mysql_free_result($query);
		
		return $result;
	}
	//===========================================================
	//genera una consulta retornando el primer campo del
	//primer registros. Libera la memoria del query
	//===========================================================

	function getField($sql_query)
	{
		$query = $this->genQuery($sql_query);
		if(mysql_num_rows($query)) $resu = mysql_result($query, 0, 0);
		else $resu = false;
		mysql_free_result($query);

		return $resu;
	}
	
	//===========================================================
	//retorna el numero de campos de la consulta
	//===========================================================
	
	function numRows($sql_query)
	{
		$num = mysql_num_rows($sql_query);
		return $num;
	}
	
	//===========================================================
	//retorna el resultado como un objeto
	//===========================================================
	
	function fetchObject($result)
	{
		$row = mysql_fetch_object($result);
		return $row;
	}
	
	//===========================================================
	//retorna el resultado como un array indexado
	//===========================================================
	
	function fetchRow($result)
	{
		$row = mysql_fetch_row($result);
		return $row;
	}
	
	//===========================================================
	//retorna el resultado como un array asociativo
	//===========================================================
	
	function fetchArray($result)
	{
		$row = mysql_fetch_array($result);
		return $row;
	}
	
	//===========================================================
	//retorna el resultado como una matriz asociativa
	//===========================================================
	
	function fetchAssoc($result)
	{
		$row = mysql_fetch_assoc($result);
		return $row;
	}
}
?>