<?php
//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	echo "You can't load this page directly!";
	die;
}

//-=-=-=-= load the data =-=-=-=-

require_once('classes/class.dashboard.php');

//-=-=-=-= the query =-=-=-=-

$dashboard = new Dashboard();
$users_list = $dashboard->getUserListLimited(0, $items_per_page);
$ideas_list = $dashboard->getIdeasListLimited(0, $items_per_page);
$ciudad_data = $dashboard->getCiudades();
 while($row_ciudad = $dashboard->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais);
}
$categ_data = $dashboard->getCategorias();
 while($row_categ = $dashboard->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
}
?>
			<!-- Panel principal -->
			<h3>Panel principal</h3>
			

			
			<div class="info-widget rounded">
				<div class="header">Usuarios &rarr; <a href="<?php echo $this_page; ?>?c=users">ver todos</a></div>
				<div class="content-scroll">
					<table>
						<?php while($row_user = $dashboard->fetchObject($users_list)){ ?>
						<tr>
							<td><?php echo $row_user->nombre; ?></td>
							<td><a href="http://www.facebook.com/profile.php?id=<?php echo $row_user->fbid; ?>">Perfil de facebook</a></td>
							<td class="edit"><a href="">Ideas</a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
					<table class="tablesorter info-widget rounded" id="sorter">
						<thead>
						
						<tr>
							<th>Idea  &rarr; <a href="<?php echo $this_page; ?>?c=ideas">ver todas</a></th>
							<th>Ciudad</th>
							<th>Barrio</th>
							<th>Categoria</th>
							<th>Votos</th>
							<th>Usuario</th>
							<th>Aprobado</th>
							<th>Creado</th>
							<th class="edit"></th>
						</tr>
						</thead>
						<tbody>
						<?php while($row_ideas = $dashboard->fetchObject($ideas_list)){ ?>
						<tr>
							<td><?php echo utf8_decode($row_ideas->idea); ?></td>
							<td><?php echo utf8_decode($ciudades[$row_ideas->ciudad]['nombre']); ?></td>
							<td><?php echo utf8_decode($row_ideas->barrio); ?></td>
							<td><?php echo utf8_decode($categorias[$row_ideas->categoria]['nombre']); ?></td>
							<td><?php echo $row_ideas->votos; ?></td>
							<td><a href="http://www.facebook.com/profile.php?id=<?php echo $row_ideas->usuario; ?>"><?php echo $row_ideas->nombre; ?></a></td>
							<td><?php if($row_ideas->aprobado==1) {echo "Si";} else {echo "No";}; ?></td>
							
							<td><?php echo $row_ideas->creado; ?></td>
							
							<td class="edit"><a href="<?php echo "?c=ideas&m=edit&id=".$row_ideas->id; ?>">ver</a></td>
						</tr>
						<?php } ?>
						</tbody>
						
					</table>
			