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

require_once('classes/class.paises.php');

$content= new Pais();
$content_data = $content->getContent($id);
$row_content = $content->fetchObject($content_data);

?>
			<!-- form de edicion de contenidos -->
			
			<div id="edit">
				<h3><?php echo $page_title; ?> pais</h3>
				
				<!-- error and messages -->
			
				<?php getError($error_num, $custom_error); ?>
				
				<?php getMessage($message); ?>
				
				<!-- form -->
				
				<form id="data" name="data" action="<?php echo $actions_page; ?>" method="post">
					<div class="left">
						
						<ul>
							<li class="title"><label for="nombre">Pais</label></li>
							<li><input class="required rounded title" type="text" name="nombre" id="nombre" value="<?php echo utf8_decode($row_content->nombre); ?>" tabindex="30" /></li>
							<li class="title"><label for="descripcion">Descripción</label></li>
							
						<li><input class="required rounded title" type="text" name="descripcion" id="descripcion" value="<?php echo utf8_decode($row_content->descripcion); ?>" tabindex="30" /></li>
										
						
						</ul>
						<!-- hidden data -->
						<input type="hidden" name="c" id="c" value="<?php echo $c; ?>" />
						<input type="hidden" name="m" id="m" value="<?php echo $m; ?>" />
						<input type="hidden" name="action" id="action" value="<?php echo $action; ?>_pais" />
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