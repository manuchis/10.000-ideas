<?php //mantenimiento
	$mant_data = $site->getMantenimiento();
	$mantenimiento = $site->fetchObject($mant_data);
	$mant_url= HOME_URL. "mantenimiento.php";
	$active_session = true;
	require_once('admin/includes/constants.php');
	if (!isset($_SESSION))
	{
		ini_set("session.gc_maxlifetime", 10800);
		session_name(SESNAME);
		session_start();
	//	session_regenerate_id();
	}
	if(!isset($_SESSION['userName']) && !isset($_SESSION['userToken']))
	{
		$active_session = false;
		//now we check the cookie, so if we have a cookie we could star a session
		if(isset($_COOKIE[COOKIE_NAME])){ $active_session = true;}
	}
	if($mantenimiento->mantenimiento==1){
		require_once('admin/includes/constants.php');
		if($active_session == false){
			redir($mant_url);
		}
		};
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en" xmlns:fb="https://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en" xmlns:fb="https://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en" xmlns:fb="https://www.facebook.com/2008/fbml"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" xmlns:fb="https://www.facebook.com/2008/fbml"> <!--<![endif]-->
<head>
		<meta charset="utf-8">
		
		<!-- Use the .htaccess and remove these lines to avoid edge case issues.
		More info: h5bp.com/b/378 -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
		<title><?php if($s == "single") echo utf8_decode($row_post->idea) . " en ";?>10000ideas - ¿Tienes una idea para tu ciudad?</title>
		<meta name="description" content="¿Cómo hacer nuestra ciudad un lugar mejor para vivir ?
		Todos tenemos buenas ideas, si las compartimos podemos hacerlas realidad. Participá dejando la tuya!">
		<meta name="author" content="Manuel Portela">
		<meta property="og:title" content="<?php if($s == "single") echo utf8_decode($row_post->idea) . " en ";?>10000ideas - ¿Tenés una idea para tu ciudad?" />
		<meta property="og:description" content="¿Cómo hacer nuestra ciudad un lugar mejor para vivir ?
		Todos tenemos buenas ideas, si las compartimos podemos hacerlas realidad. Participá dejando la tuya!" />
		<meta property="og:image" content="<?php echo HOME_URL; ?>img/Logo_Boceto_2-02.png" />
		<meta property="og:url" content="<?php echo HOME_URL; ?>" />
		<meta property="fb:app_id" content="192235404187723" />
		<meta property="og:site_name" content="10.000 Ideas" />
		
		<!-- Mobile viewport optimized: j.mp/bplateviewport -->
		<meta name="viewport" content="width=device-width,initial-scale=1">
		
		<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
		
		<!-- CSS: implied media=all -->
		<!-- CSS concatenated and minified via ant build script-->
		<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="css/dropkick.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="css/screen.css">
		<!--[if IE]>
	   <link href="css/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
	
	  <![endif]-->
		<!-- end CSS-->
		
		<!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->
		
		<!-- All JavaScript at the bottom, except for Modernizr / Respond.
		Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
		For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
		<script type="text/javascript" charset="utf-8">
			var ideasmap="<?php echo base64_encode($ideasmap); ?>";
			var citymap="<?php echo base64_encode($citymap); ?>";  
			var hasCity;
		</script>
		<script src="js/libs/modernizr-2.0.6.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAAoeCzTyfkx60b1bnNIVt3bBSiTfHuFtDiWMaJo7M6xIExjO3KzhTJSRlEB4CCIexaGzzWE4zR-bvESQ"></script>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="js/libs/base64.js"></script>
		
	
</head>

<body class="<?php echo $s;?>">
	<div id="fb-root"></div>
		<script type="text/javascript" charset="utf-8">
	  window.fbAsyncInit = function() {
	          FB.init({
	            appId      : '192235404187723',
	            status     : true, 
	            cookie     : true,
	            xfbml      : true,
	            oauth      : true,
	          });
			FB.Event.subscribe('auth.login', function () {
			          top.location.ref = "<?php echo HOME_URL; ?>?msg=".urlencode('Recuerda elegir tu ciudad!');
			      });

			FB.Event.subscribe('auth.logout', function () {
			          top.location.href = "<?php echo HOME_URL; ?>logout.php";
			      });
	        };
	        (function(d){
	           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	           js = d.createElement('script'); js.id = id; js.async = true;
	           js.src = "//connect.facebook.net/en_US/all.js";
	           d.getElementsByTagName('head')[0].appendChild(js);
	         }(document));
		</script>
		<header class="body">
		
				<div class="header-body-wrapper">
				
							<div class="logo">
								<a href="<?php echo HOME_URL; ?>">10k ideas</a>
						</div> <!-- End of .logo -->
						<h1 class="typo">¿Tienes una idea para tu ciudad?</h1>
						<nav class="header-nav" >
							<a class="" href="help.php#ayuda">Ayuda</a> -
							<a class="" href="help.php#colaborar">Colaborar</a> -
							<a class="" href="blog/">Blog</a>
						</nav> <!-- End of nav -->
						
						<div class="city-selection">
								<div class="city-selection-wrapper">
										<form action="place" method="get" accept-charset="utf-8">
														<span>Tú estas en</span>
															<select name="ciudad" id="ciudad" size="1">
																<?php foreach($ciudades as $ciudad){ ?>
																	<option value="<?php echo $ciudad['id']; ?>" <?php echo getSelected($ciudad['id'], $city); ?>><?php echo utf8_decode($ciudad['nombre']); ?></option>
																<?php } ?>
																																
															</select>
										
										</form>
								</div> <!-- end of .city-selection-wrapper -->
						</div> <!-- end of .city-selection -->
						
				</div> <!-- End of .header-body-wrapper -->
		
		</header> <!-- End of header.body -->