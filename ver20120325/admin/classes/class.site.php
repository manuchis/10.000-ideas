<?php
class site extends mysql
{	
	function getProducstbyUser($user_id){
			$query = "SELECT * FROM permisos JOIN productos ON(permisos.product_id = productos.id) WHERE permisos.user_id = $user_id AND productos.parent = 0 ORDER BY productos.nombre ASC";
			$result = $this->genQuery($query);
			return $result;
	}
	function getSubservicebyUser($pd, $pc, $user){
			$query = sprintf("SELECT productos.nombre_corto, productos.nombre , productos.codigo, productos.id FROM permisos JOIN productos ON(permisos.product_id = productos.id) WHERE permisos.user_id = %s AND productos.parent = %s ORDER BY productos.nombre ASC",
						GetSQLValueString($user, "int"),
						GetSQLValueString($pc, "text"));
			$result = $this->genQuery($query);
			return $result;
	}
	function getProductData($pd){
		$query = sprintf("SELECT * FROM productos WHERE nombre_corto=%s",
					GetSQLValueString($pd, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
/*	function getSubserviceList($pd, $user){
		$pd = "rep_". $pd;
		$query = "SELECT productos.nombre, productos.codigo,count(*) as count_subservicio FROM permisos JOIN productos ON(permisos.product_id = $pd.subservicio) WHERE permisos.user_id = $user GROUP BY subservicio ASC";
		$result = $this->genQuery($query);
		return $result;
	} */
	function getSubservice($pd, $sbp){
		$pd = "rep_". $pd;
		$query = sprintf("SELECT * FROM $pd WHERE subservicio=%s GROUP BY fecha ASC",
					GetSQLValueString($sbp, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getDeltaTableLimited($pd){
		$pd = "rep_". $pd;
		$query = "SELECT *,SUM(plata_exitosos) as sum_plata_exitosos, SUM(plata_exitosos_ellos) as sum_plata_exitosos_ellos,SUM(plata_con_cobro) as sum_plata_con_cobro,SUM(plata_con_cobro_ellos) as sum_plata_con_cobro_ellos,SUM(cobrados_exitosos) as sum_cobrados_exitosos, SUM(cobrados_exitosos_ellos) as sum_cobrados_exitosos_ellos,SUM(clientes_con_cobro) as sum_clientes_con_cobro,SUM(clientes_con_cobro_ellos) as sum_clientes_con_cobro_ellos  FROM $pd GROUP BY fecha ORDER BY fecha ASC LIMIT 30";
		$result = $this->genQuery($query);
		return $result;
	}
	function getDeltaTableGroupedLimited($pd, $user){
		$pd = "rep_". $pd;
		$query = sprintf("SELECT tablaprod.*,SUM(tablaprod.plata_exitosos) as sum_plata_exitosos, SUM(tablaprod.plata_exitosos_ellos) as sum_plata_exitosos_ellos,SUM(tablaprod.plata_con_cobro) as sum_plata_con_cobro,SUM(tablaprod.plata_con_cobro_ellos) as sum_plata_con_cobro_ellos,SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_exitosos_ellos) as sum_cobrados_exitosos_ellos,SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro,SUM(tablaprod.clientes_con_cobro_ellos) as sum_clientes_con_cobro_ellos FROM $pd as tablaprod JOIN permisos ON (tablaprod.subservicio =permisos.product_id) WHERE permisos.user_id = %s GROUP BY fecha ORDER BY fecha ASC LIMIT 30",
					GetSQLValueString($user, "text"));
		$result = $this->genQuery($query);
		return $result;
	}

	function getDeltaTablebyDateGrouped($pd,$startdate,$enddate, $user){
		$pd = "rep_". $pd;
			$query = sprintf("SELECT tablaprod.*,SUM(tablaprod.plata_exitosos) as sum_plata_exitosos, SUM(tablaprod.plata_exitosos_ellos) as sum_plata_exitosos_ellos,SUM(tablaprod.plata_con_cobro) as sum_plata_con_cobro,SUM(tablaprod.plata_con_cobro_ellos) as sum_plata_con_cobro_ellos,SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_exitosos_ellos) as sum_cobrados_exitosos_ellos,SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro,SUM(tablaprod.clientes_con_cobro_ellos) as sum_clientes_con_cobro_ellos FROM $pd as tablaprod JOIN permisos ON (tablaprod.subservicio =permisos.product_id) WHERE permisos.user_id = %s  AND fecha BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY fecha ORDER BY fecha ASC", 
						GetSQLValueString($user, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getDeltaTableGroupedLimitedspd($pd, $spd){
		$pd = "rep_". $pd;
		$query = sprintf("SELECT tablaprod.*,SUM(tablaprod.plata_exitosos) as sum_plata_exitosos, SUM(tablaprod.plata_exitosos_ellos) as sum_plata_exitosos_ellos,SUM(tablaprod.plata_con_cobro) as sum_plata_con_cobro,SUM(tablaprod.plata_con_cobro_ellos) as sum_plata_con_cobro_ellos,SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_exitosos_ellos) as sum_cobrados_exitosos_ellos,SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro,SUM(tablaprod.clientes_con_cobro_ellos) as sum_clientes_con_cobro_ellos FROM $pd as tablaprod JOIN productos ON (tablaprod.subservicio =productos.id) WHERE productos.nombre_corto = %s GROUP BY fecha ORDER BY fecha ASC LIMIT 30",
					GetSQLValueString($spd, "text"));
		$result = $this->genQuery($query);
		return $result;
	}

	function getDeltaTablebyDateGroupedspd($pd,$startdate,$enddate, $spd){
		$pd = "rep_". $pd;
			$query = sprintf("SELECT tablaprod.*,SUM(tablaprod.plata_exitosos) as sum_plata_exitosos, SUM(tablaprod.plata_exitosos_ellos) as sum_plata_exitosos_ellos,SUM(tablaprod.plata_con_cobro) as sum_plata_con_cobro,SUM(tablaprod.plata_con_cobro_ellos) as sum_plata_con_cobro_ellos,SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_exitosos_ellos) as sum_cobrados_exitosos_ellos,SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro,SUM(tablaprod.clientes_con_cobro_ellos) as sum_clientes_con_cobro_ellos FROM $pd as tablaprod JOIN productos ON (tablaprod.subservicio =productos.id) WHERE productos.nombre_corto = %s  AND fecha BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY fecha ORDER BY fecha ASC",
						GetSQLValueString($spd, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getTortaLimited($pd, $user){
		$pd = "rep_". $pd;
		$query = sprintf("SELECT SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro, SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_punt_exitosos) as sum_cobrados_punt_exitosos FROM $pd as tablaprod JOIN permisos ON (tablaprod.subservicio =permisos.product_id) WHERE permisos.user_id = %s GROUP BY fecha ORDER BY fecha ASC LIMIT 30", 	GetSQLValueString($user, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getTorta($pd,$startdate,$enddate, $user){
		$pd = "rep_". $pd;
			$query = sprintf("SELECT SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro, SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_punt_exitosos) as sum_cobrados_punt_exitosos FROM $pd as tablaprod JOIN permisos ON (tablaprod.subservicio =permisos.product_id) WHERE permisos.user_id = %s  AND productos.fecha BETWEEN '".$startdate."' AND '".$enddate."'  GROUP BY fecha ORDER BY fecha ASC", 	GetSQLValueString($user, "text"));
			$result = $this->genQuery($query);
		return $result;
	}
	function getTortaLimitedspd($pd, $spd){
		$pd = "rep_". $pd;
		$query = sprintf("SELECT SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro, SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_punt_exitosos) as sum_cobrados_punt_exitosos FROM $pd as tablaprod JOIN productos ON (tablaprod.subservicio =productos.id) WHERE productos.nombre_corto = %s  GROUP BY fecha ORDER BY fecha ASC LIMIT 30", 	GetSQLValueString($spd, "text"));
		$result = $this->genQuery($query);
		return $result;
	}
	function getTortaspd($pd,$startdate,$enddate, $spd){
		$pd = "rep_". $pd;
			$query = sprintf("SELECT SUM(tablaprod.clientes_con_cobro) as sum_clientes_con_cobro, SUM(tablaprod.cobrados_exitosos) as sum_cobrados_exitosos, SUM(tablaprod.cobrados_punt_exitosos) as sum_cobrados_punt_exitosos FROM $pd as tablaprod JOIN productos ON (tablaprod.subservicio =productos.id) WHERE productos.nombre_corto = %s  AND productos.fecha BETWEEN '".$startdate."' AND '".$enddate."'  GROUP BY fecha ORDER BY fecha ASC", 	GetSQLValueString($spd, "text"));
			$result = $this->genQuery($query);
		return $result;
	}
	function uploadReport($csv_file_data){
		$query = "INSERT INTO reportes(persona, fechasug, fechafinal, producto, subproducto, resultado, costo, modified) VALUES";
		$today = now();
		
		 foreach( $csv_file_data as $val ):
			$subpr = explode("_", $val[3]);
			$query .= "('".$val[0]."','".$val[1]."','".$val[2]."','".$subpr[0]."','".$subpr[1]."','".$val[4]."','".$val[5]."','".$today."'),";
		 endforeach;
			$lisquery = substr($query, 0, -1);
			$lisquery = $lisquery.";";
			$result = $this->genQuery($lisquery);
			return $result;
	}
}
function arrayToSQLString($array)
{
   return "'".implode("','",$array)."'";
}

?>