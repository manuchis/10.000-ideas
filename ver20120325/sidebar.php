	<aside class="sidebar">
	
<?php if($s=="main"){ ?>	
		<div class="brief">
				¿Cómo hacer nuestra ciudad un lugar mejor para vivir? Todos tenemos buenas ideas, si las compartimos podemos hacerlas realidad. Participá dejando la tuya! <a href="help.php#ayuda">Enterate ></a>
		</div> <!-- end of .brief -->
		<h2 class="typo">Buscar ideas</h2> 
		
			<form class="right rounded" id="search" name="search" action="<?php echo $this_page; ?>" method="get">
				<input type="hidden" name="city" value="<?php echo $city; ?>" />
				<input class="rounded required" type="text" id="q" name="q" value="<?php echo $q; ?>" tabindex="21"> 
				<input class="search" type="submit" tabindex="22" />
			</form>
			
<?php }elseif($s=="single"){ ?>
	<div class="idea-sharing">
					<a href="https://twitter.com/share" class="twitter-share-button" data-lang="es" data-related="10000ideascom" data-hashtags="10000ideas">Twittear</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					 <div class="fb-like" data-href="<?php echo HOME_URL; ?>single.php?post=<?php echo $row_post->id; ?>" data-send="true" data-width="390" data-show-faces="false"></div>
			</div> <!-- end of .idea-sharing -->
	
			<div class="idea-mapped" id="map_canvas">
					
			</div> <!-- end of .idea-mapped -->
	
<?php }  // end if $s 
?>	
		
<!-- start of common sidebar -->
			<h2 class="typo">Categorías</h2>
			
			<ul class="categories">
					
						<?php foreach($categorias as $cat){ ?>
						<li>
								<a class="" href="index.php?cat=<?php echo $cat['id']; if($city){ echo "&city=".$city; } ?>" style="background-image: url(img/cats/<?php echo $cat['color']; ?>_s.png);"><?php echo utf8_decode($cat['nombre']); ?></a>
						</li>
						<?php } ?>
															
			</ul> <!-- End of ul.categories -->
			<h2 class="typo">Inspiración</h2>
			<script type="text/javascript" src="http://intervalos.tumblr.com/js"></script>
			<p><small>Noticias vía <a href="http://intervalos.intangiblesurbanos.com.ar/">Intervalos:IntangiblesUrbanos</a></small></p>
			
<!-- end of common sidebar -->


<?php if($s=="help"){ ?>
		<h2 class="typo">Apoyos</h2>
		<ul>
			<li><a href="http://urbz.net/">Urbz</a></li>
			<li><a href="http://www.plataformaurbana.cl/">Plataforma Urbana</a></li>
			<li><a href="http://intangiblesurbanos.com.ar/">Intangibles Urbanos</a></li>
			<li><a href="http://gravedad.cl/">Gravedad</a></li>
			
		</ul>
		<p><small>Más <a href="colab.php">colaboraciones</a></small></p>
<?php }  // end if $s 
 ?>		

	<h2 class="typo">Twitter</h2>

<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 5,
  interval: 30000,
  width: 310,
  height: 300,
  theme: {
    shell: {
      background: '#ececec',
      color: '#333333'
    },
    tweets: {
      background: '#fbfbfb',
      color: '#636363',
      links: '#0087d5'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    behavior: 'all'
  }
}).render().setUser('10000ideascom').start();
</script>
			
	</aside> <!-- end of aside.sidebar -->