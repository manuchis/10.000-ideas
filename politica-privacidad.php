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
						
												
												<h2 id="ayuda">Política de privacidad</h2>
										
			
										<h3></h3>		
										<p>10.000 ideas es un proyecto independiente de bien público, coordinado por Manuel Portela y opera el sitio http://10.000ideas.com/ , http://000ideas.com y sus derivados.</p>

										<p>Principios básicos sobre nuestra política de privacidad:<br />
										- No soliticamos datos personales mas allá de tu nombre real, a menos que realmente se necesiten.<br />
										- No compartimos su información personal con nadie, a excepción de cumplir con la ley o proteger nuestros derechos.<br />
										- No alojamos información personal comprometedora en nuestros servidores.<br />
										- Podremos modificar las politicas de privacidad en cualquier momento de ser necesario, siempre con arreglo a la ley y a las regulaciones administrativas y judiciales pertinentes, y será notificado a los usuarios a través de este sitio.<br />
										Si tienes preguntas sobre eliminar o corregir información personal, por favor escribe a 1@000ideas.com.</p>

										<h3>Visitantes</h3>
										<p>Como la mayoría de los operadores web, 10.000 ideas colecta datos que no identifican a las personas que los navegadores y servidores proveen tal como el tipo de navegador utilizado, el lenguaje de preferencia, el sitio de referencia y la fecha y tiempo de cada petición del visitante. El proposito de recolectar esta información que no identifica a las personas es comprender mejor cómo los visitantes usan el sitio web. Eventualmente 10.000 ideas publicará información recolectada.</p>

										<p>10.000 ideas también recolecta información que puede identificar a las personas potencialmente, tal como direcciones IP. Esta información no es visible públicamente y no será difundida.</p>

										<h3>Datos personales</h3>
										<p>Algunos visitantes deciden interactuar con 10.000 ideas por lo cual se requiere información identificatoria. La cantidad y tipo de información depende de la naturaleza de la interacción.</p>
										<p>Por ejemplo, para dejar una idea o votar una, es requerido dejar un nombre real, un correo electrónico o ser relacionado con una red social como son Facebook.com o Twitter.com.
										10.000 ideas no usará esta información con otro provecho que la mencionada. Los visitantes pueden negarse a proveer esta información, o a que esta sea modificada o eliminada.</p>

										<h3>Protección de datos personales</h3>
										<p>10.000 ideas protegerá los datos personales con arreglo a la legislación vigente y su
										reglamentación,, y solo serán utilizado por las personas autorizadas que precisen esta información para el desarrollo del sitio y sus actividades, y hayan aceptado no compartirlas con otras personas o instituciones. </p>
										<p>10.000 ideas no alquilará ni venderá la información recolectada ni los datos personales a nadie.
										Sin perjuicio de lo expuesto, considerando que internet es un sistema abierto, de acceso público, 10.00 ideas no puede garantizar que terceros no autorizados no puedan eventualmente superar las medidas de seguridad y utilizar la información de los Usuarios en forma indebida, en cuyo caso 10.000 ideas no será responsable.</p>

										<p>10.000 ideas ocasinalmente enviará correos electrónicos acerca de las novedades del proyecto, solicitar respuestas o mantener actualizado al usuario. Asimismo, el usuario puede anular la suscripción a la lista de correos de 10.000ideas en cualquier momento.</p>

										<h3>Cookies</h3>
										<p>Una "cookie"  es una cadena de información que un sitio web puede almacenar en la computadora del visitante, y que el navegador del visitante provee al sitio web cada vez que el visitante regresa. 10.000 ideas utiliza las cookies para identificar a los visitantes, seguir su uso y guardar sus preferencias. Los visitantes que no estén interesados en usar cookies en sus computadoras, deben ajustar su navegador para que este no las almacene antes de visitar 10.000 ideas  con la probabilidad de que algunas funciones del sitio no estén disponibles o no funcionen correctamente.</p>

										<h3>Transferencia</h3>
										<p>Si 10.000 ideas o alguno de sus proyectos es adquirido o deja de funcionar, la información será transferida o adquirida por un tercero. Usted será notificado si esto ocurre y cualquier adquiriente o cesionario de 10.000 ideas puede continuar utilizando su información personal establecida según esta póliza.</p>

										<h3>Publicidades</h3>
										<p>Las publicidades que aparecen en el sitio web son provistas por partners de publicidad que utilizan cookies. Estas cookies permiten a los servidores de publicidad (Ad server) a reconocer su computadora cada vez que envían una publicidad online para compilar información acerca de usted u otros que usen su computadora. Esta información permite a las redes de publicidad para, entre otras cosas, enviar publicidades dirigidas que ellos creen que será de interés para usted. Esta política de privacidad cubre el uso de cookies por 10.000 ideas y no cubre el uso de cookies por ningún partner de publicidad. Por el uso de cookies por parte de éstos partners, 10.000 ideas no se hace responsable.</p>

										<h3>Menores de edad</h3>
										<p>Debido a que los menores de edad (menores a 18 años) pueden no alcanzar a comprender debidamente la Política de Privacidad y sus implicancias, ni decidir válidamente sobre las opciones disponibles para los Usuarios, los Usuarios deberán, en sus respectivas políticas de privacidad, respetar las reglamentaciones al respecto aplicables a los menores de edad, y asimismo instar a todos los padres o representantes, tutores o adultos bajo cuya supervisión se encuentren los menores que accedan a los sitios de internet, a participar activa y cuidadosamente en las actividades que el menor realice en internet, en los servicios on-line que utilicen dichos menores, en la información a la que estos accedan, ya sea cuando dichos menores visiten los sitios de los Usuarios o cualquier otro sitio de terceros, y a enseñarles y a guiarlos en cómo proteger su propia información personal mientras estén navegando en internet.</p>

										<h3>Cambios en la política de privacidad</h3>
										<p>10.000 ideas podrá modificar la presente Política de Privacidad en caso que lo considere oportuno. En caso que las modificaciones sean sustanciales con relación al tratamiento de los datos personales recolectados en virtud de los Servicios, las mismas serán también notificadas mediante la publicación de un aviso destacado en la página principal del Sitio de 10.000 ideas. En caso que el Usuario deseara ser notificado vía correo electrónico de dichas modificaciones, deberá enviar un correo electrónico , a 1@000ideas.com , desde la dirección de correo electrónico en la cual desea recibir dichas notificaciones.</p>

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