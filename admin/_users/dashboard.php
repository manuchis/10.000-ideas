<?php

//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	include('../includes/messages_list.php');
	echo $messages_list['includes'][0];
	die;
}

//-=-=-=-= if it's and editor =-=-=-=-

if($row_user->roll == 'editor')
{
	echo '<p class="message">'.$messages_list['admin'][2].'</p>';
	die;
}

//-=-=-=-= load the data =-=-=-=-

require_once('classes/class.users.php');

//-=-=-=-= pages =-=-=-=-

$main_page = $this_page."?c=".$c;
$edit_page = $this_page."?c=".$c."&m=edit";
$pager_page = $main_page;

//-=-=-=-= the query =-=-=-=-

$webuser = new User();

$webuser_list = $webuser->getUserList();
$webuser_list_limited = $webuser->getUserListLimited($start_row, $items_per_page);

//-=-=-=-= search =-=-=-=-

if($q)
{
	$webuser_list = $webuser->searchUser($q);
	$webuser_list_limited = $webuser->SearchUserLimited($q, $start_row, $items_per_page);
	$pager_page = $this_page."?c=".$c."&q=".$q;
}

$total_items = $webuser->numRows($webuser_list);
?>
			<!-- Internas de las secciones -->
			<h3>Usuarios</h3>
			
			<!-- error and messages -->
			
			<?php getError($error_num, $custom_error); ?>
			
			<?php getMessage($message); ?>
			
			<!-- selections -->
			<div class="actions rounded">
				
				<a class="add-button left" href="#">Nuevo usuario</a>
				
				<form class="right rounded" id="search" name="search" action="<?php echo $this_page; ?>" method="get">
					<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
					Buscar: <input class="rounded required" type="text" id="q" name="q" value="<?php echo $q; ?>"> <input class="search" type="submit" />
				</form>
				
				<div class="clear"></div>
				
			</div>
			
			<!-- form con tabla de contenidos -->
			
			<form id="list" name="list" action="<?php echo $actions_page; ?>" method="post" enctype="multipart/form-data">
				<table class="tablesorter" id="sorter">
					<thead>
						<tr>
							<th class="check"><input name="allbox" type="checkbox" id="allbox" value="1" onclick="CheckAll();" /></th>
							<th>Nombre</th>
							<th>Votos</th>
							
							<th>Perfil</th>
							<th class="date">Alta</th>
							<th class="date">bloqueado</th>
							
						</tr>
					</thead>
					<tbody>
						<?php while($row_webuser = $webuser->fetchObject($webuser_list_limited)){ ?>
						<tr>
							<td><input class="required" type="checkbox" name="id[]" value="<?php echo $row_webuser->id; ?>" /></td>
							<td><a href="<?php echo $edit_page."&id=".$row_webuser->id; ?>" title="Editar <?php echo $row_webuser->nombre; ?> <?php echo $row_webuser->apellido; ?>"><?php echo $row_webuser->nombre; ?></a></td>
							<td> <?php echo $row_webuser->votos; ?></td>
							<td><a href="http://www.facebook.com/profile.php?id=<?php echo $row_webuser->usuario; ?>">Ver Facebook</a></td>
							
							<td><?php echo justDate($row_webuser->created); ?></td>
							<td class="date"><?php if($row_webuser->banned==1) {echo "Si";} else {echo "No";}; ?></td>
							
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
				<!-- saubmit data -->
				<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
				<input type="hidden" id="m" name="m" value="<?php echo $m; ?>" />
				<input type="hidden" id="action" name="action" value="delete_user" />
				
				<div class="actions rounded">
					
					<div class="left">
						<?php if($total_items > 0){ ?><input class="button" type="button" id="submit-list" name="submit-list" value="Eliminar" /><?php } ?>
					</div>
					
					<div id="pager">
						<?php getPager($total_items, $items_per_page, $page, $pager_page); ?>
					</div>
					
					<div class="clear"></div>
				</div>
				
			</form>