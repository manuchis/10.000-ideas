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

require_once('classes/class.admin.php');

//-=-=-=-= pages =-=-=-=-

$main_page = $this_page."?c=".$c;
$edit_page = $this_page."?c=".$c."&m=edit";
$pager_page = $main_page;
$message = $_GET['msg'];
//-=-=-=-= content variables =-=-=-=-

$source = "menu";

//-=-=-=-= the query =-=-=-=-

$webuser = new Admin();

$webuser_list = $webuser->getAdminList();
$webuser_list_limited = $webuser->getAdminListLimited($start_row, $items_per_page);

//-=-=-=-= search =-=-=-=-


//-=-=-=-= status =-=-=-=-


?>
			<!-- Internas de las secciones -->
			<h3>Administradores</h3>
			
			<!-- error and messages -->
			
			<?php getError($error_num, $custom_error); ?>
			
			<?php getMessage($message); ?>
			
			<!-- selections -->
			<div class="actions rounded">
			
				<a class="add-button left" href="<?php echo $edit_page; ?>">Nuevo administrador</a>
				
				<div class="clear"></div>
				
			</div>
	
			<!-- form con tabla de contenidos -->
			
			<form id="list" name="list" action="<?php echo $actions_page; ?>" method="post">
				<table class="tablesorter" id="sorter">
					<thead>
						<tr>
							<th class="check"><input name="allbox" type="checkbox" id="allbox" value="1" onclick="CheckAll();" /></th>
							<th>Nombre</th>
							<th>Email</th>
							<th>Ciudad</th>
							<th class="date">Alta</th>
							<th class="date">Login</th>
						</tr>
					</thead>
					<tbody>
						<?php while($row_webuser = $webuser->fetchObject($webuser_list_limited)){ ?>
						<tr>
							<td><input class="required" type="checkbox" name="id[]" value="<?php echo $row_webuser->id; ?>" /></td>
							<td><a href="<?php echo $edit_page."&id=".$row_webuser->id; ?>" title="Editar <?php echo $row_webuser->nombre; ?> <?php echo $row_webuser->apellido; ?>"><?php echo $row_webuser->nombre; ?> <?php echo $row_webuser->apellido; ?></a></td>
							<td><a href="mailto:<?php echo $row_webuser->email; ?>" title="Enviar mail a <?php echo $row_webuser->email; ?>"><?php echo $row_webuser->email; ?></a></td>
							<td><?php echo $row_webuser->ciudad; ?></td>
							<td><?php echo justDate($row_webuser->alta); ?></td>
							<td><?php echo justDate($row_webuser->login); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
				<!-- saubmit data -->
				<input type="hidden" id="c" name="c" value="<?php echo $c; ?>" />
				<input type="hidden" id="m" name="m" value="<?php echo $m; ?>" />
				<input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
				<input type="hidden" id="action" name="action" value="delete_admin" />
				
				
				<div class="actions rounded">
					
						<div class="left">
							<input class="button" type="button" id="submit-list" name="submit-list" value="Eliminar" tabindex="25" />
						</div>
						
					<div id="pager">
						<?php getPager($total_items, $items_per_page, $page, $pager_page); ?>
					</div>
					
					<div class="clear"></div>
				</div>
				
			</form>	