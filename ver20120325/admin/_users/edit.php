<?php
//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	include('includes/messages_list.php');
	echo $messages_list['includes'][0];
	die;
}

//-=-=-=-= if it's and editor =-=-=-=-

if($row_user->roll == 'editor')
{
	echo '<p class="message">'.$messages_list['admin'][0].'</p>';
	die;
}

//-=-=-=-= load the data =-=-=-=-

require_once('classes/class.users.php');

$webuser = new User();

$webuser_data = $webuser->getUser($id);
$row_webuser = $webuser->fetchObject($webuser_data);
?>
			<!-- form de edicion de contenidos -->
			
			<div id="edit">
				<h3><?php echo $page_title; ?> usuario</h3>
				
				<!-- error and messages -->
			
				<?php getError($error_num, $custom_error); ?>
				
				<?php getMessage($message); ?>
				
				<!-- form -->
				
				<form id="user" name="user" action="<?php echo $actions_page; ?>" method="post">
					<div class="left">
						
						<ul>
							<li class="title"><label for="name">Nombre</label></li>
							<li><?php echo $row_webuser->nombre; ?></li>
							
							<?php if($row_webuser->fbid) {?><li><img src="https://graph.facebook.com/<?php echo $row_webuser->fbid; ?>/picture" class="avatar" height="50px" width="50px" /></li><?php } ?>
							
							<li class="title"><label for="email">Perfil</label></li>
							<li><?php if($row_webuser->fbid) {?><a href="http://www.facebook.com/profile.php?id=<?php echo $row_webuser->usuario; ?>">Ver Facebook</a><?php } else {echo"no tiene perfil relacionado"; } ?></li>
							
							
							
						</ul>
						
						<!-- hidden data -->
						<input type="hidden" name="c" id="c" value="<?php echo $c; ?>" />
						<input type="hidden" name="m" id="m" value="<?php echo $m; ?>" />
						<input type="hidden" name="action" id="action" value="<?php echo $action; ?>_user" />
						<input type="hidden" name="id" id="id" value="<?php echo $row_webuser->id; ?>" />
						
						<input class="button" type="submit" id="submit" name="submit" value="Guardar datos" tabindex="50" />
						
					</div>
					
					<div class="right rounded">
						
						<ul>
							<li class="title"><label for="banned">Bloqueado</label></li>
							<li>
								
							<select class="rounded text required" id="banned" name="banned" tabindex="46">
								<option value="0" <?php echo getSelected($row_webuser->banned, "0"); ?> >No</option>
								<option value="1" <?php echo getSelected($row_webuser->banned, "1"); ?> >Si</option>
							
							</select>
							</li>
							
							<?php if($action == 'edit'){ ?>
							<li class="title">Fechas</li>
							<li><strong>Registro:</strong> <?php echo $row_webuser->created; ?></li>
							<?php } ?>
						</ul>
					
					</div>
					
				</form>
				
			</div>
			
			<div class="clear"></div>