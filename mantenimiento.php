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
		</head>		
		<body class="mantenimiento">
		
		<section class="body">
				
		
				<div class="section-body-wrapper">
				
					<h2>En mantenimiento. Lo sentimos, vuelva pronto!</h2>
				</div> <!-- End of .section-body-wrapper -->
		
		</section> <!-- End of section.body -->
		
		
		
	<?php include('footer.php');?>
  
</body>
</html>