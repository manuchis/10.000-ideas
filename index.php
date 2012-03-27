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

$error_num;
if(isset($_GET['error']) && $_GET['error'] != '') //error number
	$error_num = $_GET['error'];

$custom_error;
if(isset($_GET['cm']) && $_GET['cm'] != '') //custom message
	$custom_error = $_GET['cm'];

$message;
if(isset($_GET['msg']) && $_GET['msg'] != '') //custom message
	$message = $_GET['msg'];


$s = "main";
if(isset($_GET['s']) && $_GET['s'] != '')
	$s = $_GET['s'];	
$l;
if(isset($_GET['l']) && $_GET['l'] != '')
	$l = $_GET['l'];

$y;
if(isset($_GET['y']) && $_GET['y'] != '')
	$y = $_GET['y'];

$q;
if(isset($_GET['q']) && $_GET['q'] != '')
	$q = $_GET['q'];
	
$page = 0;
if(isset($_GET['page']) && $_GET['page'] != '')
	$page = $_GET['page'];

$cat = 0;
if(isset($_GET['cat']) && $_GET['cat'] != '')
	$cat = $_GET['cat'];
	
$city = 1;
if(isset($_GET['city']) && $_GET['city'] != '')
	$city = $_GET['city'];
//=====================================
// start the site connection
//=====================================

$site = new site();
$ciudad_data = $site->getCiudades();
$categ_data = $site->getCategorias();
if($q){
	$mainideas_data = $site->searchIdeasLimitedbyCity($q,0,10, $city);
	$votedideas_data = $site->searchIdeasLimitedbyCityVoted($q,0,10, $city);
	$map_data = $site->searchIdeasLimitedbyCity($q,0,10, $city);
}
elseif($cat==0){
	$mainideas_data = $site->getIdeasLimitedbyCity(0,10, $city);
	$votedideas_data = $site->getIdeasLimitedbyCityVoted(0,10, $city);
	$map_data = $site->getIdeasLimitedbyCity(0,10, $city);
}else{
	$mainideas_data = $site->getIdeasLimitedbyCityAndCat(0,10, $city, $cat);
	$votedideas_data = $site->getIdeasLimitedbyCityVotedAndCat(0,10, $city, $cat);
	$map_data = $site->getIdeasLimitedbyCityAndCat(0,10, $city, $cat);
}

$categorias; $ciudades; $ideasmap="";
 while($row_categ = $site->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
} 
 while($row_ciudad = $site->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais, 'paisn'=>$row_ciudad->paisn, 'barrios'=>$row_ciudad->barrios);
}
 while($row_map = $site->fetchObject($map_data)){ 
	$ideasmap .= utf8_decode($row_map->id).','. utf8_decode($row_map->idea).','. utf8_decode($categorias[$row_map->categoria]['nombre']).','.utf8_decode($row_map->ubicacion).','. utf8_decode($row_map->barrio).','. utf8_decode($ciudades[$row_map->ciudad]['nombre']).','.utf8_decode($ciudades[$row_map->ciudad]['paisn']).','.utf8_decode($categorias[$row_map->categoria]['color']).';';
}
$citymap = utf8_decode($ciudades[$city]['nombre']).','.utf8_decode($ciudades[$city]['paisn']);
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
	$usercheck = $site->checkUser(utf8_encode($fbuser->name), $fbuser->id); //checkea si está logueado
	$checkuser = $site->fetchObject($usercheck);
	if($checkuser->id){ //si está logueado brinda la info
		$userinfo = $site->getUser($checkuser->id);
		$infouser = $site->fetchObject($userinfo);
	}else{ //si no está logueado escribe la info del usuario y la muestra
		$setuser = $site->setUser(utf8_encode($fbuser->name), $fbuser->id, now());
		if($setuser){
			$userinfo = $site->getUser($checkuser->id);
			$infouser = $site->fetchObject($userinfo);
		}else{
			$errorlogin = "Error en el acceso"; // si hay error en escribir la info
			redir("index.php?msg=". urlencode($errorlogin));
			
		}
	}
}else{ //si no hizo facebook connect
	
}

?>
<?php include('header.php'); ?>
		
	
		<section class="body">
				
				<section class="map">
				
						<div id="map_canvas"></div>
						
						<div class="submit-updates">
								
								<div class="submit-updates-wrapper">
											<!-- error and messages -->


												
																							
										<form id="submit-form" method="post" action="action_send.php">
											<?php if ($infouser->banned==1) {$message = "Usuario bloqueado, escribenos a 1@000ideas.com!"; }; 
												getMessage($message); ?>
											<?php if ($fbuser) {  ?>
												<?php if ($infouser->banned==0) { ?>
												<fieldset>
														  <legend>Formulario</legend>
														  
														  <ol class="form">
														    
																    <li>
																      <label for="address">Dirección:</label>
																      <input maxlength="32" id="address" name="address" type="text" size="20" value="" height="1" placeholder="Si tu idea se aplica en un lugar específico, indícanos dónde" required>
																    </li>
																    
																    <li>
																      <label for="neighborhood">Barrio:</label>
																		<select id="neighborhood" name="neighborhood" size="1" placeholder="Barrio" required>
																			<option value="" disabled selected>Barrio/Comuna/Depto</option>
																			<option value="Todos">Todos</option>
																			<?php $barrios = explode(",", $ciudades[$city]['barrios']);
																		 foreach($barrios as $barrio){
																			$barrio = str_replace("'","",utf8_decode($barrio));
																			 ?>
																				<option value="<?php echo $barrio; ?>"><?php echo $barrio; ?></option>																			
																			<?php } ?>
																		</select>
																    </li>
																    
																    <li>
																      <label for="category">Categoría:</label>
																		<select id="category" name="category" size="1" placeholder="Catergoría" required>
																			<option value="" disabled selected>Categoría</option>
																			<?php foreach($categorias as $cat){ ?>
																				<option value="<?php echo $cat['id']; ?>"><?php echo utf8_decode($cat['nombre']); ?></option>																			
																			<?php } ?>
																		</select>
																    </li>
																    <li>
																      <label for="idea">Idea:</label>
																      <textarea maxlength="650" id="idea" name="idea" type="text" size="13" value=""  height="1" placeholder="Ahora sí! Cuéntanos tu idea..." required></textarea>
																    </li>
																    <li>
																    	<input type="submit" id="submit" name="submit" value="Publicar" />
																    	<input type="hidden" name="action" value="new_idea" id="action" />
																		<input type="hidden" name="city" value="<?php echo $city ?>" id="city" />
																		<input type="hidden" name="user" value="<?php echo $infouser->id; ?>" id="user" />
																    </li>

														  </ol>
												</fieldset> <!-- end of fieldset -->											    
													
													      <p class="user-logged"><?php echo utf8_decode($infouser->nombre); ?> <a href="#" onclick="FB.logout();">(salir)</a></p>
													<?php } //end user banned
													 ?>
														<input type="hidden" name="ub" value="<?php echo $infouser->banned; ?>" id="ub" />
													
							 					    <?php } else { ?>
														
														<div class="fb-login-button">Ingresá para dejarnos tu idea</div>
												    <?php } ?>
										</form> <!-- end of form#contact-form -->
										
								</div> <!-- end of .submit-updates-wrapper -->
						</div> <!-- end of .submit-updates -->
				
				</section> <!-- end of section.map -->
		
				<div class="section-body-wrapper">
				
						<section class="main">

								<article class="updates">
										<?php getError($error_num, $custom_error); ?>
										
										<h2 class="typo">Ideas</h2>
										<div class="updates-list-filter">
										
												<label for="orderby-votes-selector">Ideas más votadas</label><input type="radio" name="list-filter" value="orderby-votes" id="orderby-votes-selector" />
												
												<label for="orderby-date-selector">Últimas ideas</label><input type="radio" name="list-filter" value="orderby-date" id="orderby-date-selector" checked />
										
										</div> <!-- end of .updates-list-filter -->
										
										
										<ul class="updates-list active" id="orderby-date">
												<?php 
												$mainideas_ver = mysql_num_rows($mainideas_data); 
												if($mainideas_ver > 0): 
												 while($row_mainideas = $site->fetchObject($mainideas_data)){
													 ?>
													
													<li>
															<div class="post">

																	<a class="post-category" style="background-image: url(img/cats/<?php echo $categorias[$row_mainideas->categoria]['color'];?>_c.png);" href="index.php?cat=<?php echo $row_mainideas->categoria;?>"><?php echo $categorias[$row_mainideas->categoria]['nombre'];?></a>
																	<p class="post-meta">Por <?php echo utf8_decode($row_mainideas->usuario); ?> de <a href=""><?php echo utf8_decode($row_mainideas->barrio); ?></a></p>

																	<h3><a href="single.php?post=<?php echo $row_mainideas->id; ?>"><?php echo utf8_decode($row_mainideas->idea); ?></a></h3>
															</div> <!-- end of .post -->

															<div class="post-bottom">
																	<a  class="comment-link" href="single.php?post=<?php echo $row_mainideas->id; ?>">Comentar</a>
																	<a class="votes-link" href="single.php?post=<?php echo $row_mainideas->id; ?>" title="Apoya esta idea!" voteid="<?php echo $row_mainideas->id; ?>"><span class="votes"><?php echo $row_mainideas->votos; ?></span> votos <span class="no-votes">+1</span></a>
															</div> <!-- end of .post-bottom -->
													</li>
												<?php } else :  echo "<li>Lo sentimos, no hay ideas para esta ciudad. Prueba dejar la tuya! </li>";  endif; ?>
											
										
												
										</ul> <!-- end of ul.updates-list -->
										<ul class="updates-list" id="orderby-votes">
												<?php $votedideas_ver = mysql_num_rows($votedideas_data);
												 if($votedideas_ver > 0): 
												 while($row_votedideas = $site->fetchObject($votedideas_data)){ ?>
													
													<li>
															<div class="post">

																	<a class="post-category" style="background-image: url(img/cats/<?php echo $categorias[$row_votedideas->categoria]['color'];?>_c.png);" href="index.php?cat=<?php echo $row_votedideas->categoria;?>"><?php echo $categorias[$row_votedideas->categoria]['nombre'];?></a>
																	<p class="post-meta">Por <?php echo $row_votedideas->usuario; ?> de <a href=""><?php echo $row_votedideas->barrio; ?></a></p>

																	<h3><a href="single.php?post=<?php echo $row_votedideas->id; ?>"><?php echo utf8_decode($row_votedideas->idea); ?></a></h3>
															</div> <!-- end of .post -->

															<div class="post-bottom">
																	<a  class="comment-link" href="single.php?post=<?php echo $row_votedideas->id; ?>">Comentar</a>
																	<a class="votes-link" href="single.php?post=<?php echo $row_votedideas->id; ?>" title="<?php echo $row_votedideas->id; ?>"><span class="votes"><?php echo $row_votedideas->votos; ?></span> votos <span class="no-votes">+1</span></a>
															</div> <!-- end of .post-bottom -->
													</li>
												<?php } else :  echo "<li>Lo sentimos, no hay ideas para esta ciudad. Prueba dejar la tuya! </li>"; endif;  ?>
											
										
												
										</ul> <!-- end of ul.updates-list -->
								</article> <!-- end of article.updates -->
								
							<?php include("sidebar.php");?>
						
						</section> <!-- end of section.main -->
						
				
				</div> <!-- End of .section-body-wrapper -->
		
		</section> <!-- End of section.body -->
		
		
		
	<?php include('footer.php');?>
  
</body>
</html>