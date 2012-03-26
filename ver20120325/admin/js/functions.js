/*=======================================
=======================================*/

//check all the checkboxes

function CheckAll()
{
   for (var i=0; i < document.list.elements.length; i++)
   {
      var e = document.list.elements[i];
      if (e.name != "allbox")
         e.checked = document.list.allbox.checked;
   }
}

//set external image

function setImage(image, ext, id)
{
	var img = '<img class="loaded" src="../media/images/' + image +'_m.'+ ext + '" />';
	self.parent.document.data.image.value = id;
	self.parent.document.getElementById('imgholder').innerHTML = '';
	self.parent.document.getElementById('imgholder').innerHTML = img;
	//self.parent.tb_remove();
	return false;
}

function removeImage()
{
	var img = '<img class="hidder" src="images/grey.jpg" />';
	self.parent.document.data.image.value = '';
	self.parent.document.getElementById('imgholder').innerHTML = '';
	self.parent.document.getElementById('imgholder').innerHTML = img;
	//self.parent.tb_remove();
	return false;
}