<?php
class Dashboard extends Mysql
{
	function getUserListLimited($start_row, $limit)
	{
		$query = "SELECT * FROM usuarios ORDER BY nombre ASC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
		return $result;
	}
	function getIdeasListLimited($start_row, $limit)
	{
		$query = "SELECT ideas.id, ideas.categoria, ideas.ciudad, ideas.barrio, ideas.idea, ideas.creado, ideas.aprobado,ideas.votos, usuarios.fbid as usuario, usuarios.nombre as nombre, ideas.user FROM ideas LEFT JOIN usuarios ON (ideas.user = usuarios.id) ORDER BY ideas.creado DESC";
		$query_limit = sprintf("%s LIMIT %d, %d", $query, $start_row, $limit);
		$result = $this->genQuery($query_limit);
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