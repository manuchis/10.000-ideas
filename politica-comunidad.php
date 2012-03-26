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
						
												
												<h2 id="ayuda">Políticas de comunidad</h2>
										
			
										<h3>Interactuar en el sitio</h3>		
										<p>Al ingresar al sitio y registrarte por cualquiera de los medios, aceptas participar en nuestra comunidad. Por lo cual aceptas y adhieres a estos términos.</p>

										<h3>Qué hacer</h3>
										<p><strong>Uso del nombre real.</strong> Buscamos que las ideas y los comentarios sean creados por personas reales, por eso no permitimos el uso anónimo en la comunidad.</p>
										<p><strong>Comparte las ideas.</strong> Aprovecha las herramientas para compartir y conectar tus ideas y aquellas que te gusten. Difundelas!</p>
										<p><strong>Activate!</strong> Deja tus propias ideas y aporta a las ideas de otros, ponerse en contacto y construir grandes ideas hace que los proyectos se realicen.</p>
										
										<h3>Qué no hacer</h3>
										<p><strong>No te quejes.</strong> Este es un lugar para proponer nuevas ideas, construir espacios y coordinar proyectos que benefician a todos, no queremos que se convierta en una zona de quejas. Los comentarios e ideas serán moderadas para preservar el espíritu del proyecto.</p>
										<p><strong>No blasfemar.</strong> El uso de lenguaje ofensivo e hiriente contra otros será repudiado y tu usario puede ser bloqueado o borrado.</p>
										<p><strong>No molestes.</strong> Este sitio no es un lugar donde se pueda acosar, amenazar, intimidar, o faltar el respeto hacia otras personas. Si recibimos quejas por el uso indiscriminado, tu usario será bloqueado.</p>
										<p><strong>Sin publicidad.</strong> Este es un espacio abierto a las ideas, pero no permitimos mensajes comerciales ni políticos. </p>
										<p><strong>No exponer información confidencial ni contenidos bajo derechos de autor.</strong> Si no tienes derecho a publicar un material, este no es el lugar indicado. Tampoco aceptamos datos ni información personal. Todo contenido que sea considerado confidencial será eliminado. </p>
										<p><strong>No destruyas.</strong> No intentes hackear el sitio, ni utilizar bots contra el. Este sitio es de bien público e independiente, por favor si necesitas algo escribenos. Respeta el trabajo de otros.</p>
										<h3>A los moderadores y editores</h3>
										<p><strong>Ser justo.</strong> Moderar la actividad del sitio de forma consistente con los principios del mismo. Contacta a otros miembros si tienes dudas o escribenos si algún miembro viola estas condiciones de uso.</p>
										<p><strong>Ser frecuente.</strong> Asegurarse de que los proyectos e ideas no se han violado algunas de las reglas. Visitenlos con frecuencia y ante cualquier duda escribenos a 1@000ideas.com</p>
										<p><strong>Solicita ayuda.</strong> Si no esta seguro de como lidiar con un problema o necesita algun consejo, escribe a 1@000ideas.com y lo resolveremos juntos.</p>
										
										<p>Tambien existen los <a href="terminos-condiciones.php">terminos de uso</a> que pueden ser leidos. Si no estas de acuerdo con estas condiciones, probablemente no sea el lugar apropiado para ti. Recuerda escribirnos ante cualquier duda.</p>
										
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