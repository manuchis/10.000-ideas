<?php
//=====================================
// the classes
//=====================================

require_once('includes/functions.php');
require_once('classes/class.mysql.php');
require_once('classes/class.site.php');

//=====================================
// get the variables
//=====================================

$this_page = $_SERVER['PHP_SELF'];

$s = "help";
if(isset($_GET['s']) && $_GET['s'] != '')
	$s = $_GET['s'];

$id = 0;
if(isset($_GET['post']) && $_GET['post'] != '')
	$id = $_GET['post'];
	
$l;
if(isset($_GET['l']) && $_GET['l'] != '')
	$l = $_GET['l'];

$y;
if(isset($_GET['y']) && $_GET['y'] != '')
	$y = $_GET['y'];

$page = 0;
if(isset($_GET['page']) && $_GET['page'] != '')
	$page = $_GET['page'];

$city = 1;
if(isset($_GET['city']) && $_GET['city'] != '')
	$city = $_GET['city'];
//=====================================
// start the site connection
//=====================================

$site = new site();

$ciudad_data = $site->getCiudades();
$categ_data = $site->getCategorias();
$categorias; $ciudades;
 while($row_categ = $site->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
} 
 while($row_ciudad = $site->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais);
}

$app_id = "192235404187723";
$app_secret = "28c6c2c8323198f4060a22085b04acf7";

define('YOUR_APP_ID', $app_id);
define('YOUR_APP_SECRET', $app_secret);

$cookie = get_facebook_cookie($app_id, $app_secret);
if($cookie){
	$fbuser = json_decode(file_get_contents('https://graph.facebook.com/me?access_token=' . $cookie['access_token']));
	$logged = $cookie;	
}

if($fbuser){ //si ya hizo facebook connect
	$usercheck = $site->checkUser($fbuser->name, $fbuser->id); //checkea si está logueado
	$checkuser = $site->fetchObject($usercheck);
	if($checkuser->id){ //si está logueado brinda la info
		$userinfo = $site->getUser($checkuser->id);
		$infouser = $site->fetchObject($userinfo);
	}else{ //si no está logueado escribe la info del usuario y la muestra
		$setuser = $site->setUser($fbuser->name, $fbuser->id, now());
		if($setuser){
			$userinfo = $site->getUser($checkuser->id);
			$infouser = $site->fetchObject($userinfo);
		}else{
			$errorlogin = "Error en el acceso"; // si hay error en escribir la info
		}
	}
}else{ //si no hizo facebook connect
	
}
?>
<?php include('header.php'); ?>
		<section class="body">
				
				<div class="section-body-wrapper">
				
						<section class="main">
							
								<article class="ideas help">
						
												
										<h2 id="colaborar">Agradecimientos y colaboraciones</h2>

										<h3>Colaboradores</h3>
										<ul>
											<li><a href="http://twitter.com/marinat">Marina Taliberto</a></li>
											<li><a href="http://twitter.com/feer_z">Fernando de La Madrid</a></li>
											<li><a href="http://twitter.com/dalilamodernday">Dalila Gryngarten</a> (ilustraciones)</li>
											<li><a href="http://twitter.com/marisacharny">Marisa Charny</a></li>
											<li><a href="http://twitter.com/laurapaonessa">Laura Paonessa</a></li>
											<li><a href="http://twitter.com/rulski">Eugenio Berzal</a> (maquetación)</li>
											<li><a href="http://twitter.com/eavogadro">Enrique Avogadro</a></li>
											<li><a href="http://twitter.com/barbidelfino">Barbie Delfino</a></li>
											<li><a href="http://twitter.com/analauradlv">Ana Laura De La Vega</a></li>
											<li><a href="http://twitter.com/abasolo">José Abásolo</a></li>
											<li><a href="http://twitter.com/daniassael">Daniela Assael</a></li>
											<li><a href="http://twitter.com/acila">Debora Lambrechts</a></li>
											<li><a href="http://twitter.com/nolochacon">Nolo Chacón</a></li>
											<li><a href="http://twitter.com/nestorcarrasco">Nestor Carrasco</a></li>
											<li><a href="http://twitter.com/iharadutra">Ihara Dutra</a></li>
											<li><a href="http://twitter.com/perse79">Román Pellegrino</a></li>
											<li><a href="http://twitter.com/buanzo">Arturo Busleiman</a></li>
											<li><a href="http://twitter.com/lextwit">Alejandro Prieto</a></li>
											<li>Gabriel Hölzel</li>
											<li><a href="http://twitter.com/javierpallero">Javier José Pallero</a> (legales)</li>
											<li><a href="http://twitter.com/lalilov">Laura Messina</a></li>
											<li><a href="http://twitter.com/lisa_simpsonlk">Elisa Ferrari</a> (traducciones)</li>
											
											
										</ul>
										<p>¿Quieres colaborar? <a herf="http://10.000ideas.com/site/help.php#colaborar">Aquí</a> encontrarás la forma.</p>

									<ul class="datos">
										<li>Twitter: <a href="http://twitter.com/10000ideascom">@10000ideascom</a></li>
										<li>Facebook: <a href="http://www.facebook.com/pages/10000-ideas/217231118365265">http://www.facebook.com/pages/10000-ideas/217231118365265</a></li>
										<li>Google+: <a href="https://plus.google.com/113202297971949252410">https://plus.google.com/113202297971949252410</a></li>
										<li>Mail: <a href="mailto:&#049;&#064;&#048;&#048;&#048;&#105;&#100;&#101;&#097;&#115;&#046;&#099;&#111;&#109;">1@000ideas.com</a></li>
										</ul>
										
									
									
								</article> <!-- end of article.ideas -->
							
								<?php include("sidebar.php"); ?>
							
						
						</section> <!-- end of section.main -->
						
				
				</div> <!-- End of .section-body-wrapper -->
		
		</section> <!-- End of section.body -->
		
<?php include('footer.php');?>
</body>
</html>