<?php
//-=-=-=-= you must put this script on every page =-=-=-=-

if(basename($_SERVER['SCRIPT_FILENAME']) != 'index.php')
{
	//avoid to load the page directly
	echo "You can't load this page directly!";
	die;
}
?>

			<ul>
				<li><a <?php echo setCurrent($c, "dashboard"); ?> href="index.php" title="Panel principal">Panel principal</a></li>
				<li class="separator"></li>
				<li><a <?php echo setCurrent($c, "idea"); ?> href="index.php?c=ideas" title="Ideas">Ideas</a></li>
				<li><a <?php echo setCurrent($c, "cat"); ?> href="index.php?c=cat" title="Categorías">Categorías</a></li>
				<li class="separator"></li>
				
				<li><a <?php echo setCurrent($c, "ciudad"); ?> href="index.php?c=ciudades" title="Ciudades">Ciudades</a></li>
				<li><a <?php echo setCurrent($c, "pais"); ?> href="index.php?c=paises" title="Paises">Países</a></li>
				
				<li class="separator"></li>
				
				<li><a <?php echo setCurrent($c, "user"); ?> href="index.php?c=users" title="Usuarios">Usuarios</a></li>
				<li><a <?php echo setCurrent($c, "admin"); ?> href="index.php?c=admin" title="Administradores">Administradores</a></li>
			</ul>