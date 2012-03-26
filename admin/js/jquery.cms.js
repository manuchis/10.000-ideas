$(document).ready(ini);

function ini(){
	
	/*-=-=-= table sorter =-=-=-*/
	
	$.tablesorter.defaults.widgets = ['zebra'];
	
	$('#sorter').tablesorter({
		headers: { 0: { sorter: false }}
	});
	
	$('#cat-sorter').tablesorter({
		headers: { 0: { sorter: false }},
		sortList: [[1,0]] 
	});
	
	/*-=-=-= confirm button =-=-=-*/
	
	$("#submit-list").click( function() {
		jConfirm('Desea borrar los elementos seleccionados?', 'Confirmacion', function(r) {
			if(r){
				$("form#list").submit();
			}
		});
	});
	
	/*-=-=-= the error fields =-=-=-*/
	
	$("form .required").attr("title"," * Campo requerido!");
	
	$("form#list .required").attr("title"," debes seleccionar una!");
	
	$("form#search .required").attr("title","Ingresa algo para buscar!");
	
	/*-=-=-= search form validate =-=-=-*/
	
	$("form#search").validate({
		rules: {
			q:{
				required:true,
				minLength:3
			}
		},
		messages: {
			q: ""
		}
	});
	
	/*-=-=-= list form validation =-=-=-*/
	
	$("form#list").validate({
		errorPlacement: function(error, element) {
			error.appendTo();
		}
	});
	
	/*-=-=-= data form validation =-=-=-*/
	
	$("form#data").validate({
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("li").prev("li") );
		}
	});
	
	/*-=-=-= admin form validation =-=-=-*/
	
	$("form#admin").validate({
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("li").prev("li") );
		}
	});
	
	/*-=-=-= user form validation =-=-=-*/
	
	$("form#user").validate({
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("li").prev("li") );
		}
	});
	
	/*-=-=-= date picker =-=-=-*/
	
	$('.date').datepicker({  
		dateFormat: 'yy-mm-dd'
	});
	
	/*-=-=-= file style =-=-=-*/
	
	$("input[type=file]").filestyle({ 
		image: "images/choose-file.png",
	    imageheight : 22,
	    imagewidth : 82,
	    width : 390
 	});
 	
 	 /*-=-=-= hide loading animation =-=-=-*/
	
	$("img.loader").hide();
	
	/*-=-=-= ajax =-=-=-*/
	
	 $.ajax({
	 	error: function(){
	 		alert('Ocurrio un error!')
	 	}
	 });
	
}

function ajaxForm(page, action, id)
{
	var loader = 'img#loader' + id;
	var post = 'tr#post-' + id;
	
	$(loader).show();
	
	$.post(page, { action: action, id: id },
 	function(data){
    	if(data == 'ok'){
    		$(loader).hide();
    		$(post).fadeOut("slow");
    	}else if(data == 'deleted'){
    		$(loader).hide();
    		$(post).fadeOut("slow");
    	}else if(data == 'error'){
    		$(loader).hide();
    		alert('Ocurrio un error, intenta mas tarde');
    	}
  	});
}