<?php
//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	include('includes/messages_list.php');
	echo $messages_list['includes'][0];
	die;
}

//-=-=-=-= load the data =-=-=-=-

require_once('classes/class.ideas.php');

$content= new Idea();
$content_data = $content->getContent($id);
$row_content = $content->fetchObject($content_data);
$ciudad_data = $content->getCiudades();
 while($row_ciudad = $content->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais);
}
$categ_data = $content->getCategorias();
 while($row_categ = $content->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
}
?>
			<!-- form de edicion de contenidos -->
			
			<div id="edit">
				<h3><?php echo $page_title; ?> idea</h3>
				
				<!-- error and messages -->
			
				<?php getError($error_num, $custom_error); ?>
				
				<?php getMessage($message); ?>
				
				<!-- form -->
				
				<form id="data" name="data" action="<?php echo $actions_page; ?>" method="post">
					<div class="left">
						
						<ul>
							<li class="title"><label for="idea">Idea</label></li>
							<li><textarea name="idea" id="idea" class="required rounded title" rows="8" cols="40"><?php echo utf8_decode($row_content->idea); ?></textarea></li>
							<li class="title"><label for="ubicacion">ubicacion</label></li>
							<li><input class="required rounded title" type="text" name="ubicacion" id="ubicacion" value="<?php echo utf8_decode($row_content->ubicacion); ?>" tabindex="30" /></li>
							<li class="title"><label for="ubicacion">Barrio</label></li>
							<li><input class="required rounded title" type="text" name="barrio" id="barrio" value="<?php echo utf8_decode($row_content->barrio); ?>" tabindex="30" /></li>
							<li class="title"><label for="ciudad">Ciudad</label></li>
								<li><select name="ciudad" id="ciudad" size="1">
									<?php foreach($ciudades as $ciudad){ ?>
										<option value="<?php echo $ciudad['id']; ?>" <?php echo getSelected($ciudad['id'], $row_content->ciudad); ?>><?php echo $ciudad['nombre']; ?></option>
									<?php } ?>
								</select></li>
								<li class="title"><label for="Usuario">Usuario</label></li>
								<li><a href="http://www.facebook.com/profile.php?id=<?php echo $row_content->usuario; ?>"><?php echo utf8_decode($row_content->nombre) ." (". $row_content->user .")"; ?></a></li>
							<li class="title"><label for="categoria">Categoría</label></li>
								<li><select name="categoria" id="categoria" size="1">
									<?php foreach($categorias as $categoria){ ?>
									<option value="<?php echo $categoria['id']; ?>" <?php echo getSelected($categoria['id'], $row_content->categoria); ?>><?php echo utf8_decode($categoria['nombre']); ?></option>
									<?php } ?>
							</select></li>
										
						
						</ul>
						<!-- hidden data -->
						<input type="hidden" name="c" id="c" value="<?php echo $c; ?>" />
						<input type="hidden" name="m" id="m" value="<?php echo $m; ?>" />
						<input type="hidden" name="action" id="action" value="<?php echo $action; ?>_ideas" />
						<input type="hidden" name="id" id="id" value="<?php echo $row_content->id; ?>" />
						
						<input class="button" type="submit" id="submit" name="submit" value="Guardar datos" tabindex="55" />
						
					</div>
					<div class="right rounded">
						
						<ul>
							<li><strong>Votos</strong> <?php echo $row_content->votos; ?></li>
							<li class="title"><label for="aprobado">Aprobado</label></li>
							<li>
								
							<select class="rounded text required" id="aprobado" name="aprobado" tabindex="46">
								<option value="0" <?php echo getSelected($row_content->aprobado, "0"); ?> >No</option>
								<option value="1" <?php echo getSelected($row_content->aprobado, "1"); ?> >Si</option>
							
							</select>
							</li>							
							<li class="title">Fechas</li>
							<li><strong>Creaci&oacute;n:</strong> <?php echo $row_content->creado; ?></li>
							<li><strong>Modificaci&oacute;n:</strong> <?php echo $row_content->modificado; ?></li>
							
						</ul>
					
					</div>
					
				</form>
				
			</div>
			
			<div class="clear"></div>