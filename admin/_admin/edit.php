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

$webuser = new Admin();

//$content_data = $content->getContent($id);
//$row_content = $content->fetchObject($content_data);
$user_data = $webuser->getAdmin($id);
$row_user = $webuser->fetchObject($user_data);

$ciudad_data = $webuser->getCiudades();
 while($row_ciudad = $webuser->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais);
}
?>
			<!-- form de edicion de contenidos -->
			
			<div id="edit">
				<h3><?php echo $page_title; ?> Accesos</h3>
				
				<!-- error and messages -->
			
				<?php getError($error_num, $custom_error); ?>
				
				<?php getMessage($message); ?>
				
				<!-- form -->
				
				<form id="data" name="data" action="<?php echo $actions_page; ?>" method="post">
					<div class="left">
						
						<ul>
							<li class="title">Nombre</li>
							<li><input class="required rounded title" type="text" name="nombre" id="nombre" value="<?php echo $row_user->nombre; ?>" tabindex="30" /><input class="required rounded title" type="text" name="apellido" id="apellido" value="<?php echo $row_user->apellido; ?>" tabindex="30" /></li>
							<li class="title">Correo</li>
							<li><input class="required rounded title" type="text" name="email" id="email" value="<?php echo $row_user->email; ?>" tabindex="30" /></li>
														
							<li class="title">Ciudad</li>
							<li><select name="ciudad" id="ciudad" size="1">
								<?php foreach($ciudades as $ciudad){ ?>
									<option value="<?php echo $ciudad['id']; ?>" <?php echo getSelected($ciudad['id'], $row_user->ciudad); ?>><?php echo $ciudad['nombre']; ?></option>
								<?php } ?>
																								
							</select></li>
							
								<?php if($action == 'edit'){ ?>
								<li class="title"><label for="psw">Contrase&ntilde;a actual</label></li>
								<li><input class="required rounded text" type="password" name="psw" id="psw" value="" tabindex="40" /></li>

								<li class="title"><label for="psw_n">Contrase&ntilde;a nueva</label> <small>(solo si se requiere cambiar la contrase&ntilde;a)</small></li>
								<li><input class="rounded text" type="password" name="psw_n" id="psw_n" value="" tabindex="45" /></li>
								<?php } ?>
						</ul>
						
						<!-- hidden data -->
						<input type="hidden" name="c" id="c" value="<?php echo $c; ?>" />
						<input type="hidden" name="m" id="m" value="<?php echo $m; ?>" />
						<input type="hidden" name="action" id="action" value="<?php echo $action; ?>_admin" />
						<input type="hidden" name="id" id="id" value="<?php echo $row_user->id; ?>" />
						
						<input class="button" type="submit" id="submit" name="submit" value="Guardar datos" tabindex="55" />
						
					</div>
						<div class="right rounded">

							<ul>
							
								</li>

								<?php if($action == 'edit'){ ?>
								<li class="title">Fechas</li>
								<li><strong>Registro:</strong> <?php echo $row_user->alta; ?></li>
								<li><strong>Modificación:</strong> <?php echo $row_user->modificacion; ?></li>
								<li><strong>Login:</strong> <?php echo $row_user->login; ?></li>
								
								<?php } ?>
							</ul>

						</div>
			
					
				</form>
				
			</div>
			
			<div class="clear"></div>