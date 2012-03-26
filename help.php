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
						
												
												<h2 id="ayuda">Ayuda</h2>
										
			

										<h3>¿Qué es 10.000 ideas?</h3>
										<p><strong>10.000 ideas</strong> es una plataforma de colaboración ciudadana creada con la intención de dar una oportunidad para que todos podamos participar en la mejora de nuestra ciudad. Para participar sólo tienes que dejar una idea que se te ocurra, por más simple que parezca. Así, ya estarás aportando la generación de nuevos proyectos que permitirán que tu ciudad sea más accesible para todos.</p>

										<h3>¿Quien lo hace?¿Por qué?</h3>
										<p><strong>10.000 ideas</strong> está realizado con la colaboración de personas reales y auténticas con un mismo interés. Lo hacemos porque pensamos que las ciudades no las hacen los gobiernos únicamente, nosotros los ciudadanos, debemos proponer nuevas iniciativas y construir la ciudad que queremos vivir.</p>

										<h3>¿Quienes pueden participar?</h3>
										<p>Todos, es una plataforma abierta y simple para que cualquiera tenga acceso. Podes proponer una idea o votar la que más te guste, así se elegirán los mejores proyectos para hacerlos realidad. Recuerda que no aceptamos quejas ni mensajes negativos, las ideas propuestas deben ser constructivas. Puedes visitar nuestras <a href="politica-comunidad.php">políticas de comunidad</a> para un mejor uso de la plataforma.</p>

										<h3>¿Qué ciudades están disponibles?</h3>
										<p>La idea es que participen todas las ciudades de Latinoamérica, pero éstas van a estar disponibles de a poco. Si tu ciudad no está en la lista, <del>escribenos a  <a href="mailto:&#049;&#064;&#048;&#048;&#048;&#105;&#100;&#101;&#097;&#115;&#046;&#099;&#111;&#109;">1@000ideas.com</a></del> abre un ticket en <a href="https://github.com/manuchis/10.000-ideas/issues">nuestro repositorio</a> para que la agreguemos. </p>

										<h3>¿Y si quiero colaborar de otra forma?</h3>
										<p>Visita la sección <a href="#colaborar">colaborar</a>. Podrás encontrar la forma que más te guste para apoyar nuestro proyecto.</p> 

										<h3>¿Como se eligen los proyectos seleccionados?</h3>
										<p>Entre las ideas más votadas, se seleccionarán aquellas que cumplan con diferentes características para que los proyectos sean viables. Un conjunto de profesionales y consejeros relacionados con el desarrollo urbano evaluarán las ideas y junto con la persona que la propuso, se realizará una propuesta formal para llevar a cabo el proyecto.</p>

										<h3>¿Como ayuda 10.000 ideas a hacer realidad los proyectos?</h3>
										<p><strong>10.000 ideas</strong>brindará ayuda para gestionar y llevar el proyecto a buen puerto. Queremos apoyar a aquellos que quieran producirlo, conseguir financiación y los medios necesarios para elaborarlos. Si bien nosotros no vamos a desarrollar los proyectos, estaremos encantados de acompañarlos en su desarrollo.</p>

										<p class="mas"><strong>¿Mas preguntas?</strong> Escribenos a <a href="mailto:&#049;&#064;&#048;&#048;&#048;&#105;&#100;&#101;&#097;&#115;&#046;&#099;&#111;&#109;">1@000ideas.com</a> o mándanos un <a href="http://twitter.com/10000ideas.com">tweet @10000ideascom</a></p>

										<h2 id="colaborar">Colaborar</h2>

										<h3>Embajadores</h3>
										<p>Los embajadores son quienes se postulen para llevar la bandera de 10.000 ideas en su ciudad. Quienes crean en el proyecto, pueden tomar la iniciativa y comenzar a difundirlo entre sus conocidos ó a través de cualquier medio. Si te interesa ser un embajador en tu ciudad, escríbenos a  <a href="mailto:&#049;&#064;&#048;&#048;&#048;&#105;&#100;&#101;&#097;&#115;&#046;&#099;&#111;&#109;">1@000ideas.com</a> y te mandamos el material para que empieces cuanto antes!</p>

										<h3>Difusión</h3>
										<p>Si eres un medio de comunicación o te interesa recibir información del proyecto e invitaciones puedes anotarte a nuestro mailing y seguir nuestro blog dónde publicamos todas las actualizaciones. En la <a href="<?php echo HOME_URL; ?>press.php">sección de prensa</a> encontrarás material de difusión. También te invitamos a difundir nuestra cuenta de Twitter, página de Facebook y de Google+</p>

										<h3>Donar</h3>
										<p><strong>10.000 ideas</strong> es un proyecto sin fines de lucro, pero nos gustaría contar con recursos para realizar eventos, presentaciones y diferentes campañas de difusión para que todo el mundo se entere. Ayúdanos con una donación y serás incluido en nuestra lista de <a href="colab.php">colaboradores</a>.</p>

										<h3>Enviar proyecto</h3>
										<p>Si tienes algún proyecto que ya esté armado y crees que tiene que ver con <strong>10.000 ideas</strong>, <a href="mailto:&#049;&#064;&#048;&#048;&#048;&#105;&#100;&#101;&#097;&#115;&#046;&#099;&#111;&#109;">escríbenos</a> y busquemos la forma de colaborar mutuamente.</p>

										<h3>Apoyar</h3>
										<p><strong>10.000 ideas</strong> apoya y difunde una causa, la libertad de que personas y ciudades  pueden ser colaborativas auditando la transparencia gubernamental de los proyectos presentados. Es una asamblea pública y abierta a todos. Súmate a nuestra causa compartiendo el sitio en las redes sociales.</p>

										<h3>Producción</h3>
										<p>Si eres desarrollador web o de aplicaciones móviles, diseñador o productor, colabora con nosotros en el desarrollo de la plataforma. No tenemos un presupuesto y creamos este sitio en nuestro tiempo libre. Escríbenos para desarrollar la más grande plataforma de colaboración ciudadana!</p>

										<h3>Repositorio de errores y sugerencias</h3>
										<p>Como es un sitio en constante desarrollo y queremos que esté cada vez mejor, hemos abierto un espacio en <a href="https://github.com/manuchis/10.000-ideas/issues/">GitHub</a> para que puedan dejarnos los errores que han visto y las sugerencias para mejorar el sitio. </p>
										
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