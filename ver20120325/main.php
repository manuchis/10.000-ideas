<?php
$news_data = $site->getNewsListLimitedPos(0, 8);
$work_data = $site->getRandomWork();
$row_work = $site->fetchObject($work_data);
$i = 0;
?>
<div id="main"> <!-- contenido principal --> 

<div id="welcome">
	<h2 class="tit welcome">Bienvenidos</h2>
	
<p>Damos la bienvenida a los alumnos de los cinco niveles al ciclo 2011. Los invitamos a explorar en &eacute;ste sitio <strong>nuestros antecedentes</strong> de los &uacute;ltimos dos a&ntilde;os, y <strong>nuestra propuesta para el 2011</strong>, la cual estar&aacute; centrada en  el <strong>dise&ntilde;o para el desarrollo social</strong>, considerando la plena inclusi&oacute;n de la disciplina  en el Anteproyecto del Plan Nacional de Ciencia y Tecnolog&iacute;a 2011-2014  para lo cual trabajamos, asi como para su consideraci&oacute;n en las otras tem&aacute;ticas: desarrollo industrial, salud y medio ambiente. Esta propuesta se despliega a trav&eacute;s de...</p>

	<p><a href="index.php?s=content&id=100">Leer más &raquo;</a></p>
</div><!-- cierra #welcome -->

<div id="news">
<h2 class="tit noticias">&Uacute;ltimas Noticias</h2>

<div class="post-grouop">
	<?php while($row_news = $site->fetchObject($news_data)){ ?>

		<?php if($i == 0 && $row_news->image != ''){ ?>
			<div id="post-<?php echo $i+1; ?>" class="post">
				<div class="post-img"><img src="../media/images/<?php echo $row_news->image; ?>.<?php echo $row_news->ext; ?>" /></div>
					<div class="entry">
						<h1><?php echo $row_news->title; ?></h1>
						<p><?php echo $row_news->short; ?></p>
						<p><a href="index.php?s=news_det&id=<?php echo $row_news->id; ?>" class="more">Seguir leyendo...</a></p>
					</div><!-- cierra entry -->
					<div class="clearfix"><!-- --></div>
				</div> <!-- cierra post -->
		<?php } else{  ?>
			<?php if($i == 1){
				echo "<div class='otras-noticias'>Otras noticias</div>";
			}?>
		<div class="post second" id="post-<?php echo $i+1; ?>">
			<div class="entry">
				<h1><a href="index.php?s=news_det&id=<?php echo $row_news->id; ?>"><?php echo $row_news->title; ?></a></h1>
			</div><!-- cierra entry -->
			<div class="clearfix"><!-- --></div>
		</div> <!-- cierra post -->
		<?php } $i++; } ?>
		<div class="clearfix"><!-- --></div>
</div> <!-- cierra #post-group -->		

</div> <!-- cierra #news -->
</div> <!-- cierra #main -->
<?php include('sidebar.php'); ?>