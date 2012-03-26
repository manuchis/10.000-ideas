<?php
//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	include('../includes/messages_list.php');
	echo $messages_list['includes'][0];
	die;
}

//-=-=-=-= load the data =-=-=-=-

require_once('classes/class.ideas.php');

//-=-=-=-= pages =-=-=-=-

$main_page = $this_page."?c=".$c;
$edit_page = $this_page."?c=".$c."&m=edit";
$pager_page = $main_page;

//-=-=-=-= content variables =-=-=-=-

$source = "menu";

//-=-=-=-= the query =-=-=-=-

$content = new Idea();

$content_list = $content->getContentList($source);
$content_list_limited = $content->getContentListLimited($start_row, $items_per_page, $source);


//-=-=-=-= search =-=-=-=-

if($q)
{
	$content_list = $content->searchContent($q, $source);
	$content_list_limited = $content->SearchContentLimited($q, $start_row, $items_per_page, $source);
	$pager_page = $this_page."?c=".$c."&q=".$q;
}

//-=-=-=-= status =-=-=-=-

if($status != '')
{
	$content_list = $content->getContentListByStatus($status, $source);
	$content_list_limited = $content->getContentListByStatusLimited($status, $start_row, $items_per_page, $source);
	$pager_page = $this_page."?c=.$c.&status=".$status;
}

$total_items = $content->numRows($content_list);
$ciudad_data = $content->getCiudades();
 while($row_ciudad = $content->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais);
}
$categ_data = $content->getCategorias();
 while($row_categ = $content->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
}
?>
			<!-- Internas de las secciones -->
			<h3>Ideas</h3>
			
			<!-- error and messages -->
			
			<?php getError($error_num, $custom_error); ?>
			
			<?php getMessage($message); ?>
			
			<!-- selections -->
				<div class="actions rounded">

								
				<form class="right rounded" id="search" name="search" action="<?php echo $this_page; ?>" method="get">
					<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
					Buscar: 
					<input class="rounded required" type="text" id="q" name="q" value="<?php echo $q; ?>" tabindex="21"> 
					<input class="search" type="submit" tabindex="22" />
				</form>
				
				<div class="clear"></div>
				
			</div>
			
			<!-- sorter -->
			<!-- <div id="type">
				<a href="<?php echo $main_page; ?>" title="Todos los contenidos">Todos los contenidos</a> | <a href="<?php echo $main_page; ?>&status=1" title="Publicados">Publicados</a> | <a href="<?php echo $main_page; ?>&status=0" title="No publicados">No publicados</a>
			</div> -->
			
			<!-- form con tabla de contenidos -->
			
			<form id="list" name="list" action="<?php echo $actions_page; ?>" method="post">
				<table class="tablesorter" id="sorter">
					<thead>
						<tr>
							<th class="check"><input name="allbox" type="checkbox" id="allbox" value="1" onclick="CheckAll();" /></th>
							<th>Idea</th>
							<th>Ubicación</th>
							<th>Barrio</th>
							<th>Ciudad</th>
							<th>Categoría</th>
							<th>User</th>
							<th class="date">Aprobado</th>
							<th class="date">Votos</th>
							<th class="date">Creado</th>
						</tr>
					</thead>
					<tbody>
						<?php while($row_content = $content->fetchObject($content_list_limited)){ ?>
						<tr>
							<td><input class="required" type="checkbox" name="id[]" value="<?php echo $row_content->id; ?>" /></td>
							<td><a href="<?php echo $edit_page."&id=".$row_content->id; ?>" title="Editar <?php echo $row_content->idea; ?>"><?php echo utf8_decode($row_content->idea); ?></a></td>
							<td><?php echo utf8_decode($row_content->ubicacion); ?></td>
							<td><?php echo utf8_decode($row_content->barrio); ?></td>
							<td><?php echo utf8_decode($ciudades[$row_content->ciudad]['nombre']); ?></td>
							<td><?php echo utf8_decode($categorias[$row_content->categoria]['nombre']); ?></td>
							<td><?php echo $row_content->nombre." (".$row_content->user.")"; ?></td>
							<td><?php if($row_content->aprobado == 1) echo "Si"; ?></td>
							<td><?php echo $row_content->votos; ?></td>
							<td><?php echo $row_content->creado; ?></td>
							
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
				<!-- saubmit data -->
				<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
				<input type="hidden" id="m" name="m" value="<?php echo $m; ?>" />
				<input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
				<input type="hidden" id="action" name="action" value="delete_ideas" />
				
				<div class="actions rounded">
					
					<div class="left">
						<?php if($total_items > 0){ ?><input class="button" type="button" id="submit-list" name="submit-list" value="Eliminar" tabindex="25" /><?php } ?>
					</div>
					
					<div id="pager">
						<?php getPager($total_items, $items_per_page, $page, $pager_page); ?>
					</div>
					
					<div class="clear"></div>
				</div>
				
			</form>
			