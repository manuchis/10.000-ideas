$(document).ready(function() {
ideasmap = Base64.decode(ideasmap); 
citymap = Base64.decode(citymap); 
$('.updates-list-filter').buttonset(); //funcion de los filtros
$('.updates-list').hide();
$('.updates-list.active').show();

$('.updates-list-filter input').click(function(){
	$('.updates-list-filter input').attr('checked', false);
	$(this).attr('checked', true);
	var charter = $(this).val();
	$('.updates-list.active').removeClass('active').fadeOut('fast',function(){
		$('#'+charter).fadeIn('slow').addClass('active');
	});
});
	

$('#ciudad').dropkick({
  change: function (value, label) { //dropdown ciudades
	$('#city').val(value);
	var url = "index.php?city="+value; 
	$(location).attr('href',url);
  }
});
$('#category').dropkick({
	width: 150,
  change: function (value, label) { //dropdown del form
	$('#category').val(value);
  }
});
$('#neighborhood').dropkick({
	width: 150,
  change: function (value, label) { //dropdown del form
	$('#neighborhood').val(value);
  }
});
$('.votes-link').click(function(){ //votacion ajax
	var target = $(this).children('.votes');
	var votes = target.text();
	var id = $(this).attr('voteid');
	var usuario = $('#user').val();
	var usban = $('#ub').val();
	if(usban==1){
		alert('Usuario bloqueado, escribenos a 1@000ideas.com');
	}else{
		$.ajax({
			url: "action_vote.php",
			type: "POST",
			data: "action=new_vote&usuario="+usuario+"&votes="+votes+"&id="+id ,
			success: function(data){
				if(data=="650"){
					alert('Ya votaste, solo puedes votar una vez.');
				}else if(data=="651"){
					alert('Tienes que ingresar como usuario para votar.');
				}else if(data=="500"){
						alert('Error desconocido, intenta más tarde.');
				}else{
					var new_votes = parseInt(votes)+1;
					target.text(new_votes);
					alert('Gracias por votar');	
				}
				}
			});
	}
	return false;
});
var initialLocation;
var baires = new google.maps.LatLng(-34.6166667, -58.44999999999999);
var browserSupportFlag =  new Boolean();
var geocoder = new google.maps.Geocoder();
var myOptions = {
    zoom: 12,
	scrollwheel: false,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	mapTypeControl: false,
  }; 
geocoder.geocode({ 'address': citymap}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);}
		});
var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
mapa(map, ideasmap);

if(hasCity= ""){ // si no determinó su ubicación, usa el geolocalization
// Try W3C Geolocation (Preferred)
 if(navigator.geolocation) {
   browserSupportFlag = true;
   navigator.geolocation.getCurrentPosition(function(position) {
     initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
     map.setCenter(initialLocation);
   }, function() {
     handleNoGeolocation(browserSupportFlag);
   });
 // Try Google Gears Geolocation
 } else if (google.gears) {
   browserSupportFlag = true;
   var geo = google.gears.factory.create('beta.geolocation');
   geo.getCurrentPosition(function(position) {
     initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
     map.setCenter(initialLocation);
   }, function() {
     handleNoGeoLocation(browserSupportFlag);
   });
 // Browser doesn't support Geolocation
 } else {
   browserSupportFlag = false;
   handleNoGeolocation(browserSupportFlag);
 }
}
}); // cierra document.ready

function mapa(map, ideasmap){

	var ideas = ideasmap.split(';');
	var ideasL = ideas.length - 1;
	for(var i = 0; i < ideasL; i++) { 
		var laidea = ideas[i].split(',');
		if(laidea[4]=="Todos"){
			var direccion = laidea[3]+', '+laidea[5]+', '+laidea[6];
			
		}else{
			var direccion = laidea[3]+', '+laidea[4]+', '+laidea[5]+', '+laidea[6];
		}
	    var geocoder = new google.maps.Geocoder();
	    var geoOptions = {
	      address: direccion,
	      //bounds: getBounds(),
	      region: "NO"
	    };
	    geocoder.geocode(geoOptions, geoCallback(map, laidea, ideasL));
	  }
}

function geoCallback(map, laidea, ideasL) {
   return function(results, status) {
     if (status == google.maps.GeocoderStatus.OK) {
       addMarker(map, laidea, results[0].geometry.location);	
		if(ideasL==1){ // si hay una sola idea posicionar el mapa en esa idea
			map.setCenter(results[0].geometry.location);
		}
     } else {
       console.log("Geocode failed " + status);
     }
   };
 }
function addMarker(map, laidea, location) {
 // console.log("Setting marker for " + laidea[3]+","+laidea[4]+","+laidea[5]+","+laidea[6]  + " (location: " + location + ")");
  var marker = new google.maps.Marker({ map : map, position : location, icon: 'img/cats/'+laidea[7]+'_m.png'});
  marker.setTitle(laidea[1]);
  var infowindow = new google.maps.InfoWindow( {
    content : "<div class='infowindow_class'><strong>"+laidea[1]+"</strong>En "+laidea[4]+" <a href=''>Ver idea ></a></div>",
    size : new google.maps.Size(100, 300)
  });
  new google.maps.event.addListener(marker, "click", function() {
    infowindow.open(map, marker);
  });
}

 
 function handleNoGeolocation(errorFlag) {
   if (errorFlag == true) {
     alert("El servicio de Geolocalización ha fallado, seleccioná la ciudad donde vivís.");
     initialLocation = baires;
   } else {
     alert("Tu navegador no soporta el servicio de geolocalización. Por el momento te ubicamos en Buenos Aires, pero puedes elegir tu ciudad desde el selector.");
     initialLocation = baires;
   }
   map.setCenter(initialLocation);
 }
