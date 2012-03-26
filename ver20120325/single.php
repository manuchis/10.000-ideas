<?php
//=====================================
// the classes
//=====================================

require_once('includes/functions.php');
require_once('classes/class.mysql.php');
require_once('classes/class.site.php');
require_once('includes/facebook.php');

//=====================================
// get the variables
//=====================================

$this_page = $_SERVER['PHP_SELF'];

$s = "single";
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
$mainideas_data = $site->getIdeasLimitedbyCity(0,10, $city);
$map_data = $site->getIdeasLimitedbyCity(0,10, $city);
$categorias; $ciudades;
 while($row_categ = $site->fetchObject($categ_data)){ 
	$categorias[$row_categ->id] = Array('id'=>$row_categ->id, 'nombre'=>$row_categ->nombre, 'color'=>$row_categ->color);
} 
 while($row_ciudad = $site->fetchObject($ciudad_data)){ 
	$ciudades[$row_ciudad->id] = Array('id'=>$row_ciudad->id, 'nombre'=>$row_ciudad->nombre, 'pais'=>$row_ciudad->pais, 'paisn'=>$row_ciudad->paisn);
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
$post_data = $site->getIdeabyId($id); 
$row_post = $site->fetchObject($post_data);
$ideasmap = $row_post->id.','. utf8_decode($row_post->idea).','. utf8_decode($categorias[$row_post->categoria]['nombre']).','.utf8_decode($row_post->ubicacion).','. utf8_decode($row_post->barrio).','. utf8_decode($ciudades[$row_post->ciudad]['nombre']).','.utf8_decode($ciudades[$row_map->ciudad]['paisn']).','.$categorias[$row_post->categoria]['color'].';';
?>
<?php include('header.php'); ?>
		<section class="body">
				
				<div class="section-body-wrapper">
				
						<section class="main">
								<?php  
									if($row_post):
								?>
								<article class="ideas">
								
										<div class="post-top">
										
												<p class="post-meta">Por <?php echo utf8_decode($row_post->usuario); ?> de <a href=""><?php echo utf8_decode($row_post->barrio); ?></a></p>
												<p class="post-date"><?php echo date('j \d\e ', strtotime($row_post->creado)) . meses(date('m', strtotime($row_post->creado))) . date(' \d\e Y', strtotime($row_post->creado)); ?></p>
										
										</div> <!-- end of .post-top -->
										
										<div class="post">
											<?php 	if($row_post->fbid){ ?>
														<img src="https://graph.facebook.com/<?php echo $row_post->fbid; ?>/picture" class="avatar" height="50px" width="50px" />
											<?php	}else if($row_post->imagen){ ?>
													<img src="<?php echo HOME_URL.'user_images/'.$row_post->imagen; ?>" class="avatar" height="50px" width="50px" />
											<?php 	}else{ ?>
													<img src="<?php echo HOME_URL.'user_images/ninguno.png' ?>" class="avatar" height="50px" width="50px" />
													
											<?php 	}	?>
										
												<div class="post-category"  style="background-image: url(img/cats/<?php echo $categorias[$row_post->categoria]['color'];?>_c.png);"><a href="index.php?cat=<?php echo $row_mainideas->categoria;?>" style="color: #<?php echo $categorias[$row_post->categoria]['color'];?>;"><?php echo utf8_decode($categorias[$row_post->categoria]['nombre']); ?></a></div>
												<h3><?php echo make_links_clickable(utf8_decode($row_post->idea)); ?></h3>
										
										</div> <!-- end of .post -->
										
										<div class="post-bottom">
												
												<a class="votes-link" href="single.php?post=<?php echo $row_mainideas->id; ?>" title="Apoya esta idea!" voteid="<?php echo $row_mainideas->id; ?>"><span class="votes"><?php echo $row_post->votos; ?></span> votos <span class="no-votes">+1</span></a>
												<input type="hidden" name="ub" value="<?php echo $infouser->banned; ?>" id="ub" /><input type="hidden" name="user" value="<?php echo $infouser->id; ?>" id="user" />
												
																						
										</div> <!-- end of .post-bottom -->
								
										<div id="disqus_thread"></div>
										<script type="text/javascript">
										    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
										    var disqus_shortname = '10000ideas'; // required: replace example with your forum shortname

										    /* * * DON'T EDIT BELOW THIS LINE * * */
										    (function() {
										        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
										        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
										        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
										    })();
										</script>
										<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
										<a href="http://disqus.com" class="dsq-brlink">Comentarios por <span class="logo-disqus">Disqus</span></a>
										
								</article> <!-- end of article.ideas -->
								<?php
									else: ?>
									<article class="ideas">No hay ideas</article>
								<?php	endif; ?>
								
							<?php include("sidebar.php");?>
						
						</section> <!-- end of section.main -->
						
				
				</div> <!-- End of .section-body-wrapper -->
		
		</section> <!-- End of section.body -->
		
<?php include('footer.php');?>
</body>
</html>