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

require_once('classes/class.ciudades.php');

$content= new Ciudad();
$content_data = $content->getContent($id);
$row_content = $content->fetchObject($content_data);
$pais_data = $content->getPaises();
 while($row_pais = $content->fetchObject($pais_data)){ 
	$paises[$row_pais->id] = Array('id'=>$row_pais->id, 'nombre'=>$row_pais->nombre, 'color'=>$row_pais->color);
}
?>
			<!-- form de edicion de contenidos -->
			
			<div id="edit">
				<h3><?php echo $page_title; ?> ciudad</h3>
				
				<!-- error and messages -->
			
				<?php getError($error_num, $custom_error); ?>
				
				<?php getMessage($message); ?>
				
				<!-- form -->
				
				<form id="data" name="data" action="<?php echo $actions_page; ?>" method="post">
					<div class="left">
						
						<ul>
							<li class="title"><label for="nombre">Ciudad</label></li>
							<li><input class="required rounded title" type="text" name="nombre" id="nombre" value="<?php echo utf8_decode($row_content->nombre); ?>" tabindex="30" /></li>
							<li class="title"><label for="pais">País</label></li>
						
									<li><select name="pais" id="pais" size="1">
										<?php foreach($paises as $pais){ ?>
										<option value="<?php echo $pais['id']; ?>" <?php echo getSelected($pais['id'], $row_content->pais); ?>><?php echo $pais['nombre']; ?></option>
										<?php } ?>
								</select></li>
						<li class="title"><label for="pais">Barrios</label></li>
										
						<li><textarea name="barrios" rows="8" cols="40" class="required rounded title"><?php echo utf8_decode($row_content->barrios);?></textarea></li>
						</ul>
						<!-- hidden data -->
						<input type="hidden" name="c" id="c" value="<?php echo $c; ?>" />
						<input type="hidden" name="m" id="m" value="<?php echo $m; ?>" />
						<input type="hidden" name="action" id="action" value="<?php echo $action; ?>_ciudad" />
						<input type="hidden" name="id" id="id" value="<?php echo $row_content->id; ?>" />
						
						<input class="button" type="submit" id="submit" name="submit" value="Guardar datos" tabindex="55" />
						
					</div>
					<div class="right rounded">
						
						<ul>
								
							<li class="title">Fechas</li>
							<li><strong>Creaci&oacute;n:</strong> <?php echo $row_content->created; ?></li>
							
						</ul>
					
					</div>
					
				</form>
				
			</div>
			
			<div class="clear"></div>