tinyMCE.init({
	//general options
	mode : "exact",
	theme : "advanced",
	skin : "o2k7",
	skin_variant : "silver",
	plugins : "media,paste",
	elements : "contents, comment",
	height : "450",
	//theme options
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	theme_advanced_buttons1 : "bold,italic,underline,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,image,media,separator,undo,redo,separator,code,cleanup,pasteword",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	tab_focus : ":prev,:next"
});