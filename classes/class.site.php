<?php
class site extends mysql
{	
	/* contenidos */
	function getIdeas()
	{
		$query = "SELECT * FROM ideas WHERE aprobado='1' ORDER BY creado DESC";
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeabyId($id)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.id as id, ideas.votos as votos,ideas.categoria as categoria,  ideas.creado as creado, ideas.ubicacion as ubicacion,ideas.barrio as barrio, ideas.ciudad as ciudad ,usuarios.nombre as usuario, usuarios.fbid as fbid, usuarios.imagen as imagen FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.id=%s", GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeasLimited($start_row, $limit)
	{
		$query = "SELECT * FROM ideas WHERE aprobado='1' ORDER BY creado DESC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeasLimitedbyCity($start_row, $limit, $city)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s ORDER BY ideas.creado DESC", GetSQLValueString($city, "text"));
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeasLimitedbyCityVoted($start_row, $limit, $city)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s ORDER BY votos DESC", GetSQLValueString($city, "text"));
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeasLimitedbyCityAndCat($start_row, $limit, $city, $cat)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s AND categoria=%s ORDER BY ideas.creado DESC", GetSQLValueString($city, "text"), GetSQLValueString($cat, "text"));
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function getIdeasLimitedbyCityVotedAndCat($start_row, $limit, $city, $cat)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s AND categoria=%s ORDER BY votos DESC", GetSQLValueString($city, "text"), GetSQLValueString($cat, "text"));
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function searchIdeasLimitedbyCity($q,$start_row, $limit, $city)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s AND ideas.idea LIKE '%%%s%%'  OR ideas.ubicacion LIKE '%%%s%%'   ORDER BY ideas.creado DESC", GetSQLValueString($city, "text"), $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query);
		return $result;
	}
	function searchIdeasLimitedbyCityVoted($q, $start_row, $limit, $city)
	{
		$query = sprintf("SELECT ideas.idea as idea, ideas.ubicacion as ubicacion, ideas.ciudad as ciudad, ideas.id as id, ideas.votos as votos, ideas.creado as creado, ideas.categoria as categoria, ideas.barrio as barrio ,usuarios.nombre as usuario FROM ideas JOIN usuarios ON(ideas.user=usuarios.id) WHERE ideas.aprobado='1' AND ideas.ciudad=%s  AND ideas.idea LIKE '%%%s%%'  OR ideas.ubicacion LIKE '%%%s%%'  ORDER BY votos DESC", GetSQLValueString($city, "text"), $q, $q);
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
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
		$query = "SELECT ciudades.id, ciudades.nombre, ciudades.barrios, ciudades.pais as pais, paises.nombre as paisn FROM ciudades LEFT JOIN paises ON (ciudades.pais = paises.id) ORDER BY nombre ASC";
		$result = $this->genQuery($query);
		return $result;
	}
	function getMenuByLevel($l)
	{
		$query = sprintf("SELECT * FROM contents WHERE level=%s AND status='1' ORDER BY pos ASC",
						GetSQLValueString($l, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function checkUser($name, $id)
	{
		$query = sprintf("SELECT id FROM usuarios WHERE fbid=%s",
						GetSQLValueString($id, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getUser($id){
		$query = sprintf("SELECT * FROM usuarios WHERE id=%s",
						GetSQLValueString($id, "int"));
		$result = $this->genQuery($query);
		return $result;
	}
	function setUser($name, $fbid, $date){
			$query = sprintf("INSERT INTO usuarios (nombre, fbid, created) VALUES (%s, %s, %s)",
							GetSQLValueString($name, "text"),
							GetSQLValueString($fbid, "text"),
							GetSQLValueString($date, "date"));
			$result = $this->genQuery($query);
		return $result;
	}
	function getMantenimiento()
	{
		$query = "SELECT mantenimiento FROM mantenimiento";
		$result = $this->genQuery($query);
		return $result;
	}
	function searchContent($q)
	{
		$query = sprintf("SELECT * FROM ideas  WHERE aprobado='1' AND idea LIKE '%%%s%%' OR ubicacion LIKE '%%%s%%' ORDER BY creado DESC", $q, $q);
		$result = $this->genQuery($query);
		return $result;
	}
}
function arrayToSQLString($array)
{
   return "'".implode("','",$array)."'";
}
?>