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
		
		<title>10000ideas - ¿Tenés una idea para tu ciudad ?</title>
		<meta name="description" content="">
		<meta name="author" content="">
		
		<!-- Mobile viewport optimized: j.mp/bplateviewport -->
		<meta name="viewport" content="width=device-width,initial-scale=1">
		
		<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
		
		<!-- CSS: implied media=all -->
		<!-- CSS concatenated and minified via ant build script-->
		<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="css/dropkick.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="css/screen.css">
		
		<!-- end CSS-->
		
		<!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->
		
		<!-- All JavaScript at the bottom, except for Modernizr / Respond.
		Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
		For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->

		<script src="js/libs/modernizr-2.0.6.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAAoeCzTyfkx60b1bnNIVt3bBSiTfHuFtDiWMaJo7M6xIExjO3KzhTJSRlEB4CCIexaGzzWE4zR-bvESQ"></script>

	
	
</head>

<body>
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
			          top.location.ref = "<?php echo HOME_URL; ?>";
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
						<h1 class="typo">¿Tenés una idea para tu ciudad ?</h1>
						<nav class="header-nav" >
							<a class="" href="help.php#ayuda">Ayuda</a> -
							<a class="" href="help.php#colaborar">Colaborar</a> -
							<a class="" href="blog/">Blog</a>
						</nav> <!-- End of nav -->
						
		
						
				</div> <!-- End of .header-body-wrapper -->
		
		</header> <!-- End of header.body -->
		<section class="body">
				
				<div class="section-body-wrapper">
				
						<section class="main">
							
								<article class="ideas">
								
										<div class="post-top">
										
												<p class="post-meta"></p>
												<p class="post-date"></p>
										
										</div> <!-- end of .post-top -->
										
										<div class="post">
										
													<img src="<?php echo HOME_URL.'user_images/ninguno.png' ?>" class="avatar" height="50px" width="50px" />
													
												<div class="post-category"  style="background-image: url(img/cats/<?php echo $categorias[$row_post->categoria]['color'];?>_c.png);"><a href="">	</a></div>
												<h3>	</h3>
										
										</div> <!-- end of .post -->
										
										<div class="post-bottom">
												
															
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
								
								<aside class="sidebar">
								
										<div class="idea-sharing">
												<a href="https://twitter.com/share" class="twitter-share-button" data-lang="es" data-related="10000ideascom" data-hashtags="10000ideas">Twittear</a>
												<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
												 <div class="fb-like" data-href="<?php echo HOME_URL; ?>single.php?post=<?php echo $row_post->id; ?>" data-send="true" data-width="390" data-show-faces="false"></div>
										</div> <!-- end of .idea-sharing -->
								
										
										<h2 class="typo">Categorías</h2>
										
										<ul class="categories">
												
												
																						
										</ul> <!-- End of ul.categories -->
								
								</aside> <!-- end of aside.sidebar -->
						
						</section> <!-- end of section.main -->
						
				
				</div> <!-- End of .section-body-wrapper -->
		
		</section> <!-- End of section.body -->
		
			<footer class="body">

					<div class="footer-body-wrapper">

							<div class="footer-info">

									<p class="copyright">10.000 ideas © <span xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/InteractiveResource" property="dct:title" rel="dct:type">10.000 ideas</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://10.000ideas.com" property="cc:attributionName" rel="cc:attributionURL">Manuchis</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Reconocimiento-NoComercial-CompartirIgual 3.0 Unported License</a>.<br />Creado a partir de la obra en <a xmlns:dct="http://purl.org/dc/terms/" href="http://10.000ideas.com" rel="dct:source">10.000ideas.com</a>. <a href="">Políticas de Privacidad</A> - <a href="">Condiciones de uso</a> - Escribinos a 1@000ideas.com</p>
							</div> <!-- end of .footer-info -->

					</div> <!-- End of .footer-body-wrapper -->

			</footer> <!-- End of footer.body -->


	  <!-- JavaScript at the bottom for fast page loading -->

	  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
	  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>
		<script src="js/libs/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/libs/jquery.dropkick-1.0.0.js" type="text/javascript" charset="utf-8"></script>



	  <!-- scripts concatenated and minified via ant build script-->
	  <script defer src="js/main.js"></script>
	  <!-- end scripts-->


	  <!-- Change UA-XXXXX-X to be your site's ID -->
	  <script>
	    window._gaq = [['_setAccount','UA256094291'],['_trackPageview'],['_trackPageLoadTime']];
	    Modernizr.load({
	      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
	    });
	  </script>


	  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
	       chromium.org/developers/how-tos/chrome-frame-getting-started -->
	  <!--[if lt IE 7 ]>
	    <script src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	  <![endif]-->
</body>
</html>